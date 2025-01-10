<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\AffiliateLink;
use App\Mail\CommissionEarned;
use App\Models\AffiliateCommission; // メールファサードをインポート
use Illuminate\Support\Facades\Mail; // 作成したCommissionEarnedメールクラスをインポート

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // セッションIDを文字列として取得
            $sessionId = (string) $session->id;

            // メタデータから商品名を取得
            $productName = $session->metadata->product_name ?? '商品名不明';

            // すでにこのセッションIDで報酬が記録されていないか確認
            $existingCommission = AffiliateCommission::where('session_id', $sessionId)->first();

            if (!$existingCommission) {
                // ref をセッションから取得し、アフィリエイトリンクと紐付ける
                $ref = $session->client_reference_id;
                $affiliateLink = AffiliateLink::where('token', $ref)->first();

                if ($affiliateLink) {
                    $referrer = $affiliateLink->user;

                    if ($referrer) {
                        // 商品に紐付いたアフィリエイトリンクのproduct_idを取得
                        $product = $affiliateLink->product;

                        if ($product) {
                            // ユーザーのaffiliate_type_idに基づいて報酬を取得
                            $commissionData = $product->commissions()->where('affiliate_type_id', $referrer->affiliate_type_id)->first();

                            if ($commissionData) {
                                // 固定報酬が設定されている場合はそれを使用
                                if ($commissionData->fixed_commission) {
                                    $commission = $commissionData->fixed_commission;
                                } else {
                                    // 固定報酬が設定されていない場合は％で計算
                                    $commissionRate = $commissionData->commission_rate / 100;
                                    $commission = 16800 * $commissionRate; // 例として16,800円に対する報酬を計算
                                }

                                // アフィリエイト報酬を記録
                                $newCommission = AffiliateCommission::create([
                                    'user_id' => $referrer->id,
                                    'affiliate_link_id' => $affiliateLink->id,
                                    'amount' => $commission,
                                    'session_id' => $sessionId,
                                    'product_name' => $productName, // 商品名を保存
                                ]);

                                // コミッションが記録されたユーザーにメールを送信
                                Mail::to($referrer->email)->send(new CommissionEarned($referrer, $commission));
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function handleStripeWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // メタデータから必要な情報を取得
            $affiliateRef = $session->metadata->affiliate_ref;
            $productId = $session->metadata->product_id;

            // 商材とアフィリエイトリンクを特定
            $product = Product::find($productId);
            $affiliateLink = AffiliateLink::where('token', $affiliateRef)->first();

            if ($product && $affiliateLink) {
                $referrer = $affiliateLink->user;

                // 報酬を計算
                $commission = $product->fixed_commission ?? ($product->price * $product->commission_rate / 100);

                // 報酬を記録
                AffiliateCommission::create([
                    'user_id' => $referrer->id,
                    'affiliate_link_id' => $affiliateLink->id,
                    'amount' => $commission,
                    'product_name' => $product->name,
                    'session_id' => $session->id,
                    'is_paid' => 0,
                ]);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
