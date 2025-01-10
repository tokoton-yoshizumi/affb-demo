<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripeCheckoutController extends Controller
{
    public function createCheckoutSession(Request $request, $productId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // 商材を取得
        $product = Product::find($productId);

        if (!$product || !$product->price_id) {
            Log::error('Product not found or missing price_id', ['product_id' => $productId]);
            return response()->json(['error' => 'Product not found or price_id is missing'], 404);
        }

        try {
            // Stripe Checkoutセッションを作成
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $product->price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
                'metadata' => [
                    'affiliate_ref' => $request->input('affiliate_ref'),
                    'product_id' => $product->id,
                ],
            ]);

            // URLをログに記録
            Log::info('Stripe Checkout Session created successfully', ['url' => $session->url]);

            // Checkout URLに直接リダイレクト
            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Session creation failed', [
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
