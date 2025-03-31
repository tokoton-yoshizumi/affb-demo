<?php

use Illuminate\Http\Request;
use App\Models\AffiliateLink;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormWebhookController;
use App\Http\Controllers\AffiliateTypeController;
use App\Http\Controllers\AgentRegisterController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StripeCheckoutController;
use App\Http\Controllers\GeneralRegisterController;
use App\Http\Controllers\Admin\RewardRequestController;
use App\Http\Controllers\RobotPaymentWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (Request $request) {
    // URLにアフィリエイトトークン（ref）が含まれている場合、それをセッションに保存
    if ($request->has('ref')) {
        session(['affiliate_ref' => $request->input('ref')]);
    }

    return view('welcome'); // 公式サイトのトップページビューを表示
});

Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/reward-request', [RewardController::class, 'showRequestForm'])->name('reward.request');
Route::post('/reward-request/confirm', [RewardController::class, 'confirmRequest'])->name('reward.confirm');
Route::post('/reward-request/finalize', [RewardController::class, 'finalizeRequest'])->name('reward.finalize');


// 認証が必要なルート
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Checkout関連のルート
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Stripe決済後のwebhook
Route::post('/webhook/stripe-zen', [WebhookController::class, 'handleWebhook']);


// Webhookエンドポイントの設定
Route::post('/webhook/form', [FormWebhookController::class, 'handle'])->name('webhook.form');

Route::post('/create-checkout-session/{productId}', [StripeCheckoutController::class, 'createCheckoutSession']);



Route::post('/webhook/stripe', [WebhookController::class, 'handleStripeWebhook'])->name('webhook.stripe');


// 一般向けの登録
Route::get('/general-register', [GeneralRegisterController::class, 'showRegistrationForm'])->name('general.register');
Route::post('/general-register', [GeneralRegisterController::class, 'register']);

// 代理店向けの登録
Route::get('/agent-register', [AgentRegisterController::class, 'showRegistrationForm'])->name('agent.register');
Route::post('/agent-register', [AgentRegisterController::class, 'register']);


// 管理者
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/products/show-code/{product}', [ProductController::class, 'showCode'])->name('products.showCode');

    Route::get('/products/show-code-thanks/{product}', [ProductController::class, 'showCodeThanks'])->name('products.show_code_thanks');


    Route::post('/products/{product}/track-later', [ProductController::class, 'trackLater'])->name('products.track_later');
    Route::post('/products/{product}/track-done', [ProductController::class, 'trackDone'])->name('products.track_done');

    // フォーム作成のためのルート
    Route::get('/products/{product}/create-form', [ProductController::class, 'createForm'])->name('products.createForm');
    Route::post('/products/{product}/save-form', [ProductController::class, 'saveForm'])->name('products.saveForm');

    // サンクスページスクリプトを表示するルート
    Route::get('/products/{product}/show-thank-you-script', [ProductController::class, 'showThankYouScript'])->name('products.showThankYouScript');

    Route::resource('products', ProductController::class);
    Route::resource('affiliate-types', AffiliateTypeController::class);
    Route::get('/admin/reward-requests', [RewardRequestController::class, 'index'])->name('admin.reward-requests.index');
    Route::put('/admin/reward-requests/{id}', [RewardRequestController::class, 'update'])->name('admin.reward-requests.update');


    Route::resource('customers', CustomerController::class);
});


// ROBOT PAYMENT のキックバック
Route::match(['get', 'post'], '/webhook/robot-payment', [RobotPaymentWebhookController::class, 'handle']);


require __DIR__ . '/auth.php';
