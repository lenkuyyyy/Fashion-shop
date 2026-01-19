<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TestOrderController extends Controller
{
    public function __construct()
    {
        // Áp dụng middleware restrict.admin cho các hàm admin
        // $this->middleware('restrict.admin')->only(['updateStatus', 'requestCancel', 'handleCancelRequest', 'updateReturnStatus']);
    }

    public function index(Request $request)
    {
        $userId = 21; // ✅ Test cứng với user_id = 21

        $query = Order::with(['user', 'shippingAddress', 'orderDetails.productVariant', 'coupon', 'returnRequest'])
            ->where('user_id', $userId) // ✅ thay vì Auth::id()
            ->orderByDesc('created_at');

        if ($request->filled('q')) {
            $query->where('order_code', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }


public function show($id)
{
    $userId = 21; // gán cứng để test

    $order = Order::with(['user', 'shippingAddress', 'orderDetails.productVariant', 'coupon', 'returnRequest'])
        ->where('user_id', $userId)
        ->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => $order
    ]);
}


    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $newStatus = $request->input('status');

        $validTransitions = [
            'pending' => 'processing',
            'processing' => 'shipped',
            'shipped' => 'delivered',
        ];

        if (!isset($validTransitions[$order->status]) || $validTransitions[$order->status] !== $newStatus) {
            return response()->json(['error' => 'Chuyển trạng thái không hợp lệ'], 400);
        }

        $order->status = $newStatus;
        $order->save();

        $this->createOrderNotificationToClient(
            $order,
            "Trạng thái đơn hàng #{$order->order_code} đã được cập nhật thành '{$newStatus}'.",
            'Cập nhật trạng thái đơn hàng'
        );

        return response()->json(['success' => 'Cập nhật trạng thái đơn hàng thành công']);
    }

public function requestCancel(Request $request, $id)
{
    $userId = 21; // test cứng

    $order = Order::where('user_id', $userId)->findOrFail($id);

    if (!in_array($order->status, ['pending', 'processing'])) {
        return response()->json([
            'success' => false,
            'message' => 'Không thể hủy đơn hàng ở trạng thái hiện tại.'
        ]);
    }

    $order->status = 'cancel_request';
    $order->cancel_reason = $request->cancel_reason;
    $order->save();

    return response()->json([
        'success' => true,
        'message' => 'Đã gửi yêu cầu hủy đơn hàng thành công.'
    ]);
}

    public function handleCancelRequest(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $action = $request->input('action');
        $note = $request->input('admin_cancel_note');

        if ($order->cancel_confirmed) {
            return response()->json(['error' => 'Yêu cầu hủy đã được xử lý'], 400);
        }

        if (!in_array($action, ['approve', 'reject'])) {
            return response()->json(['error' => 'Hành động không hợp lệ'], 400);
        }

        if ($action === 'approve') {
            $order->cancel_confirmed = true;
            $order->admin_cancel_note = $note ?: 'Admin xác nhận hủy đơn';
            $order->status = 'cancelled';
            $order->payment_status = $order->payment_status === 'completed' && in_array($order->payment_method, ['online', 'bank_transfer'])
                ? 'refund_in_processing'
                : 'failed';
            $order->save();

            $this->createOrderNotificationToClient(
                $order,
                "Đơn hàng #{$order->order_code} đã được hủy.",
                'Đơn hàng đã hủy'
            );

            return response()->json(['success' => 'Xác nhận hủy đơn hàng thành công']);
        }

        if ($action === 'reject') {
            if (empty($note)) {
                return response()->json(['error' => 'Yêu cầu lý do từ chối'], 422);
            }

            $order->cancel_confirmed = true;
            $order->admin_cancel_note = $note;
            $order->save();

            $this->createOrderNotificationToClient(
                $order,
                "Yêu cầu hủy đơn hàng #{$order->order_code} bị từ chối. Lý do: {$note}",
                'Yêu cầu hủy bị từ chối'
            );

            return response()->json(['success' => 'Từ chối yêu cầu hủy đơn hàng thành công']);
        }
    }

    public function updateReturnStatus(Request $request, $id)
    {
        $returnRequest = ReturnRequest::with('order.user')->findOrFail($id);
        $order = $returnRequest->order;
        $newStatus = $request->input('status');

        if ($newStatus === 'rejected' && !$request->filled('admin_note')) {
            return response()->json(['error' => 'Yêu cầu lý do từ chối trả hàng'], 422);
        }

        $validTransitions = [
            'requested' => ['approved', 'rejected'],
            'approved' => ['refunded'],
        ];

        if (!isset($validTransitions[$returnRequest->status]) || !in_array($newStatus, $validTransitions[$returnRequest->status])) {
            return response()->json(['error' => 'Chuyển trạng thái không hợp lệ'], 400);
        }

        $returnRequest->status = $newStatus;
        $returnRequest->admin_note = $request->input('admin_note') ?: "Yêu cầu trả hàng được cập nhật thành {$newStatus}";

        if ($newStatus === 'approved' && in_array($order->payment_method, ['online', 'bank_transfer']) && $order->payment_status === 'completed') {
            $order->payment_status = 'refund_in_processing';
            $order->save();
        }

        if ($newStatus === 'refunded' && in_array($order->payment_method, ['online', 'bank_transfer']) && $order->payment_status === 'refund_in_processing') {
            $order->payment_status = 'refunded';
            $order->save();
        }

        $returnRequest->save();

        $this->createOrderNotificationToClient(
            $order,
            "Yêu cầu trả hàng của đơn #{$order->order_code} được cập nhật thành '{$newStatus}'.",
            'Cập nhật yêu cầu trả hàng'
        );

        return response()->json(['success' => 'Cập nhật trạng thái trả hàng thành công']);
    }

    private function createOrderNotificationToClient(Order $order, $message, $title)
    {
        try {
            Notification::create([
                'user_id' => $order->user_id,
                'title' => $title,
                'message' => $message,
                'type' => 'order',
                'is_read' => false,
                'order_id' => $order->id,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo thông báo cho khách hàng: ' . $e->getMessage());
        }
    }

    private function createOrderNotification(Order $order, $message, $title)
    {
        try {
            $admins = \App\Models\User::where('role_id', 1)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'order',
                    'is_read' => false,
                    'order_id' => $order->id,
                    'created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi tạo thông báo cho admin: ' . $e->getMessage());
        }
    }
}
