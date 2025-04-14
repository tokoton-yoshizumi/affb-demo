<?php

namespace App\Http\Controllers;

use App\Models\CustomerSubmission;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\AffiliateLink;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\Log;

class FormWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Webhook Request:', $request->all());

        // 必要なフィールドを最初に取得（順番重要）
        $name = $request->input('full-name');
        $email = $request->input('email');
        $affiliateRef = $request->input('affiliate_ref');
        $productId = $request->input('product_id');
        $action = $request->input('action');
        $timestamp = now();

        // アクティブなアフィリエイトリンクを先にチェック
        $affiliateLink = AffiliateLink::where('token', $affiliateRef)
            ->where('is_active', true)
            ->first();

        if (!$affiliateLink) {
            Log::warning('Commission was NOT recorded because affiliate link is inactive or not found', [
                'email' => $email,
                'affiliate_ref' => $affiliateRef,
            ]);
            return response()->json(['error' => 'Inactive or missing affiliate link'], 404);
        }

        // 以下、必要項目のバリデーション
        if (!$name || !$email || !$productId || !$action) {
            Log::error('Invalid request data', compact('name', 'email', 'productId', 'action'));
            return response()->json(['error' => 'Invalid customer data'], 400);
        }

        // 顧客登録（有効リンクがある場合のみ）
        $customer = Customer::firstOrCreate(
            ['email' => $email],
            ['name' => $name]
        );

        // フォーム送信記録
        $otherData = $request->except([
            'full-name',
            'email',
            'affiliate_ref',
            'product_id',
            'action',
            'timestamp'
        ]);

        CustomerSubmission::create([
            'customer_id' => $customer->id,
            'product_id' => $productId,
            'affiliate_ref' => $affiliateRef,
            'action' => $action,
            'timestamp' => $timestamp,
            'other_data' => json_encode($otherData),
        ]);

        Log::info('Customer created or found successfully', ['customer_id' => $customer->id]);

        // アフィリエイターと商材を取得
        $referrer = $affiliateLink->user;
        $product = Product::find($productId);

        if (!$referrer || !$product) {
            Log::warning('Missing referrer or product', compact('referrer', 'product'));
            return response()->json(['error' => 'Missing referrer or product'], 404);
        }

        $commissionData = $product->commissions()
            ->where('affiliate_type_id', $referrer->affiliate_type_id)
            ->first();

        if (!$commissionData) {
            Log::warning('No commission data found', [
                'email' => $email,
                'product_id' => $productId,
                'affiliate_type_id' => $referrer->affiliate_type_id,
            ]);
            return response()->json(['error' => 'Commission data not found'], 404);
        }

        $commission = $commissionData->fixed_commission_on_form ?? $commissionData->fixed_commission;

        AffiliateCommission::create([
            'user_id' => $referrer->id,
            'affiliate_link_id' => $affiliateLink->id,
            'customer_id' => $customer->id,
            'amount' => $commission,
            'product_name' => $product->name,
            'session_id' => null,
            'is_paid' => 0,
            'paid_at' => null,
            'status' => '確定',
            'created_at' => $timestamp,
            'reward_type' => 'form',
        ]);

        Log::info('Affiliate commission recorded successfully', [
            'user_id' => $referrer->id,
            'amount' => $commission,
            'product_id' => $productId,
        ]);

        return response()->json([
            'status' => 'mail_sent',
            'message' => 'Form submitted successfully',
        ]);
    }
}
