<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\OrderStatusUpdated;

class Order extends Model
{
    protected $fillable = [
        'shop_address_id',
        'order_code',
        'user_id',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'note',
        'cancellation_requested',
        'cancel_reason',
        'admin_cancel_note',
        'cancel_confirmed',
        'shipping_address_id',
        'shipping_fee',
        'coupon_id',
        'extra_info',
        'vnp_txn_ref',
        'vnp_transaction_no',
        'vnp_response_code',
        'vnp_bank_code',
        'vnp_bank_tran_no',
        'vnp_card_type',
        'vnp_pay_date',
        'vnp_secure_hash',
        'order_discount', 
        'shipping_discount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function returnRequest()
    {
        return $this->hasOne(ReturnRequest::class);
    }

    // Gán hiển thị tiếng Việt và định dạng cho trạng thái đơn hàng
    public function getStatusLabel()
    {
        $statuses = [
            'pending' => ['label' => 'Đang chờ xử lý', 'color' => 'bg-warning'],
            'processing' => ['label' => 'Đang xử lý', 'color' => 'bg-primary'],
            'shipped' => ['label' => 'Đang giao hàng', 'color' => 'bg-info'],
            'delivered' => ['label' => 'Đã giao hàng', 'color' => 'bg-success'],
            'completed' => ['label' => 'Đã hoàn thành', 'color' => 'bg-secondary'],
            'cancelled' => ['label' => 'Đơn đã hủy', 'color' => 'bg-danger'],
            'refund_in_processing' => ['label' => 'Đang xử lý trả hàng', 'color' => 'bg-info'],
            'refunded' => ['label' => 'Đã hoàn tiền', 'color' => 'bg-success'],
        ];

        return $statuses[$this->status] ?? ['label' => 'Không xác định', 'color' => 'bg-secondary'];
    }

    public static function getStatusMeta($status)
    {
        $statuses = [
            'pending' => ['label' => 'Đang chờ xử lý', 'color' => 'bg-warning'],
            'processing' => ['label' => 'Đang xử lý', 'color' => 'bg-primary'],
            'shipped' => ['label' => 'Đang giao hàng', 'color' => 'bg-info'],
            'delivered' => ['label' => 'Đã giao hàng', 'color' => 'bg-success'],
            'completed' => ['label' => 'Đã hoàn thành', 'color' => 'bg-secondary'],
            'cancelled' => ['label' => 'Đơn đã hủy', 'color' => 'bg-danger'],
            'refund_in_processing' => ['label' => 'Đang xử lý trả hàng', 'color' => 'bg-info'],
            'refunded' => ['label' => 'Đã hoàn tiền', 'color' => 'bg-success'],
        ];

        return $statuses[$status] ?? ['label' => 'Không xác định', 'color' => 'bg-secondary'];
    }

    // Gán hiển thị tiếng Việt và định dạng cho phương thức thanh toán
    public function getPaymentMethod($paymentMethod)
    {
        $methods = [
            'cod' => ['label' => 'Thanh toán khi nhận hàng', 'color' => '#CC6666'],
            'online' => ['label' => 'Thanh toán trực tuyến', 'color' => '#6699CC'],
            'bank_transfer' => ['label' => 'Thanh toán qua ngân hàng', 'color' => '#CC66CC'],
        ];

        return $methods[$paymentMethod] ?? ['label' => 'Không xác định'];
    }

    // Gán hiển thị tiếng Việt và định dạng cho trạng thái thanh toán
    public function getPaymentStatus($paymentStatus)
    {
        $paymentStatuses = [
            'pending' => ['label' => 'Chờ thanh toán', 'color' => '#FF9966'],
            'completed' => ['label' => 'Đã thanh toán', 'color' => '#009900'],
            'refund_in_processing' => ['label' => 'Đang xử lý hoàn tiền', 'color' => '#6699FF'],
            'refunded' => ['label' => 'Đã hoàn tiền', 'color' => '#33CC99'],
            'failed' => ['label' => 'Thanh toán thất bại', 'color' => '#666666'],
        ];

        return $paymentStatuses[$paymentStatus] ?? ['label' => 'Không xác định'];
    }

    protected $dispatchesEvents = [
        'updated' => OrderStatusUpdated::class,
    ];
}