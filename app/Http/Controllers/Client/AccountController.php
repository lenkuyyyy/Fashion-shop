<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateAccountClientRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    // hàm hiển thị trang tài khoản người dùng
    public function show()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này.');
        }     
        return view('client.pages.account', compact('user'));
    }

    public function update(UpdateAccountClientRequest $request)
    {
        $user = Auth::user();

        $user->name = $request->name;
        $user->address = $request->address;
        $user->phone_number = $request->phone_number;

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Lưu avatar mới
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if (!$user->google_id && $request->filled('new_password')) {
            if (!$request->filled('old_password')) {
                return back()->withErrors(['old_password' => 'Vui lòng nhập mật khẩu cũ khi thay đổi mật khẩu.']);
            }
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'Mật khẩu cũ không đúng']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->back()->with('success', '✅ Cập nhật tài khoản thành công!');
    }
}