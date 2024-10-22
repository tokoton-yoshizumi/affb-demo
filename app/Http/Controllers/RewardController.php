<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffiliateCommission;
use App\Models\RewardRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    /**
     * 振込申請フォームの表示
     */
    public function showRequestForm(Request $request)
    {
        $user = Auth::user();

        // 30日が経過した未払い報酬の合計を取得
        $totalCommission = AffiliateCommission::where('user_id', $user->id)
            ->where('created_at', '<=', Carbon::now()->subDays(30))
            ->whereNull('paid_at')  // 支払済みの報酬は除外
            ->sum('amount');

        // 過去の報酬を一覧で表示（支払い状況に応じたリスト）
        $commissions = AffiliateCommission::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // すでに保存されている銀行情報を取得
        $bankDetails = [
            'bank_name' => $user->bank_name,
            'branch_name' => $user->branch_name,
            'account_number' => $user->account_number,
            'account_holder' => $user->account_holder,
            'account_type' => $user->account_type,
        ];

        // ビューにデータを渡す
        return view('reward.request', compact('totalCommission', 'commissions', 'bankDetails'));
    }

    /**
     * 振込申請の確認画面
     */
    public function confirmRequest(Request $request)
    {
        $user = Auth::user();
        $totalCommission = AffiliateCommission::where('user_id', $user->id)
            ->where('created_at', '<=', Carbon::now()->subDays(30))
            ->whereNull('paid_at')  // 未払いの報酬のみ
            ->sum('amount');

        $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'branch_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:20'],
            'account_holder' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'string', 'in:普通預金,当座預金'],  // 口座の種類をバリデート
        ]);

        return view('reward.confirm', [
            'amount' => $totalCommission,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'account_type' => $request->account_type,
        ]);
    }

    /**
     * 振込申請の確定処理
     */
    public function finalizeRequest(Request $request)
    {
        $user = Auth::user();

        // 銀行情報をユーザーに保存
        $user->update([
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'account_type' => $request->account_type,
        ]);

        // 振込申請データを reward_requests テーブルに保存 (銀行情報は不要)
        RewardRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'status' => 'pending',  // ステータスを申請中に設定
        ]);

        // 振込申請処理: ここでは全額支払うため、部分支払いの処理は不要
        AffiliateCommission::where('user_id', $user->id)
            ->where('created_at', '<=', Carbon::now()->subDays(30))
            ->whereNull('paid_at')  // 未払いの報酬のみ
            ->update(['paid_at' => Carbon::now()]);  // 支払済み

        return redirect()->route('reward.request')->with('success', '振込申請が送信されました。');
    }
}
