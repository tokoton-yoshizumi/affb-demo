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
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // 商材を取得
        $product = Product::find($productId);

        if (!$product || !$product->price_id) {
            return response()->json(['error' => 'Product not found or price_id is missing'], 404);
        }

        // Checkoutセッションを作成
        $session = Session::create([
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
