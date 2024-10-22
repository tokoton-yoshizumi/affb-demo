<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AffiliateLink;
use App\Models\ProductCommission;
use App\Models\AffiliateType;

class ProductController extends Controller
{
    // 商材一覧の表示
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    // 商材登録フォームの表示
    public function create()
    {
        // 管理画面で登録されたすべてのアフィリエイタータイプを取得
        $affiliateTypes = AffiliateType::all();
        return view('admin.products.create', compact('affiliateTypes'));
    }

    // 商材の保存
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'url' => 'required|url',
            'commissions' => 'required|array',  // 各タイプごとの報酬を配列としてバリデーション
        ]);

        // 商材登録
        $product = Product::create($request->only(['name', 'description', 'price', 'url']));

        // 各アフィリエイタータイプごとの報酬を保存
        foreach ($request->commissions as $affiliateTypeId => $commission) {
            if ($commission !== null) {
                ProductCommission::create([
                    'product_id' => $product->id,
                    'affiliate_type_id' => $affiliateTypeId,
                    'fixed_commission' => $commission,
                ]);
            }
        }

        // 全アフィリエイターに対して商材ごとのアフィリエイトリンクを生成
        $affiliates = User::where('is_admin', false)->get();
        foreach ($affiliates as $affiliate) {
            AffiliateLink::create([
                'user_id' => $affiliate->id,
                'product_id' => $product->id,
                'url' => $product->url . '?ref=' . $affiliate->id,
                'token' => Str::random(10),
            ]);
        }

        return redirect()->route('products.index')->with('success', '商材と報酬、アフィリエイトリンクが生成されました');
    }

    // 商材編集フォームの表示
    public function edit(Product $product)
    {
        $affiliateTypes = AffiliateType::all();
        return view('admin.products.edit', compact('product', 'affiliateTypes'));
    }

    // 商材の更新
    public function update(Request $request, Product $product)
    {
        // バリデーション
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'url' => 'required|url',
            'commissions' => 'required|array', // 各タイプごとの報酬を配列としてバリデーション
        ]);

        // 商材を更新
        $product->update($request->only(['name', 'description', 'price', 'url']));

        // 報酬情報を更新
        ProductCommission::where('product_id', $product->id)->delete(); // 既存の報酬を削除して再登録
        foreach ($request->commissions as $affiliateTypeId => $commission) {
            if ($commission !== null) {
                ProductCommission::create([
                    'product_id' => $product->id,
                    'affiliate_type_id' => $affiliateTypeId,
                    'fixed_commission' => $commission,
                ]);
            }
        }

        // アフィリエイトリンクを更新
        $affiliates = User::where('is_admin', false)->get();
        foreach ($affiliates as $affiliate) {
            // 既存のアフィリエイトリンクがある場合は更新、なければ作成
            $affiliateLink = AffiliateLink::firstOrCreate(
                ['user_id' => $affiliate->id, 'product_id' => $product->id],
                ['token' => Str::random(10)]
            );

            // URLを更新
            $affiliateLink->update([
                'url' => $product->url . '?ref=' . $affiliate->id,
            ]);
        }

        return redirect()->route('products.index')->with('success', '商材とアフィリエイトリンク、報酬が更新されました');
    }

    // 商材の削除
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', '商材が削除されました');
    }
}
