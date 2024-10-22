<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Http\Request;
use App\Models\AffiliateLink;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\Mail; // メールファサードをインポート
use App\Mail\CommissionEarned; // 作成したCommissionEarnedメールクラスをインポート

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

                    if ($referrer && $referrer->affiliateProgram) {
                        // 固定報酬が設定されている場合はそれを使用
                        if ($referrer->affiliateProgram->fixed_commission) {
                            $commission = $referrer->affiliateProgram->fixed_commission;
                        } else {
                            // 固定報酬が設定されていない場合は％で計算
                            $commissionRate = $referrer->affiliateProgram->commission_rate / 100;
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

        return response()->json(['status' => 'success'], 200);
    }
}
