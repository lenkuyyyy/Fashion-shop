<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Client\Auth\Mail\SendVerificationCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('client.pages.register'); // Hiển thị form đăng ký
    }
    public function sendOtp(RegisterRequest $request)
{
    $otp = strtoupper(Str::random(6));
    session([
        'otp_register' => $otp,
        'register_data' => $request->only('name', 'email', 'password')
    ]);

    Mail::to($request->email)->send(new SendVerificationCode($otp));

    return back()->with('otp_sent', true);
}

   public function registerWithOtp(Request $request)
{
    if ($request->otp !== session('otp_register')) {
        return back()
            ->withErrors(['otp' => '❌ Mã xác minh không đúng'])
            ->with('otp_sent', true); // giữ lại trạng thái để hiển thị form OTP
    }

    $data = session('register_data');

    User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'status' => 'active',
        'role_id' => 2,
    ]);

    session()->forget(['otp_register', 'register_data']);

    return redirect()->route('login')->with('success', '✅ Đăng ký thành công, mời bạn đăng nhập.');
}

}
