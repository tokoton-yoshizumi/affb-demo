<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RobotPaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // すべての受信データをログに書き出し
        Log::info('[RobotPaymentWebhook] 受信データ:', $request->all());

        // レスポンスはHTTP 200でOK（ロボットペイメントはレスポンスに依存しません）
        return response()->json(['status' => 'received']);
    }
}
