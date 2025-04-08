<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AffiliateCommission;
use App\Http\Controllers\Controller;

class AffiliateCommissionController extends Controller
{
    public function updateStatus(Request $request, AffiliateCommission $commission)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:確定,重複,保留'],
        ]);

        $commission->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('status', 'ステータスを更新しました');
    }
}
