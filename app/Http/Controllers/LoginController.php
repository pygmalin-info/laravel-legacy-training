<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/members');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $flg = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($flg) {
            $request->session()->regenerate();
            return redirect('/members');
        }

        return back()->withInput()->with('error', 'メールアドレスまたはパスワードが違います。');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
