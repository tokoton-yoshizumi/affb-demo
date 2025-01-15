<?php

namespace App\Http\Controllers;

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

        // 顧客情報の取得（リクエスト内のデータを使用）
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $address = $request->input('address');

        // 顧客情報のバリデーション
        if (!$name || !$email) {
            Log::error('Invalid customer data', [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
            ]);
            return response()->json(['error' => 'Invalid customer data'], 400);
        }

        // 顧客情報が既に存在するか確認
        $customer = Customer::firstOrCreate([
            'email' => $email, // 顧客情報はメールアドレスで重複チェック
        ], [
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
        ]);

        Log::info('Customer created or found successfully', ['customer_id' => $customer->id]);

        // 必須フィールドを取得
        $affiliateRef = $request->input('affiliate_ref');
        $action = $request->input('action');
        $productId = $request->input('product_id');
        $timestamp = now(); // Laravelの現在のタイムスタンプを使用

        // 必須データのバリデーション
        if (!$affiliateRef || !$action || !$productId) {
            Log::error('Invalid request data', [
                'affiliate_ref' => $affiliateRef,
                'action' => $action,
                'product_id' => $productId,
                'timestamp' => $timestamp,
            ]);
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // アフィリエイトリンクを取得
        $affiliateLink = AffiliateLink::where('token', $affiliateRef)->first();

        if (!$affiliateLink) {
            Log::error('Affiliate link not found', ['affiliate_ref' => $affiliateRef]);
            return response()->json(['error' => 'Affiliate link not found'], 404);
        }

        // アフィリエイター（紹介者）を取得
        $referrer = $affiliateLink->user;

        if (!$referrer) {
            Log::error('Referrer not found', ['affiliate_link_id' => $affiliateLink->id]);
            return response()->json(['error' => 'Referrer not found'], 404);
        }

        // 商材を特定
        $product = Product::find($productId);

        if (!$product) {
            Log::error('Product not found', ['product_id' => $productId]);
            return response()->json(['error' => 'Product not found'], 404);
        }

        // アフィリエイタータイプに基づく報酬を取得
        $commissionData = $product->commissions()
            ->where('affiliate_type_id', $referrer->affiliate_type_id)
            ->first();

        if (!$commissionData) {
            Log::error('Commission data not found', [
                'product_id' => $productId,
                'affiliate_type_id' => $referrer->affiliate_type_id,
            ]);
            return response()->json(['error' => 'Commission data not found'], 404);
        }

        $commission = $commissionData->fixed_commission; // 商材登録時に設定された固定報酬

        // 資料請求データをaffiliate_commissionsテーブルに記録
        AffiliateCommission::create([
            'user_id' => $referrer->id, // 紹介者のユーザーID
            'affiliate_link_id' => $affiliateLink->id, // アフィリエイトリンクID
            'amount' => $commission, // 商材登録時に設定された報酬額
            'product_name' => $product->name, // 資料請求の商品名
            'session_id' => null, // セッションIDは不要
            'is_paid' => 0, // 支払い対象外
            'paid_at' => null, // 支払い日時なし
            'created_at' => $timestamp, // 資料請求のタイムスタンプ
        ]);

        Log::info('Affiliate commission recorded successfully', [
            'user_id' => $referrer->id,
            'affiliate_link_id' => $affiliateLink->id,
            'amount' => $commission,
            'product_id' => $productId,
        ]);

        return response()->json(['status' => 'success'], 200);
    }
}
