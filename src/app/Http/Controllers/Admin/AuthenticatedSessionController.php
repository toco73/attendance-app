<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function showLoginForm()
    {
        return view('/admin/auth/login');
    }

    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $credentials['is_admin'] = true;

        if (Auth::attempt($credentials)){
            return redirect()->intended('/admin/attendance/list');
        }

        // 認証失敗時
        return back()
            ->withErrors(['email' => 'ログイン情報が登録されていません'])
            ->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
