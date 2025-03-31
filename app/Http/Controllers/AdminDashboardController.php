<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\AffiliateCommission;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // is_adminがfalseのユーザー（アフィリエイター）を取得
        $affiliates = User::where('is_admin', false)
            ->with('commissions') // 各アフィリエイターの報酬も一緒に取得
            ->get();

        // アフィリエイト報酬をページネーションで取得（全件対象）
        $recentCommissions = AffiliateCommission::with(['user', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        return view('admin.dashboard', compact('affiliates', 'recentCommissions'));
    }
}
