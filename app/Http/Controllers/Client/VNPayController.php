<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VNPayController extends Controller
{
    /**
     * Hiển thị trang test thanh toán (ví dụ form hoặc thông tin demo)
     */
    public function testPayment()
    {
        return view('vnpay.test'); // Tạo view vnpay/test.blade.php nếu cần
    }

    /**
     * Xử lý IPN (Instant Payment Notification) từ VNPAY
     */
    public function ipn(Request $request)
    {
        $queryRaw      = $request->server('QUERY_STRING');
        $vnpHashSecret = env('VNPAY_HASH_SECRET');

        // Lấy params nguyên gốc, không decode
        $params = [];
        foreach (explode('&', $queryRaw) as $pair) {
            if (stripos($pair, 'vnp_SecureHash=') === 0 || stripos($pair, 'vnp_SecureHashType=') === 0) {
                continue;
            }
            list($k, $v) = explode('=', $pair, 2);
            $params[$k]   = $v;
        }

        ksort($params);
        $hashData      = implode('&', array_map(fn($k, $v) => "$k=$v", array_keys($params), $params));
        $calculatedHash = hash_hmac('sha512', $hashData, $vnpHashSecret);
        $receivedHash  = $request->get('vnp_SecureHash');

        Log::info('VNPAY IPN RawQuery: ' . $queryRaw);
        Log::info('VNPAY IPN HashData: ' . $hashData);
        Log::info('VNPAY IPN CalcHash: ' . $calculatedHash);
        Log::info('VNPAY IPN RecvHash: ' . $receivedHash);

        if ($calculatedHash !== $receivedHash) {
            return response()->json(['RspCode' => '97', 'Message' => 'Checksum Fail'], 400);
        }

        $order = Order::where('vnp_txn_ref', $params['vnp_TxnRef'])->first();
        if (! $order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found'], 404);
        }

        if ($params['vnp_ResponseCode'] === '00') {
            $order->update([
                'status'         => 'processing',
                'payment_status' => 'completed',
            ]);
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        $order->update([
            'status'         => 'cancelled',
            'payment_status' => 'failed',
        ]);
        
        return response()->json(['RspCode' => '01', 'Message' => 'Confirm Fail']);
    }

    /**
     * Xử lý callback trả về từ VNPAY (redirect từ cổng)
     */
    public function paymentReturn(Request $request)
    {
        $queryRaw      = $request->server('QUERY_STRING');
        $vnpHashSecret = env('VNPAY_HASH_SECRET');

        $params = [];
        foreach (explode('&', $queryRaw) as $pair) {
            if (stripos($pair, 'vnp_SecureHash=') === 0 || stripos($pair, 'vnp_SecureHashType=') === 0) {
                continue;
            }
            list($k, $v) = explode('=', $pair, 2);
            $params[$k]   = $v;
        }

        ksort($params);
        $hashData       = implode('&', array_map(fn($k, $v) => "$k=$v", array_keys($params), $params));
        $calculatedHash = hash_hmac('sha512', $hashData, $vnpHashSecret);
        $receivedHash   = $request->get('vnp_SecureHash');

        Log::info('VNPAY Return RawQuery: ' . $queryRaw);
        Log::info('VNPAY Return HashData: ' . $hashData);
        Log::info('VNPAY Return CalcHash: ' . $calculatedHash);
        Log::info('VNPAY Return RecvHash: ' . $receivedHash);

        if ($calculatedHash !== $receivedHash) {
            session()->flash('payment_error', 'Invalid checksum');
            return redirect()->route('order.failed');
        }

        $order = Order::where('vnp_txn_ref', $params['vnp_TxnRef'])->first();
        if (! $order) {
            session()->flash('payment_error', 'Order not found');
            return redirect()->route('order.failed');
        }

        if ($params['vnp_ResponseCode'] === '00') {
            $order->update([
                'status'             => 'processing',
                'payment_status'     => 'completed',
                'vnp_transaction_no' => $params['vnp_TransactionNo'],
                'vnp_response_code'  => $params['vnp_ResponseCode'],
                'vnp_bank_code'      => $params['vnp_BankCode'],
                'vnp_bank_tran_no'   => $params['vnp_BankTranNo'] ?? null,
                'vnp_card_type'      => $params['vnp_CardType'] ?? null,
                'vnp_pay_date'       => date('Y-m-d H:i:s', strtotime($params['vnp_PayDate'])),
            ]);

            // ✅ Trừ hàng tồn kho
            foreach ($order->orderDetails as $item) {
                $product = $item->productVariant;
                if ($product && $product->stock_quantity >= $item->quantity) {
                    $product->decrement('stock_quantity', $item->quantity);
                }
            }

            // Xóa giỏ hàng
            Cart::where('user_id', $order->user_id)
                ->whereIn('product_variant_id', $order->orderDetails->pluck('product_variant_id'))
                ->delete();
            session()->forget(['pending_cart_item_ids', 'pending_order_id']);

            return redirect()->route('order.success', $order->id);
        }

        $order->update([
            'status'         => 'cancelled',
            'payment_status' => 'failed',
        ]);
        session()->flash('payment_error', 'Thanh toán không thành công');
        return redirect()->route('order.failed');
    }

    /**
     * Trang thành công
     */
    public function success(Order $order)
    {
        return view('client.pages.order-success', ['order' => $order]);
    }

    /**
     * Trang thất bại
     */
    public function failed()
    {
        $error = session('payment_error', 'Thanh toán không thành công. Vui lòng thử lại.');
        return view('client.pages.order-failed', ['error' => $error]);
    }
}
