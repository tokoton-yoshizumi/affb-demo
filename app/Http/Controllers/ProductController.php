<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AffiliateLink;
use App\Models\AffiliateType;
use App\Models\ProductCommission;
use Illuminate\Support\Facades\Log;

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
        $product = Product::create($request->only(['name', 'description', 'price', 'url', 'price_id']));

        Log::info('Product created successfully', ['product_id' => $product->id]);

        // 各アフィリエイタータイプごとの報酬を保存
        foreach ($request->commissions as $affiliateTypeId => $commission) {
            if ($commission !== null) {
                Log::info('Saving commission for affiliate type', [
                    'affiliate_type_id' => $affiliateTypeId,
                    'fixed_commission' => $commission,
                ]);

                ProductCommission::create([
                    'product_id' => $product->id,
                    'affiliate_type_id' => $affiliateTypeId,
                    'fixed_commission' => $commission,
                ]);
            }
        }

        Log::info('All commissions saved successfully', ['product_id' => $product->id]);

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

        // 次のステップにリダイレクト
        return redirect()->route('products.showCode', ['product' => $product->id]);
    }


    // 商材編集フォームの表示
    public function edit(Product $product)
    {
        // アフィリエイタータイプを取得
        $affiliateTypes = AffiliateType::all();

        // 商材に関連するコミッションを取得
        $commissions = $product->commissions->pluck('fixed_commission', 'affiliate_type_id')->toArray();

        // ビューにデータを渡す
        return view('admin.products.edit', compact('product', 'affiliateTypes', 'commissions'));
    }


    // 商材の更新
    public function update(Request $request, Product $product)
    {
        try {
            // バリデーション
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|integer',
                'url' => 'required|url',
                'commissions' => 'required|array',
            ]);

            // 商材を更新
            $product->update($request->only(['name', 'description', 'price', 'url', 'price_id']));

            // ログに更新情報を記録
            Log::info('Product updated successfully', ['product_id' => $product->id]);

            // 報酬情報を更新
            ProductCommission::where('product_id', $product->id)->delete();

            foreach ($request->commissions as $affiliateTypeId => $commission) {
                if ($commission !== null) {
                    Log::info('Updating commission for affiliate type', [
                        'affiliate_type_id' => $affiliateTypeId,
                        'fixed_commission' => $commission,
                    ]);

                    ProductCommission::create([
                        'product_id' => $product->id,
                        'affiliate_type_id' => $affiliateTypeId,
                        'fixed_commission' => $commission,
                    ]);
                }
            }

            Log::info('All commissions updated successfully', ['product_id' => $product->id]);

            // アフィリエイトリンクを更新
            $affiliates = User::where('is_admin', false)->get();
            foreach ($affiliates as $affiliate) {
                $affiliateLink = AffiliateLink::firstOrCreate(
                    ['user_id' => $affiliate->id, 'product_id' => $product->id],
                    ['token' => Str::random(10)]
                );

                // ログにリンク更新情報を記録
                Log::info('Affiliate link updated:', ['affiliate_id' => $affiliate->id, 'product_id' => $product->id]);

                $affiliateLink->update([
                    'url' => $product->url . '?ref=' . $affiliate->id,
                ]);
            }

            // 更新後に追跡コードのページへリダイレクト
            return redirect()->route('products.showCode', ['product' => $product->id])
                ->with('success', '商材とアフィリエイトリンク、報酬が更新されました');
        } catch (\Exception $e) {
            // エラーログを出力
            Log::error('Error updating product:', [
                'error_message' => $e->getMessage(),
                'product_id' => $product->id,
            ]);

            return redirect()->back()->withErrors('商材の更新中にエラーが発生しました。');
        }
    }






    // 商材の削除
    public function destroy(Product $product)
    {
        try {
            // 関連データを削除
            $product->commissions()->delete(); // ProductCommissionの削除
            $product->affiliateLinks()->delete(); // AffiliateLinkの削除

            // 商材の削除
            $product->delete();

            // ログに削除情報を記録（任意）
            Log::info('Product deleted successfully', ['product_id' => $product->id]);

            return redirect()->route('products.index')->with('success', '商材が削除されました');
        } catch (\Exception $e) {
            // エラーログ
            Log::error('Error deleting product', [
                'product_id' => $product->id,
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors('商材の削除中にエラーが発生しました。');
        }
    }


    public function showCode($productId)
    {
        // 商材IDで商材を取得
        $product = Product::findOrFail($productId);

        // 商材の情報をビューに渡す
        return view('admin.products.show_code', compact('product'));
    }

    public function trackLater($productId)
    {
        // トラッキングコード未実装状態にする
        $product = Product::findOrFail($productId);
        $product->tracking_code_status = 'not_implemented';
        $product->save();

        return redirect()->route('products.index')->with('status', '「トラッキングコード未実装」としてマークされました。');
    }

    public function trackDone($productId)
    {
        // トラッキングコード完了状態にする
        $product = Product::findOrFail($productId);
        $product->tracking_code_status = 'implemented';
        $product->save();

        return redirect()->route('products.index')->with('status', '「トラッキングコード完了」としてマークされました。');
    }



    public function show($id)
    {
        // このメソッドが呼ばれた場合のデフォルトの動作を定義（空でも可）
        return redirect()->route('products.index');
    }
}
