<?php

namespace App\Http\Controllers;

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

        // 必須フィールドを取得
        $affiliateRef = $request->input('affiliate_ref');
        $action = $request->input('action');
        $productName = $request->input('product_name');
        $timestamp = $request->input('timestamp');

        // 必須データのバリデーション
        if (!$affiliateRef || !$action || !$productName) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // アフィリエイトリンクを取得
        $affiliateLink = AffiliateLink::where('token', $affiliateRef)->first();

        if (!$affiliateLink) {
            return response()->json(['error' => 'Affiliate link not found'], 404);
        }

        // アフィリエイター（紹介者）を取得
        $referrer = $affiliateLink->user;

        if (!$referrer) {
            return response()->json(['error' => 'Referrer not found'], 404);
        }

        // 商材を特定
        $product = Product::where('name', $productName)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // アフィリエイタータイプに基づく報酬を取得
        $commissionData = $product->commissions()->where('affiliate_type_id', $referrer->affiliate_type_id)->first();

        if (!$commissionData) {
            return response()->json(['error' => 'Commission data not found'], 404);
        }

        $commission = $commissionData->fixed_commission; // 商材登録時に設定された固定報酬

        // 資料請求データをaffiliate_commissionsテーブルに記録
        AffiliateCommission::create([
            'user_id' => $referrer->id, // 紹介者のユーザーID
            'affiliate_link_id' => $affiliateLink->id, // アフィリエイトリンクID
            'amount' => $commission, // 商材登録時に設定された報酬額
            'product_name' => $productName, // 資料請求の商品名
            'session_id' => null, // セッションIDは不要
            'is_paid' => 0, // 支払い対象外
            'paid_at' => null, // 支払い日時なし
            'created_at' => $timestamp, // 資料請求のタイムスタンプ
        ]);

        return response()->json(['status' => 'success'], 200);
    }
}
