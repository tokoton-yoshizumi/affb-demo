<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // セッションからアフィリエイトトークンを取得
        $affiliateRef = session('affiliate_ref', null);

        // サブスクリプションのための支払いセッションを作成
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => 'price_1PncBDFfSkuB7bpdIo7CKNMI', // Stripeダッシュボードで作成したPrice IDを使用
                'quantity' => 1,
            ]],
            'mode' => 'subscription', // サブスクリプションモードに設定
            'success_url' => route('checkout.success'), // 決済成功時のリダイレクトURL
            'cancel_url' => route('checkout.cancel'),   // キャンセル時のリダイレクトURL
            'client_reference_id' => $affiliateRef, // アフィリエイトリンクのトークンを渡す（セッションから取得）
            'metadata' => [
                'product_name' => 'WordPressテーマZEN', // 商品名をメタデータとして追加
            ],
        ]);

        return redirect($session->url);
    }


    public function success()
    {
        return view('checkout.success');
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }
}
