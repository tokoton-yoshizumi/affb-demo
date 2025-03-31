<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GeneralRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.general-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 新規ユーザーを作成
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // save() を使うことでイベントリスナーを発火させる
        $user->save();  // これにより、Userモデルのbootメソッドのcreatedイベントが発火します

        // ユーザーを自動的にログイン
        Auth::login($user);

        // ダッシュボードへリダイレクト
        return redirect()->route('dashboard');
    }
}
