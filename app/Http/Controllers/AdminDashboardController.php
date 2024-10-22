<?php

namespace App\Http\Controllers;

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

        return view('admin.dashboard', compact('affiliates'));
    }
}
