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
        Log::info('Request received:', $request->all());

        $affiliateRef = $request->input('affiliate_ref', null);
        Log::info('Affiliate ref:', ['affiliate_ref' => $affiliateRef]);

        if (!$affiliateRef) {
            return response()->json(['error' => 'Affiliate ref is missing'], 400);
        }

        $product = Product::find($productId);
        if (!$product || !$product->price_id) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        Log::info('Creating Stripe Checkout session for:', ['product_id' => $product->id]);

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
                'affiliate_ref' => $affiliateRef,
                'product_id' => $product->id,
            ],
        ]);

        Log::info('Stripe Checkout Session created:', ['url' => $session->url]);

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
