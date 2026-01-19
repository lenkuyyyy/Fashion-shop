<?php

namespace App\Http\Controllers\Client\Auth\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ResetPasswordRequest;
class ResetPasswordController extends Controller
{
    public function showResetForm($token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request()->query('email') // để điền sẵn email nếu có
        ]);
    }

    public function reset(ResetPasswordRequest $request)
{
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('success', '✅ Mật khẩu đã được thay đổi thành công!') // <-- chuyển hướng
        : back()->withErrors(['email' => [__($status)]]);
}
}
