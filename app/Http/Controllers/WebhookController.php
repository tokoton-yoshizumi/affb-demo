<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
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
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            // Stripe Webhookの署名を検証
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );

            // イベントタイプが `checkout.session.completed` の場合
            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;

                // メタデータから情報を取得
                $affiliateRef = $session->metadata->affiliate_ref ?? null;
                $productId = $session->metadata->product_id ?? null;

                if ($affiliateRef && $productId) {
                    $affiliateLink = AffiliateLink::where('token', $affiliateRef)->first();
                    $product = Product::find($productId);

                    if ($affiliateLink && $product) {
                        // アフィリエイタータイプに基づく報酬を取得
                        $commissionAmount = $product->commissions()
                            ->where('affiliate_type_id', $affiliateLink->user->affiliate_type_id)
                            ->value('fixed_commission');

                        if ($commissionAmount) {
                            // アフィリエイト報酬を記録
                            AffiliateCommission::create([
                                'user_id' => $affiliateLink->user_id,
                                'affiliate_link_id' => $affiliateLink->id,
                                'amount' => $commissionAmount,
                                'product_name' => $product->name,
                                'session_id' => $session->id,
                                'is_paid' => false,
                            ]);

                            Log::info('Affiliate commission recorded', [
                                'user_id' => $affiliateLink->user_id,
                                'product_id' => $productId,
                                'amount' => $commissionAmount,
                            ]);
                        } else {
                            Log::error('No commission amount found for product', ['product_id' => $productId]);
                        }
                    } else {
                        Log::error('Affiliate link or product not found', [
                            'affiliate_ref' => $affiliateRef,
                            'product_id' => $productId,
                        ]);
                    }
                } else {
                    Log::error('Metadata is missing from the session', [
                        'session_id' => $session->id,
                    ]);
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
}
