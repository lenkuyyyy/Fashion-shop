<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\OrderStatusUpdated;

class OrderController extends Controller
{
    // hàm lấy danh sách đơn hàng của người dùng
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này.');
        }

        // Lấy danh sách đơn hàng của người dùng
        $orders = Order::with('orderDetails.productVariant.product', 'coupon', 'returnRequest')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        // Duyệt qua từng đơn hàng để tính discount (nếu có coupon)
        foreach ($orders as $order) {
            $discount = 0;
            // Tính subtotal (chưa bao gồm phí ship)
            $subtotal = $order->orderDetails->sum('subtotal');

            if ($order->coupon) {
                if ($order->coupon->discount_type === 'fixed') {
                    $discount = $order->coupon->discount_value;
                } elseif ($order->coupon->discount_type === 'percent') {
                    $discount = round($subtotal * $order->coupon->discount_value / 100);
                }
            }

            // Gán giảm giá tạm thời vào order (không cần lưu DB)
            $order->calculated_discount = $discount;

            // Đừng gán lại $order->total, hãy dùng biến phụ nếu cần
            $order->subtotal_display = $subtotal;

            // Tính tổng tiền cuối cùng sau khi giảm giá
            $order->final_price = $order->total_price;
        }
         $totalOrderCount = Order::where('user_id', auth()->id())->count();
        return view('client.pages.orders', compact('user', 'orders', 'totalOrderCount'));
    }
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            \Log::error('CURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public function createOrderCancelNotificationToAdmin(Request $request, $id, string $title = 'Cập nhật đơn hàng')
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không tồn tại.'
            ], 404);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này không thể hủy nữa.'
            ], 400);
        }

        if ($order->cancellation_requested) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã gửi yêu cầu hủy đơn hàng này trước đó. Vui lòng chờ admin xử lý.'
            ], 400);
        }

        $reason = $request->cancel_reason;
        if (empty($reason)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập lý do hủy đơn hàng.'
            ], 400);
        }

        try {
            $order->cancel_reason = $reason;
            $order->cancellation_requested = true;
            $order->cancel_confirmed = false;
            if (
                !empty($order->vnp_txn_ref) &&
                $order->payment_method === 'online' &&
                $order->payment_status === 'pending' &&
                $order->status === 'pending'
            ) {
                $order->payment_status = 'failed';
            }
            $order->save();

            $admins = User::where('role_id', 1)->get();
            if ($admins->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có admin nào để gửi thông báo.'
                ], 400);
            }

            $message = "Người dùng {$order->user->name} yêu cầu hủy đơn hàng #{$order->order_code} với lý do: {$order->cancel_reason}. Vui lòng kiểm tra và xử lý.";

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id'   => $admin->id,
                    'title'     => $title,
                    'message'   => $message,
                    'type'      => 'order',
                    'is_read'   => false,
                    'order_id'  => $order->id,
                    'created_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi yêu cầu hủy đơn hàng thành công. Vui lòng chờ admin xử lý.'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo yêu cầu hủy đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function pay(Request $request)
    {
        $orderId = session('pending_order_id') ?? $request->input('order_id');
        if (!$orderId) {
            Log::warning('No pending_order_id found in session');
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng để thanh toán.');
        }

        $order = Order::with(['orderDetails.productVariant.product', 'shippingAddress'])
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->where('payment_method', 'online')
            ->first();

        if (!$order) {
            Log::warning('Order not found or invalid', [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'payment_method' => $order ? $order->payment_method : 'not_found'
            ]);
            return redirect()->route('cart.index')->with('error', 'Đơn hàng không hợp lệ hoặc không tồn tại.');
        }

        $subtotal = $order->orderDetails->sum(function ($detail) {
            return $detail->price * $detail->quantity;
        });

        $shippingFee = $order->shipping_fee;
        $total = $order->total_price;

        $cartItems = Cart::where('user_id', Auth::id())->get();
        \Log::info('Cart items after redirect to pay: ' . $cartItems->toJson());
        \Log::info('Order details for pay', [
            'order_id' => $orderId,
            'total_price' => $order->total_price,
            'calculated_total' => $total
        ]);

        return view('client.pages.pay', [
            'order' => $order,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'total' => $total,
        ]);
    }

    public function momo_payment(Request $request)
    {
        $orderId = session('pending_order_id') ?? $request->input('order_id');
        if (!$orderId) {
            \Log::warning('No pending_order_id found in session');
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng để thanh toán.');
        }

        $order = Order::findOrFail($orderId);
        if ($order->user_id !== Auth::id() || $order->payment_method !== 'online') {
            \Log::warning('Invalid order or payment method', [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'payment_method' => $order->payment_method
            ]);
            return redirect()->route('cart.index')->with('error', 'Đơn hàng không hợp lệ.');
        }

        // Validate total_momo
        $request->validate([
            'total_momo' => 'required|numeric|min:1000', // Momo yêu cầu tối thiểu 1000 VND
        ], [
            'total_momo.required' => 'Số tiền thanh toán không được để trống.',
            'total_momo.numeric' => 'Số tiền thanh toán phải là số.',
            'total_momo.min' => 'Số tiền thanh toán phải lớn hơn hoặc bằng 1000 VND.',
        ]);

        $amount = (int) $request->input('total_momo'); // Ép kiểu thành số nguyên
        \Log::info('Momo payment amount check', [
            'total_momo' => $request->input('total_momo'),
            'amount' => $amount,
            'order_total_price' => $order->total_price
        ]);

        if (abs($amount - (int)$order->total_price) > 0) {
            \Log::warning('Momo payment amount mismatch', [
                'order_id' => $orderId,
                'total_momo' => $amount,
                'order_total_price' => $order->total_price
            ]);
            return redirect()->route('cart.index')->with('error', 'Số tiền thanh toán không khớp với đơn hàng.');
        }

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán đơn hàng #{$order->order_code}";
        $orderIdMomo = $order->order_code . '-' . time();
        $requestId = $order->order_code . '-' . time() . '-req';
        $redirectUrl = url('/account');
        $ipnUrl = url('/momo_callback');
        $extraData = "";
        $requestType = 'payWithATM';

        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderIdMomo&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'Test',
            'storeId' => 'MomoTestStore',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderIdMomo,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        try {
            \Log::info('Momo request data', $data);
            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);

            \Log::info('Momo payment response', ['response' => $jsonResult]);

            if (isset($jsonResult['payUrl']) && $jsonResult['resultCode'] == 0) {
                // Cập nhật trạng thái đơn hàng
                $order->update([
                    'payment_status' => 'completed',
                    'status' => 'processing',
                ]);

                // Xóa các sản phẩm được chọn trong giỏ hàng
                $cartItemIds = session('pending_cart_item_ids', []);
                if (!empty($cartItemIds)) {
                    \Log::info('Deleting cart items for Momo payment', [
                        'user_id' => Auth::id(),
                        'cart_item_ids' => $cartItemIds
                    ]);
                    Cart::where('user_id', Auth::id())
                        ->whereIn('id', $cartItemIds)
                        ->delete();

                    // Xóa session
                    session()->forget(['pending_cart_item_ids', 'pending_order_id']);
                } else {
                    \Log::warning('No cart_item_ids found in session for deletion', ['order_id' => $orderId]);
                }

                \Log::info('Momo payment successful, cart cleared', ['order_id' => $orderId, 'payUrl' => $jsonResult['payUrl']]);
                return redirect($jsonResult['payUrl']);
            }

            \Log::error('Momo payment initiation failed', ['response' => $jsonResult]);
            return redirect()->route('cart.index')->with('error', 'Không thể khởi tạo thanh toán Momo: ' . ($jsonResult['message'] ?? 'Lỗi không xác định'));
        } catch (\Exception $e) {
            \Log::error('Momo payment error: ' . $e->getMessage(), ['order_id' => $orderId]);
            return redirect()->route('cart.index')->with('error', 'Lỗi khi xử lý thanh toán Momo: ' . $e->getMessage());
        }
    }

    public function momoCallback(Request $request)
    {
        $orderId = $request->input('orderId');
        if (!$orderId) {
            \Log::warning('No orderId in Momo callback', ['request' => $request->all()]);
            return response()->json(['error' => 'Invalid order']);
        }

        // Tìm đơn hàng dựa trên orderId (loại bỏ phần timestamp)
        $orderCode = explode('-', $orderId)[0];
        $order = Order::where('order_code', $orderCode)->first();

        if (!$order) {
            \Log::warning('Order not found in Momo callback', ['order_id' => $orderId]);
            return response()->json(['error' => 'Order not found']);
        }

        $resultCode = $request->input('resultCode', 1);
        if ($resultCode == 0) {
            // Cập nhật trạng thái đơn hàng
            $order->update([
                'payment_status' => 'completed',
                'status' => 'processing',
            ]);

            // Xóa giỏ hàng (nếu chưa xóa)
            $cartItemIds = session('pending_cart_item_ids', []);
            if (!empty($cartItemIds)) {
                \Log::info('Deleting cart items for Momo callback', [
                    'user_id' => $order->user_id,
                    'cart_item_ids' => $cartItemIds
                ]);
                Cart::where('user_id', $order->user_id)
                    ->whereIn('id', $cartItemIds)
                    ->delete();
                session()->forget(['pending_cart_item_ids', 'pending_order_id']);
            }

            \Log::info('Momo payment confirmed via callback', ['order_id' => $orderId]);
            return response()->json(['message' => 'Payment confirmed']);
        }

        \Log::warning('Momo payment failed via callback', ['order_id' => $orderId, 'result' => $request->all()]);
        return response()->json(['error' => 'Payment failed']);
    }

    public function received(Request $request, $id)
    {
        $user = Auth::user();
        $order = Order::findOrFail($id);

        // Kiểm tra quyền sở hữu đơn hàng
        if ($order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xác nhận đơn hàng này.'
            ], 403);
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này đã được xác nhận trước đó.'
            ], 400);
        }

        if ($order->status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa ở trạng thái đã giao!'
            ], 400);
        }

        try {
            // Cập nhật trạng thái
            $order->status = 'completed';
            $order->payment_status = 'completed';
            $order->save();

            // Gửi sự kiện cập nhật trạng thái
            Log::info('Broadcasting OrderStatusUpdated event for received order', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'channel' => 'orders.' . $order->user_id,
                'status' => $order->status,
            ]);
            broadcast(new OrderStatusUpdated($order));

            // Tạo thông báo cho khách hàng
            $this->createOrderNotificationToClient(
                $order,
                "Đơn hàng #{$order->order_code} đã được xác nhận hoàn thành vào lúc " . now()->format('H:i d/m/Y') . ".",
                'Đơn hàng hoàn thành'
            );

            // Tạo thông báo cho admin
            $this->createOrderNotificationToAdmin(
                $order,
                "Khách hàng {$user->name} đã xác nhận nhận hàng cho đơn #{$order->order_code}.",
                'Xác nhận nhận hàng'
            );

            return response()->json([
                'success' => true,
                'message' => "Cảm ơn quý khách! Đơn hàng #{$order->order_code} đã được xác nhận hoàn thành.",
                'order_code' => $order->order_code,
                'status' => $order->status
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại!'
            ], 500);
        }
    }

    private function createOrderNotificationToClient(Order $order, $message, $title)
    {
        try {
            Notification::create([
                'user_id'    => $order->user_id,
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

    private function createOrderNotificationToAdmin(Order $order, $message, $title)
    {
        try {
            $admins = User::where('role_id', 1)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id'    => $admin->id,
                    'title'      => $title,
                    'message'    => $message,
                    'type'       => 'order',
                    'is_read'    => false,
                    'order_id'   => $order->id,
                    'created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi tạo thông báo cho admin: ' . $e->getMessage());
        }
    }

    public function success(Order $order)
    {
        return view('client.pages.order-success', ['order' => $order]);
    }

    public function failed()
    {
        $error = session('payment_error', 'Thanh toán không thành công. Vui lòng thử lại.');
        return view('client.pages.order-failed', ['error' => $error]);
    }
}

