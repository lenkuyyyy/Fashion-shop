<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class VerifyController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $code = strtoupper(Str::random(6));
        Session::put('verify_email', $request->email);
        Session::put('verify_code', $code);

        Mail::raw("Mã xác minh tài khoản: $code", function ($m) use ($request) {
            $m->to($request->email)->subject('Mã xác minh tài khoản');
        });

        return response()->json(['message' => 'Mã xác minh đã được gửi']);
    }

    public function check(Request $request)
    {
        if ($request->code === Session::get('verify_code') && $request->email === Session::get('verify_email')) {
            Session::put('email_verified', true);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Sai mã xác minh'], 422);
    }
}
