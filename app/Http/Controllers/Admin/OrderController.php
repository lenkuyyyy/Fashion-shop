<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Models\ReturnRequest;
use Illuminate\Support\Facades\Log;
use App\Events\OrderStatusUpdated;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'shippingAddress', 'orderDetails.productVariant', 'coupon', 'returnRequest'])
            ->orderByDesc('created_at');
        $statuses = [
            'pending' => 'Đang chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao',
            'delivered' => 'Đã hoàn thành',
            'cancelled' => 'Đơn đã hủy',
            'refund_in_processing' => 'Đang xử lý trả hàng',
            'refunded' => 'Đã hoàn tiền',
        ];
        $q = request()->query('q');
        $hasSearch = false;

        // Lọc theo tên sản phẩm
        if ($request->filled('q')) {
            $query->where('order_code', 'like', '%' . $request->q . '%');
            $hasSearch = true;
        }
        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
            $hasSearch = true;
        }

        // Lấy danh sách sản phẩm, ưu tiên đơn online đã thnah toán,
        //  nhưng nếu có đơn cod chen ngang thì vẫn hiện đơn cod với phân trang
        $orders = $query
            ->orderByRaw("CASE 
                WHEN payment_method = 'online' THEN 1
                WHEN payment_method = 'cod' THEN 2
                ELSE 3
                END")
            ->orderByDesc('created_at') // đơn mới nhất trước
            ->paginate(10);

        // Kiểm tra nếu có tìm kiếm nhưng không có kết quả
        $noResults = $hasSearch && $orders->isEmpty();

        // dd($orders);
        return view('admin.orders.orders', compact('orders', 'statuses', 'noResults'));
    }

    public function show($id)
    {
        $order = Order::with('user', 'shippingAddress', 'orderDetails.productVariant.product', 'coupon')
            ->findOrFail($id);


        $total = $order->orderDetails()->sum('subtotal');
        // Lấy discount từ cột đã lưu (không cần tính lại từ coupon)
        $discount = 0;
        $orderDiscount = $order->order_discount ?? 0;
        $shippingDiscount = $order->shipping_discount ?? 0;

        // Tính toán giảm giá nếu có coupon
        if ($order->coupon) {
            if ($order->coupon->discount_type === 'fixed') {
                $discount = $order->coupon->discount_value;
            } elseif ($order->coupon->discount_type === 'percent') {
                $discount = round($total * $order->coupon->discount_value / 100);
            }
        }

        return view('admin.orders.show', compact('order', 'discount', 'total', 'orderDiscount', 'shippingDiscount'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldStatusLabel = Order::getStatusMeta($order->status)['label'];


        // Lấy trạng thái mới từ request
        $newStatus = $request->input('status');
        $newStatusLabel = Order::getStatusMeta($newStatus)['label'];

        // Mảng trạng thái hợp lệ và chuyển đổi
        $validTransitions = [
            'pending' => 'processing',
            'processing' => 'shipped',
            'shipped' => 'delivered',
        ];

        if (!isset($validTransitions[$order->status]) || $validTransitions[$order->status] !== $newStatus) {
            return redirect()->back()->with('error', 'Chuyển trạng thái không hợp lệ.');
        }

        // Lưu trạng thái mới
        $order->status = $newStatus;
        $order->save();
        // Gửi sự kiện cập nhật trạng thái đơn hàng
        Log::info('Broadcasting OrderStatusUpdated event', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'channel' => 'orders.' . $order->user_id,
            'status' => $newStatus,
        ]);
        broadcast(new OrderStatusUpdated($order));
        // Tạo thông báo cho người dùng
        $message = "Trạng thái đơn hàng #{$order->order_code} đã được cập nhật từ '{$oldStatusLabel}' thành '{$newStatusLabel}'.";
        $this->createOrderNotificationToClient($order, $message, 'Đơn hàng đã được cập nhật');

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function cancel(Request $request, Order $order)
    {
        // Không thể huỷ nếu đơn đã hoàn tất hoặc đã huỷ
        if (in_array($order->status, ['delivered', 'completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Không thể huỷ đơn hàng đã hoàn tất hoặc đã huỷ.');
        }

        // Ghi nhận lý do huỷ nếu có
        $adminNote = $request->admin_cancel_note;

        // Kiểm tra nếu là huỷ theo yêu cầu từ khách hàng
        if ($order->cancellation_requested && !$order->cancel_confirmed) {
            $order->cancel_confirmed = true;
            $order->admin_cancel_note = $adminNote ?? 'Admin xác nhận yêu cầu huỷ từ khách.';
        } else {
            // Admin chủ động huỷ
            $order->cancel_reason = null; // Xoá lý do từ khách nếu có
            $order->cancel_confirmed = true;
            $order->admin_cancel_note = $adminNote;
            $order->cancellation_requested = false;
        }

        if (empty($order->admin_cancel_note)) {
            return redirect()->back()->with('error', 'Vui lòng cung cấp lý do huỷ đơn hàng.');
        }

        // Đánh dấu trạng thái đơn hàng là huỷ
        $order->status = 'cancelled';
        // Lưu lại trạng thái thanh toán trước khi cập nhật
        $originalPaymentStatus = $order->payment_status;

        // Xử lý trạng thái thanh toán dựa vào phương thức và trạng thái thanh toán
        if ($order->payment_status === 'completed') {
            switch ($order->payment_method) {
                case 'cod':
                    // COD đã thanh toán? Trường hợp hiếm
                    $order->payment_status = 'failed'; // thường không hoàn tiền COD
                    break;

                case 'bank_transfer':
                case 'online':
                    // Đơn đã thanh toán thành công → bắt đầu xử lý hoàn tiền
                    $order->payment_status = 'refund_in_processing';
                    break;
            }
        } else {
            // Chưa thanh toán hoặc thất bại → không cần hoàn tiền
            $order->payment_status = 'failed';
        }

        $order->save();


        // Hoàn kho nếu:
        // - Là COD (đã trừ kho khi đặt)
        // - Hoặc Online/Bank đã thanh toán thành công trước đó (tức là đã trừ kho)
        if (
            $order->payment_method === 'cod' ||
            in_array($order->payment_method, ['online', 'bank_transfer']) && $originalPaymentStatus === 'completed'
        ) {
            $this->restoreStockQuantity($order);
        }


        // Tạo thông báo cho khách
        if ($order->cancellation_requested) {
            $message = "Đơn hàng #{$order->order_code} đã được huỷ theo yêu cầu của bạn. ";
            $successMessage = "Đã xác nhận yêu cầu huỷ đơn hàng #{$order->order_code} từ khách hàng {$order->user->name}.";
        } else {
            $message = "Đơn hàng #{$order->order_code} đã được huỷ bởi quản trị viên. Lý do: {$order->admin_cancel_note}";
            $successMessage = "Đã huỷ đơn hàng #{$order->order_code} theo yêu cầu của quản trị viên. Lý do: {$order->admin_cancel_note}";
        }

        $this->createOrderNotificationToClient($order, $message, 'Đơn hàng đã được huỷ');

        // hiển thị thông báo thành công theo yêu cầu
        return redirect()->back()->with('success', $successMessage);
    }

    public function rejectCancel(Request $request, Order $order)
    {
        // Chỉ cho từ chối nếu có yêu cầu từ khách, chưa bị xử lý và chưa huỷ
        if (!$order->cancellation_requested || $order->cancel_confirmed || $order->status === 'cancelled') {
            return response()->json([
                'error' => 'Không thể từ chối yêu cầu huỷ đã xử lý hoặc đơn đã bị huỷ.'
            ], 400);
        }

        // Lý do từ chối
        $note = trim($request->input('admin_cancel_note'));

        if (empty($note)) {
            return response()->json([
                'error' => 'Vui lòng cung cấp lý do từ chối yêu cầu huỷ đơn hàng.'
            ], 422);
        }

        // Cập nhật đơn hàng
        $order->cancel_confirmed = true; // đánh dấu là đã xử lý
        $order->cancellation_requested = true; // Giữ nguyên yêu cầu huỷ vì chỉ cho gửi huỷ 1 lần thôi
        $order->admin_cancel_note = $note;
        $order->save();

        // Gửi thông báo cho khách hàng
        $this->createOrderNotificationToClient(
            $order,
            "Yêu cầu huỷ đơn hàng #{$order->order_code} của bạn đã bị từ chối. Lý do: {$order->admin_cancel_note}",
            'Yêu cầu huỷ đơn bị từ chối'
        );

        return redirect()->back()->with('success', 'Đã từ chối yêu cầu huỷ đơn hàng.');
    }

    public function markRefunded(Order $order)
    {
        if ($order->payment_status === 'refund_in_processing' && in_array($order->status, ['cancelled', 'refund_in_processing'])) {
            $order->payment_status = 'refunded';
            $order->status = 'refunded';
            $order->save();

            broadcast(new OrderStatusUpdated($order));

            return back()->with('success', 'Đã hoàn tiền xong cho đơn hàng.');
        }

        return back()->with('error', 'Chỉ hoàn tất hoàn tiền cho đơn đang xử lý.');
    }

    public function handleCancelRequest(Request $request, Order $order)
    {
        $action = $request->input('action'); // 'approve' or 'reject'
        $note = $request->input('admin_cancel_note');

        // Đầu mỗi nhánh xử lý (approve/reject), kiểm tra nếu đã xử lý thì không cho tiếp tục
        if ($order->cancel_confirmed) {
            return response()->json(['error' => 'Yêu cầu hủy đã được xử lý trước đó.'], 400);
        }

        if (!in_array($action, ['approve', 'reject'])) {
            return response()->json(['error' => 'Hành động không hợp lệ.'], 400);
        }

        if ($action === 'approve') {
            // Lưu trạng thái thanh toán gốc
            $originalPaymentStatus = $order->payment_status;

            // Xác nhận huỷ đơn
            $order->cancel_confirmed = true;
            $order->admin_cancel_note = $note ?: 'Admin xác nhận yêu cầu huỷ từ khách.';
            $order->status = 'cancelled';

            // Xử lý trạng thái thanh toán
            if ($order->payment_status === 'completed') {
                $order->payment_status = in_array($order->payment_method, ['online', 'bank_transfer'])
                    ? 'refund_in_processing'
                    : 'failed';
            } else {
                $order->payment_status = 'failed';
            }

            $order->save();

            // Hoàn kho nếu đơn đã từng trừ kho
            if (
                $order->payment_method === 'cod' ||
                in_array($order->payment_method, ['online', 'bank_transfer']) && $originalPaymentStatus === 'completed'
            ) {
                $this->restoreStockQuantity($order);
            }

            // Gửi thông báo
            $this->createOrderNotificationToClient(
                $order,
                "Đơn hàng #{$order->order_code} đã được huỷ theo yêu cầu của bạn.",
                'Đơn hàng đã được huỷ'
            );

            // broadcast(new OrderStatusUpdated($order));

            return response()->json(['success' => 'Đã xác nhận yêu cầu huỷ đơn hàng.']);
        }

        if ($action === 'reject') {
            // Từ chối yêu cầu huỷ
            $order->cancellation_requested = true; // Giữ nguyên yêu cầu huỷ vì chỉ cho gửi huỷ 1 lần thôi
            $order->cancel_confirmed = true; // Đánh dấu là đã xử lý
            $order->admin_cancel_note = $note ?: 'Admin từ chối yêu cầu huỷ đơn từ khách.';
            $order->save();

            // Gửi thông báo
            $this->createOrderNotificationToClient(
                $order,
                "Yêu cầu huỷ đơn hàng #{$order->order_code} của bạn đã bị từ chối. Lý do: {$order->admin_cancel_note}",
                'Yêu cầu huỷ đơn bị từ chối'
            );

            // broadcast(new OrderStatusUpdated($order));

            return response()->json(['success' => 'Đã từ chối yêu cầu huỷ đơn hàng.']);
        }
    }

    public function updateReturnStatus(Request $request, $id)
    {
        // Lấy yêu cầu trả hàng kèm đơn hàng và người dùng
        $returnRequest = ReturnRequest::with('order.user')->findOrFail($id);

        $order = $returnRequest->order;
        $user = $order->user;

        // Trạng thái hiện tại của yêu cầu trả hàng
        $oldStatus = $returnRequest->status;
        $oldLabel = $returnRequest->return_status['label'] ?? 'Không xác định';

        // Trạng thái mới từ request
        $newStatus = $request->input('status');
        $returnRequest->admin_note = trim($request->input('admin_note') ?? 'Yêu cầu trả hàng đã được cập nhật');

        // Nếu là rejected thì bắt buộc phải có lý do
        if ($newStatus === 'rejected' && !$request->filled('admin_note')) {
            return redirect()->back()->with('error', 'Bạn phải nhập lý do từ chối yêu cầu trả hàng.');
        }

        // Lấy nhãn trạng thái mới
        $newLabel = ReturnRequest::getStatusLabelStatic($newStatus)['label'] ?? 'Không xác định';

        // Định nghĩa các bước chuyển trạng thái hợp lệ
        $validTransitions = [
            'requested' => ['approved', 'rejected'],
            'approved' => ['refunded'], // Chỉ cho phép từ approved → refunded
        ];

        // Kiểm tra nếu bước chuyển không hợp lệ
        if (!isset($validTransitions[$oldStatus]) || !in_array($newStatus, $validTransitions[$oldStatus])) {
            return redirect()->back()->with('error', 'Chuyển trạng thái không hợp lệ.');
        }

        // Cập nhật trạng thái mới cho yêu cầu trả hàng
        $returnRequest->status = $newStatus;

        // Xử lý cập nhật trạng thái đơn hàng và thanh toán
        if ($newStatus === 'approved') {
            $returnRequest->admin_note = $request->input('admin_note') ?? 'Yêu cầu trả hàng đã được phê duyệt';
            // Giữ nguyên trạng thái đơn hàng là refund_in_processing
            $order->status = 'refund_in_processing';

            // Nếu đơn dùng phương thức thanh toán online hoặc chuyển khoản
            if (
                in_array($order->payment_method, ['online', 'bank_transfer'])
                && $order->payment_status === 'completed'
            ) {
                $order->payment_status = 'refund_in_processing';
            }
            // Với COD thì giữ nguyên
            $order->save();
        } elseif ($newStatus === 'rejected') {
            $returnRequest->admin_note = $request->input('admin_note') ?? 'Yêu cầu trả hàng bị từ chối';
            // Khi từ chối, chuyển trạng thái đơn hàng thành completed
            $order->status = 'completed';
            // Cập nhật payment_status thành completed nếu đơn hàng đã giao (delivered) trước đó
            if ($order->payment_status === 'pending' && $order->payment_method === 'cod') {
                $order->payment_status = 'completed';
            }
            $order->save();
        } elseif ($newStatus === 'refunded') {
            $returnRequest->admin_note = $request->input('admin_note') ?? 'Yêu cầu trả hàng đã hoàn tất và tiền đã được hoàn lại';
            // Chuyển trạng thái đơn hàng thành refunded
            $order->status = 'refunded';

            // Chỉ cho phép cập nhật 'refunded' nếu phương thức có hoàn tiền
            if (
                in_array($order->payment_method, ['online', 'bank_transfer'])
                && $order->payment_status === 'refund_in_processing'
            ) {
                $order->payment_status = 'refunded';
            }
            // Với COD: không cập nhật payment_status vì không qua hệ thống
            if ($order->payment_method === 'cod') {
                $returnRequest->admin_note = $request->input('admin_note') ?? 'Yêu cầu trả hàng đã hoàn tất.';
            }
            $order->save();
        }

        // Lưu thay đổi yêu cầu trả hàng
        $returnRequest->save();

        // Gửi thông báo đến người dùng
        try {
            Notification::create([
                'user_id'   => $user->id,
                'title'     => 'Cập nhật yêu cầu trả hàng',
                'message'   => "Yêu cầu trả hàng của đơn #{$order->order_code} đã được cập nhật từ '{$oldLabel}' thành '{$newLabel}'. Lý do: {$returnRequest->admin_note}",
                'type'      => 'order',
                'is_read'   => false,
                'order_id'  => $order->id,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi thông báo cập nhật trả hàng: ' . $e->getMessage());
        }

        // Phát sự kiện cập nhật trạng thái đơn hàng
        broadcast(new OrderStatusUpdated($order));

        return redirect()->back()->with('success', 'Cập nhật trạng thái trả hàng thành công.');
    }

    public function createOrderNotificationToClient(Order $order, $message, $title)
    {
        try {
            $client = $order->user; // Người đã đặt đơn

            // Kiểm tra nếu người dùng không tồn tại
            if (!$client) {
                Log::warning("Đơn hàng #{$order->id} không có người dùng liên kết.");
                return;
            }

            // Tạo thông báo cho người dùng
            Notification::create([
                'user_id'    => $client->id,
                'title'      => $title,
                'message'    => $message,
                'type'       => 'order',
                'is_read'    => false,
                'order_id'   => $order->id,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo thông báo đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Hoàn trả số lượng sản phẩm về kho khi hủy đơn hàng
     */
    private function restoreStockQuantity(Order $order)
    {
        foreach ($order->orderDetails as $item) {
            $productVariant = $item->productVariant;
            if ($productVariant) {
                $productVariant->increment('stock_quantity', $item->quantity);
            }
        }
    }

    private function createOrderNotification(Order $order, string $message, string $title = 'Cập nhật đơn hàng')
    {
        try {
            $admins = User::where('role_id', 1)->get(); // role_id = 1 là admin

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id'   => $admin->id,                // Admin là người nhận
                    'title'     => $title,                    // Tiêu đề có thể tùy chỉnh
                    'message'   => $message,                  // Nội dung động theo tình huống
                    'type'      => 'order',                   // Phân loại là thông báo đơn hàng
                    'is_read'   => false,                     // Mặc định chưa đọc
                    'order_id'  => $order->id,                // Gắn với ID đơn hàng cụ thể
                    'created_at' => now(),                    // Đảm bảo thời gian chính xác
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi tạo thông báo đơn hàng: ' . $e->getMessage());
        }
    }
}
