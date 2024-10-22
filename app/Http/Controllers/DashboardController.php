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

        // アフィリエイト報酬を取得
        $commissions = AffiliateCommission::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        // アフィリエイトプログラムの報酬タイプを判定
        $affiliateProgram = $user->affiliateProgram;
        $rewardDescription = '';

        if ($affiliateProgram->fixed_commission) {
            $rewardDescription = '1件につき¥' . number_format($affiliateProgram->fixed_commission);
        } elseif ($affiliateProgram->commission_rate) {
            // 小数点以下を切り捨てた整数のみを表示
            $rewardDescription = '購入金額より' . floor($affiliateProgram->commission_rate) . '%';
        } else {
            $rewardDescription = '報酬は設定されていません';
        }

        return view('dashboard', compact('commissions', 'rewardDescription', 'affiliate_links'));
    }
}
