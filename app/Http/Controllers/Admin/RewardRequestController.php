<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardRequest;

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
}
