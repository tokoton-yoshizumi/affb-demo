<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AgentRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.agent-register'); // 代理店向け登録フォームのビューを表示
    }

    public function register(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 代理店ユーザーを作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_agent' => true,  // 代理店として自動的に登録
        ]);

        // ユーザーを自動的にログイン
        Auth::login($user);

        // ダッシュボードへリダイレクト
        return redirect()->route('dashboard');
    }
}
