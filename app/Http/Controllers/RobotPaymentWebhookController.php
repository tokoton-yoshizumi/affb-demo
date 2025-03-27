<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\Product;
use App\Models\AffiliateLink;
use App\Models\AffiliateCommission;
use App\Models\CustomerSubmission;

class RobotPaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('[RobotPaymentWebhook] 受信データ:', $request->all());

        $email = $request->input('em');

        if (!$email) {
            Log::warning('[Webhook] メールアドレスがありません');
            return response('NG', 200)->header('Content-Type', 'text/html');
        }

        // 顧客特定
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            Log::warning("[Webhook] 顧客が見つかりません: {$email}");
            return response('NG', 200)->header('Content-Type', 'text/html');
        }

        // 直近のフォーム送信を取得
        $submission = CustomerSubmission::where('customer_id', $customer->id)
            ->latest()
            ->first();

        if (!$submission || !$submission->affiliate_ref || !$submission->product_id) {
            Log::warning("[Webhook] 有効なフォーム送信情報が見つかりません");
            return response('NG', 200)->header('Content-Type', 'text/html');
        }

        // アフィリエイトリンク・紹介者・商品を取得
        $affiliateLink = AffiliateLink::where('token', $submission->affiliate_ref)->first();
        $referrer = $affiliateLink?->user;
        $product = Product::find($submission->product_id);

        if (!$referrer || !$product) {
            Log::warning("[Webhook] 紹介者または商品が見つかりません");
            return response('NG', 200)->header('Content-Type', 'text/html');
        }

        // 決済時報酬の取得
        $commissionData = $product->commissions()
            ->where('affiliate_type_id', $referrer->affiliate_type_id)
            ->first();

        if (!$commissionData) {
            Log::warning("[Webhook] 決済時の報酬データが見つかりません");
            return response('NG', 200)->header('Content-Type', 'text/html');
        }

        $paymentCommission = $commissionData->fixed_commission_on_payment ?? $commissionData->fixed_commission;

        // 同じ顧客・アフィリエイトリンク・商品で既に「決済成果」が登録されていないか確認
        $alreadyExists = AffiliateCommission::where('customer_id', $customer->id)
            ->where('affiliate_link_id', $affiliateLink->id)
            ->where('product_name', $product->name)
            ->where('status', '確定')
            ->exists();

        if ($alreadyExists) {
            Log::info("[Webhook] 決済報酬はすでに登録済みです");
            return response('OK', 200)->header('Content-Type', 'text/html');
        }

        // 決済成果を登録
        AffiliateCommission::create([
            'user_id' => $referrer->id,
            'affiliate_link_id' => $affiliateLink->id,
            'customer_id' => $customer->id,
            'amount' => $paymentCommission,
            'product_name' => $product->name,
            'session_id' => null,
            'is_paid' => 0,
            'paid_at' => null,
            'status' => '確定',
        ]);

        Log::info('[Webhook] 決済報酬を登録しました', [
            'email' => $email,
            'amount' => $paymentCommission,
            'user_id' => $referrer->id,
        ]);

        return response('OK', 200)->header('Content-Type', 'text/html');
    }
}
