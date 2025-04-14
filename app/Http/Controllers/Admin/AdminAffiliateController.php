<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\AffiliateLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminAffiliateController extends Controller
{
    public function show($id)
    {
        $affiliate = User::with(['commissions', 'affiliateLinks.product'])->findOrFail($id);

        return view('admin.affiliates.show', compact('affiliate'));
    }

    public function destroy($id)
    {
        $affiliate = User::findOrFail($id);

        // 関連データの削除（必要であれば）
        $affiliate->commissions()->delete();
        $affiliate->affiliateLinks()->delete();

        $affiliate->delete();

        return redirect()->route('admin.dashboard')->with('success', 'アフィリエイターを削除しました。');
    }

    /**
     * リンクの有効／無効を切り替える処理（個別リンク単位）
     */
    public function toggleLinkStatus($linkId)
    {
        $link = AffiliateLink::findOrFail($linkId);

        $link->is_active = !$link->is_active;
        $link->save();

        return back()->with('success', 'リンクのステータスを変更しました。');
    }

    /**
     * アフィリエイターの全リンクを一括で無効化する（必要なら）
     */
    public function disableAllLinks($userId)
    {
        $user = User::findOrFail($userId);

        $user->affiliateLinks()->update(['is_active' => false]);

        return back()->with('success', 'すべてのアフィリエイトリンクを無効にしました。');
    }
}
