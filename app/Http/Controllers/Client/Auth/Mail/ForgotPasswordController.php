<?php


namespace App\Http\Controllers\Client\Auth\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ForgotPasswordRequest;
class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return back()->with(
            $status === Password::RESET_LINK_SENT
                ? ['success' => '📩 Đã gửi liên kết đặt lại mật khẩu!']
                : ['error' => '❌ Không thể gửi email đến địa chỉ này.']
        );
    }
}

