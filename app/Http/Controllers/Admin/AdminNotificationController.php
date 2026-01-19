<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{   
    // hàm này dùng để đổ dữ liệu vào giao diện thông báo
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);
         $unreadCount = Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->count();
        return view('admin.others_menu.notifications', compact('notifications', 'unreadCount'));
    }
    // hàm này lọc ra thông báo chưa đọc để đánh dấu đã đọc
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        if ($notification->user_id !== Auth::id()) {
            return back()->with('error', 'Không có quyền truy cập thông báo này.');
        }
        $notification->update(['is_read' => true]);
        return back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
    }
    // hàm này đánh dấu tất cả thông báo đã đọc nhưng trong giao diện chưa có
    public function markAllRead(Request $request)
    {
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);
        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
