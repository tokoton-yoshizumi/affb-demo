<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Product;

class StripeCheckoutController extends Controller
{
    public function createCheckoutSession(Request $request, $productId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // 商材を取得
        $product = Product::find($productId);

        if (!$product || !$product->price_id) {
            \Log::error('Product not found or missing price_id', ['product_id' => $productId]);
            return response()->json(['error' => 'Product not found or price_id is missing'], 404);
        }

        try {
            // Stripe Checkoutセッションを作成
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $product->price_id, // Stripeの価格ID
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
                'metadata' => [
                    'affiliate_ref' => $request->input('affiliate_ref'), // アフィリエイトトークン
                    'product_id' => $product->id,
                ],
            ]);

            // セッションURLをログに記録
            \Log::info('Stripe Checkout Session created successfully', ['url' => $session->url]);

            // 直接リダイレクト
            return redirect($session->url);
        } catch (\Exception $e) {
            // エラーログを記録
            \Log::error('Stripe Checkout Session creation failed', [
                'error_message' => $e->getMessage(),
                'product_id' => $productId,
            ]);

            // エラーメッセージを返す
            return response()->json(['error' => 'Failed to create checkout session'], 500);
        }
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
