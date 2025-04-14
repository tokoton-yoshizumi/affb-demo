<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCommission;
use App\Models\AffiliateLink;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // アフィリエイトリンクを取得
        $affiliate_links = AffiliateLink::where('user_id', $user->id)->get();

        $commissions = AffiliateCommission::where('user_id', $user->id)
            ->with('customer') // ← これを追加
            ->orderBy('created_at', 'desc')
            ->get();



        $rewardDescription = '';


        return view('dashboard', compact('commissions', 'rewardDescription', 'affiliate_links'));
    }
}
