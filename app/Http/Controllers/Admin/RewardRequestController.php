<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\RewardRequest;
use App\Models\AffiliateCommission;
use App\Http\Controllers\Controller;

class RewardRequestController extends Controller
{
    /**
     * 振込申請一覧の表示
     */
    public function index()
    {
        // 全ての振込申請を取得
        $rewardRequests = RewardRequest::orderBy('created_at', 'desc')->get();

        // ビューにデータを渡して表示
        return view('admin.reward-requests.index', compact('rewardRequests'));
    }

    public function update(Request $request, $id)
    {
        $rewardRequest = RewardRequest::findOrFail($id);

        // ステータスを更新
        $rewardRequest->status = $request->status;
        $rewardRequest->save();

        // ステータスが「支払済」の場合、affiliate_commissions テーブルを更新
        if ($request->status === 'approved') {
            // ここで関連する affiliate_commissions をすべて対象
            $affiliateCommissions = AffiliateCommission::where('user_id', $rewardRequest->user_id)
                ->get(); // すべての報酬を対象に更新する

            foreach ($affiliateCommissions as $commission) {
                // 支払済の日時を更新
                $commission->paid_at = now();
                $commission->is_paid = 1; // 支払済フラグを1に設定
                $commission->save();
            }
        }

        return redirect()->route('admin.reward-requests.index')->with('success', 'ステータスが更新されました。');
    }
}
