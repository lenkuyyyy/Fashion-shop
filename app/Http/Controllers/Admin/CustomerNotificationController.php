<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNotificationRequest;
class CustomerNotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::whereIn('user_id', function ($query) {
            $query->select('id')->from('users')->where('role_id', 2);
        })->orderByDesc('created_at')->paginate(10);
        
        return view('admin.others_menu.notifications-client', compact('notifications'));
    }

    public function create()
    {
        return view('admin.others_menu.notifications-client');
    }
    // hàm dùng để tạo thông báo gửi tới khách hàng
    public function store(StoreNotificationRequest $request)
{
    $users = match ($request->target) {
        'all_customers' => User::where('role_id', 2)->get(),
        'all_admins' => User::where('role_id', 1)->get(),
        default => collect([]),
    };

    foreach ($users as $user) {
        Notification::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'is_read' => false,
        ]);
    }

    return redirect()->route('customer-notifications')
                     ->with('success', 'Đã gửi thông báo đến ' . $request->target . '.');
}
}
