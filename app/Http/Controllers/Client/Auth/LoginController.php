<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('client.pages.login');
    }

   public function handleLogin(LoginRequest $request)
{
    $credentials = $request->only('email', 'password');
    $remember = $request->has('remember');

    if (Auth::attempt($credentials, $remember)) {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return redirect('/admin')->with('success', 'Đăng nhập thành công (Admin)');
        } elseif ($user->role_id == 2) {
            return redirect('/')->with('success', 'Đăng nhập thành công (Khách hàng)');
        } else {
            Auth::logout();
            return redirect('/login')->withErrors(['email' => 'Tài khoản không có quyền truy cập']);

        }
    }

    return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng']);
}
}
