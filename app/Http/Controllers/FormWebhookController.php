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
        Log::info('Webhook Request:', $request->all()); // ログにリクエスト内容を記録

        // CF7のフィールド名に合わせてデータを取得
        $name = $request->input('full-name'); // CF7のnameフィールド
        $email = $request->input('email'); // メールアドレス
        $affiliateRef = $request->input('affiliate_ref'); // アフィリエイトコード
        $productId = $request->input('product_id'); // 商品ID

        // 不要なフィールド（phone, address）は削除

        // 必須項目チェック
        if (!$name || !$email) {
            Log::error('Invalid customer data', [
                'name' => $name,
                'email' => $email,
            ]);
            return response()->json(['error' => 'Invalid customer data'], 400);
        }

        // 顧客情報の登録・更新（phone, addressは省略）
        $customer = Customer::firstOrCreate([
            'email' => $email, // メールアドレスで顧客の一意性を判断
        ], [
            'name' => $name,
        ]);

        // フォームデータを保存（その他のデータを記録）
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
            'action' => $request->input('action'),
            'timestamp' => now(),
            'other_data' => json_encode($otherData), // JSON形式で保存
        ]);

        Log::info('Customer created or found successfully', ['customer_id' => $customer->id]);

        // 必須フィールドを取得
        $action = $request->input('action');
        $timestamp = now(); // Laravelの現在のタイムスタンプを使用

        // 必須データのバリデーション
        if (!$affiliateRef || !$action || !$productId) {
            Log::warning('Commission was NOT recorded due to missing required fields', [
                'email' => $email,
                'affiliate_ref' => $affiliateRef,
                'action' => $action,
                'product_id' => $productId,
            ]);
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // アフィリエイトリンクを取得
        $affiliateLink = AffiliateLink::where('token', $affiliateRef)->first();

        if (!$affiliateLink) {
            Log::warning('Commission was NOT recorded because affiliate link not found', [
                'email' => $email,
                'affiliate_ref' => $affiliateRef,
            ]);
            return response()->json(['error' => 'Affiliate link not found'], 404);
        }

        // アフィリエイター（紹介者）を取得
        $referrer = $affiliateLink->user;

        if (!$referrer) {
            Log::warning('Commission was NOT recorded because referrer not found', [
                'email' => $email,
                'affiliate_link_id' => $affiliateLink->id,
            ]);
            return response()->json(['error' => 'Referrer not found'], 404);
        }

        // 商材を特定
        $product = Product::find($productId);

        if (!$product) {
            Log::warning('Commission was NOT recorded because product not found', [
                'email' => $email,
                'product_id' => $productId,
            ]);
            return response()->json(['error' => 'Product not found'], 404);
        }

        // アフィリエイタータイプに基づく報酬を取得
        $commissionData = $product->commissions()
            ->where('affiliate_type_id', $referrer->affiliate_type_id)
            ->first();

        if (!$commissionData) {
            Log::warning('Commission was NOT recorded because commission data not found', [
                'email' => $email,
                'product_id' => $productId,
                'affiliate_type_id' => $referrer->affiliate_type_id,
            ]);
            return response()->json(['error' => 'Commission data not found'], 404);
        }


        $commission = $commissionData->fixed_commission_on_form ?? $commissionData->fixed_commission;
        // 商材登録時に設定された固定報酬

        // 資料請求データをaffiliate_commissionsテーブルに記録
        AffiliateCommission::create([
            'user_id' => $referrer->id, // 紹介者のユーザーID
            'affiliate_link_id' => $affiliateLink->id, // アフィリエイトリンクID
            'customer_id' => $customer->id,
            'amount' => $commission, // 商材登録時に設定された報酬額

            'product_name' => $product->name, // 資料請求の商品名
            'session_id' => null, // セッションIDは不要
            'is_paid' => 0, // 支払い対象外
            'paid_at' => null, // 支払い日時なし
            'status' => '確定',
            'created_at' => $timestamp, // 資料請求のタイムスタンプ
            'reward_type' => 'form',
        ]);

        Log::info('Affiliate commission recorded successfully', [
            'user_id' => $referrer->id,
            'affiliate_link_id' => $affiliateLink->id,
            'amount' => $commission,
            'product_id' => $productId,
        ]);

        return response()->json([
            'status' => 'mail_sent',
            'message' => 'Form submitted successfully',
        ], 200);
    }
}
