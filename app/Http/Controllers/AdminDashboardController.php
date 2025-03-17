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

        // 最近1週間のアフィリエイト報酬を取得し、新しいものを上に並べる
        $recentCommissions = AffiliateCommission::with('user')
            ->where('created_at', '>=', Carbon::now()->subWeek()) // 1週間以内のデータを取得
            ->orderBy('created_at', 'desc') // 新しい順に並び替え
            ->get();

        return view('admin.dashboard', compact('affiliates', 'recentCommissions'));
    }
}
