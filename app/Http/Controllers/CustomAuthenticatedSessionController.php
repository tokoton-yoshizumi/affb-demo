<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class CustomAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login'); // ログインページのビューを返す
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // 管理者なら /admin/dashboard にリダイレクト
            if (Auth::user()->is_admin) {
                return redirect()->intended('/admin/dashboard');
            }

            // 通常ユーザーは /dashboard にリダイレクト
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
