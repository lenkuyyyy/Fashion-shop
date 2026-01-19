<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientNotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('client.pages.notifications-client', compact('notifications', 'unreadCount'));
    }
    // hàm để đánh dấu all các thông báo là đã đọc
    public function markAllRead(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (!Auth::check()) {
            return redirect()->route('client.notifications')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này');
        }
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);

        return redirect()->route('client.notifications')->with('success', 'Đã đánh dấu tất cả là đã đọc');
    }
}