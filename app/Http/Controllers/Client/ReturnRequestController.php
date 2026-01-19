<?php

namespace App\Http\Controllers\Client;

use App\Models\Notification;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Events\OrderStatusUpdated;

class ReturnRequestController extends Controller
{
public function requestReturn($orderId, Request $request)
    {
        $order = Order::findOrFail($orderId);

        // Kiểm tra trạng thái đơn hàng
        if ($order->status !== 'delivered') {
            return back()->with('return-error', 'Chỉ có thể yêu cầu trả hàng với đơn đã được giao.');
        }

        // Kiểm tra xem đã có yêu cầu trả hàng chưa
        if ($order->returnRequest) {
            return back()->with('return-error', 'Bạn đã gửi yêu cầu trả hàng.');
        }

        $reason = trim($request->input('reason'));

        if (empty($reason)) {
            return back()->with('return-error', 'Vui lòng cung cấp lý do trả hàng.');
        }

        // Ghi nhận yêu cầu trả hàng
        ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'status' => 'requested',
            'reason' => $reason,
        ]);

        // Cập nhật trạng thái đơn hàng
        $order->status = 'refund_in_processing';
        $order->save();

        // Ghi log để kiểm tra
        \Log::info('Return request processed', [
            'order_id' => $orderId,
            'status' => $order->status,
            'reason' => $reason,
        ]);

        // Gửi thông báo đến admin
        $this->notifyAdminsAboutReturnRequest($order);

        // Phát sự kiện để cập nhật giao diện client
        broadcast(new OrderStatusUpdated($order));

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu trả hàng đã được ghi nhận. Vui lòng chờ admin liên hệ.',
            'order_code' => $order->order_code,
            'status' => $order->status,
        ]);
    }
    /**
     * Gửi thông báo đến tất cả admin về yêu cầu trả hàng.
     *
     * @param Order $order
     * @return void
     */
    private function notifyAdminsAboutReturnRequest(Order $order)
    {
        try {
            $admins = User::where('role_id', 1)->get();

            if ($admins->isEmpty()) {
                return; // Không có admin nào thì bỏ qua
            }

            $message = "Người dùng #{$order->user_id} - {$order->user->name} đã yêu cầu trả hàng cho đơn #{$order->order_code}. Vui lòng kiểm tra và xử lý.";

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id'   => $admin->id,
                    'title'     => 'Yêu cầu trả hàng',
                    'message'   => $message,
                    'type'      => 'order',
                    'is_read'   => false,
                    'order_id'  => $order->id,
                    'created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi tạo thông báo yêu cầu trả hàng: ' . $e->getMessage());
        }
    }
}
