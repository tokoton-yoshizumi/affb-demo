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
        $products = Product::where('status', '公開')->get();
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
            'description' => 'nullable',
            'price' => 'nullable|integer',
            'url' => 'nullable|url',
            'price_id' => 'nullable|string',
            'commissions_form' => 'required|array',
            'commissions_payment' => 'required|array',
            'thank_you_url' => 'nullable|url',
            'status' => 'required|in:公開,非公開'
        ]);

        // 商材登録
        $product = Product::create($request->only(['name', 'description', 'price', 'url', 'price_id', 'thank_you_url', 'status']));

        Log::info('Product created successfully', ['product_id' => $product->id]);

        // フォーム報酬 / 決済報酬を保存
        foreach ($request->commissions_form as $affiliateTypeId => $formAmount) {
            $paymentAmount = $request->commissions_payment[$affiliateTypeId] ?? null;

            ProductCommission::create([
                'product_id' => $product->id,
                'affiliate_type_id' => $affiliateTypeId,
                'fixed_commission_on_form' => $formAmount,
                'fixed_commission_on_payment' => $paymentAmount,
            ]);
        }

        Log::info('All commissions saved successfully', ['product_id' => $product->id]);

        // アフィリエイトリンクの生成（アフィリエイターごと）
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
                'description' => 'nullable',
                'price' => 'nullable|integer',
                'url' => 'nullable|url',
                'price_id' => 'nullable|string',
                'commissions_form' => 'required|array',
                'commissions_payment' => 'required|array',
                'thank_you_url' => 'nullable|url',
                'status' => 'required|in:公開,非公開'
            ]);

            // 商材を更新
            $product->update($request->only([
                'name',
                'description',
                'price',
                'url',
                'price_id',
                'thank_you_url',
                'status'
            ]));

            // 既存の報酬を削除
            $product->commissions()->delete();

            // フォーム報酬 / 決済報酬を再登録
            foreach ($request->commissions_form as $affiliateTypeId => $formAmount) {
                $paymentAmount = $request->commissions_payment[$affiliateTypeId] ?? null;

                ProductCommission::create([
                    'product_id' => $product->id,
                    'affiliate_type_id' => $affiliateTypeId,
                    'fixed_commission_on_form' => $formAmount,
                    'fixed_commission_on_payment' => $paymentAmount,
                ]);
            }

            return redirect()->route('products.index')->with('success', '商材が更新されました');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('商材の更新中にエラーが発生しました。');
        }
    }


    // 商材の削除
    public function destroy(Product $product)
    {
        try {
            // 商品に関連するアフィリエイトリンクを削除
            $affiliateLinks = $product->affiliateLinks;

            // 各アフィリエイトリンクに関連するコミッションを削除
            foreach ($affiliateLinks as $link) {
                $link->commissions()->delete(); // 関連する affiliate_commissions を削除
            }

            // アフィリエイトリンクを削除
            $product->affiliateLinks()->delete();

            // 商品に関連するコミッションを削除
            $product->commissions()->delete();

            // 商品自体を削除
            $product->delete();

            Log::info('Product and all related data deleted successfully', ['product_id' => $product->id]);

            return redirect()->route('products.index')->with('success', '商材が削除されました');
        } catch (\Exception $e) {
            Log::error('Error deleting product', [
                'product_id' => $product->id,
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors('商材の削除中にエラーが発生しました。');
        }
    }

    public function createForm($productId)
    {
        $product = Product::findOrFail($productId);
        $affiliateTypes = AffiliateType::all();
        return view('admin.products.create_form', compact('product', 'affiliateTypes'));
    }

    public function showThankYouScript($productId)
    {
        $product = Product::findOrFail($productId);
        return view('admin.products.show_thank_you', compact('product'));
    }




    public function showCode($productId)
    {
        // 商材IDで商材を取得
        $product = Product::findOrFail($productId);

        // 商材の情報をビューに渡す
        return view('admin.products.show_code', compact('product'));
    }

    public function showCodeThanks(Product $product)
    {
        // 完了画面に必要なデータをビューに渡す
        return view('admin.products.show_code_thanks', compact('product'));
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
