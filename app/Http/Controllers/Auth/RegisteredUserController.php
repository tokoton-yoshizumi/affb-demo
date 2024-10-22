<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // キーワードを定義
        $supporterKeyword = 'Zen!Sup2024#Key';

        // バリデーション
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'supporter_keyword' => ['required', function ($attribute, $value, $fail) use ($supporterKeyword) {
                if ($value !== $supporterKeyword) {
                    $fail('サポーターキーワードが正しくありません。');
                }
            }],
        ]);

        // ユーザーを作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_partner' => true,  // パートナーとして自動的に登録
        ]);

        // 登録イベントを発火
        event(new Registered($user));

        // ログイン
        Auth::login($user);

        // ホームページにリダイレクト
        return redirect(RouteServiceProvider::HOME);
    }
}
