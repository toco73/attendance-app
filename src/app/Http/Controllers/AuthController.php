<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->only('email','password'),$request->filled('remember'))){
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません'],
            ]);
        }
        
        $request->session()->regenerate();
        return redirect()->route('attendance.index');
    }
}
