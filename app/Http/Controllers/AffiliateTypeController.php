<?php

namespace App\Http\Controllers;

use App\Models\AffiliateType;
use Illuminate\Http\Request;

class AffiliateTypeController extends Controller
{
    public function index()
    {
        $affiliateTypes = AffiliateType::all();
        return view('admin.affiliate-types.index', compact('affiliateTypes'));
    }

    public function create()
    {
        return view('admin.affiliate-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AffiliateType::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('affiliate-types.index')->with('success', 'アフィリエイタータイプが作成されました');
    }

    public function edit(AffiliateType $affiliateType)
    {
        return view('admin.affiliate-types.edit', compact('affiliateType'));
    }

    public function update(Request $request, AffiliateType $affiliateType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $affiliateType->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('affiliate-types.index')->with('success', 'アフィリエイタータイプが更新されました');
    }

    public function destroy(AffiliateType $affiliateType)
    {
        $affiliateType->delete();
        return redirect()->route('affiliate-types.index')->with('success', 'アフィリエイタータイプが削除されました');
    }
}
