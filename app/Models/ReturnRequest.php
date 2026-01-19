<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $return_status
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnRequest whereUserId($value)
 * @mixin \Eloquent
 */
class ReturnRequest extends Model
{
    //
    protected $fillable = ['order_id', 'user_id', 'status', 'reason', 'admin_note'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReturnStatusAttribute()
    {
        $statusLabels = [
            'requested' => [
                'label' => 'Yêu cầu trả hàng đã được gửi. Quý khách vui lòng chờ xử lý.',
                'color' => 'warning',
                'title' => 'Chờ xử lý',
                'icon'  => 'bi-clock' // Đồng hồ chờ
            ],
            'approved'  => [
                'label' => 'Yêu cầu trả hàng của bạn đã được chấp nhận',
                'color' => 'primary',
                'title' => 'Đã chấp nhận',
                'icon'  => 'bi-check-circle' // Chấp nhận
            ],
            'rejected'  => [
                'label' => 'Yêu cầu trả hàng của bạn bị từ chối',
                'color' => 'danger',
                'title' => 'Đã từ chối',
                'icon'  => 'bi-x-circle' // Từ chối
            ],
            'refunded'  => [
                'label' => ($this->order->payment_status === 'pending' && $this->order->payment_method === 'cod')
                    ? 'Đã hoàn tất'
                    : 'Đã hoàn tiền',
                'color' => 'success',
                'title' => ($this->order->payment_status === 'pending' && $this->order->payment_method === 'cod')
                    ? 'Đã hoàn tất'
                    : 'Đã hoàn tiền',
                'icon'  => ($this->order->payment_status === 'pending' && $this->order->payment_method === 'cod')
                    ? 'bi-box-arrow-in-left' // Icon nhận lại hàng
                    : 'bi-cash-coin'         // Icon hoàn tiền
            ]
        ];

        return $statusLabels[$this->status] ?? [
            'label' => 'Không xác định',
            'color' => 'dark',
            'icon'  => 'bi-question-circle'
        ];
    }

    public static function getStatusLabelStatic($status)
    {
        $statusLabels = [
            'requested' => ['label' => 'Đã gửi yêu cầu trả hàng', 'color' => 'warning'],
            'approved'  => ['label' => 'Đã chấp nhận trả hàng',   'color' => 'primary'],
            'rejected'  => ['label' => 'Yêu cầu bị từ chối',      'color' => 'danger'],
            'refunded'  => ['label' => 'Đã hoàn tiền',            'color' => 'success'],
        ];

        return $statusLabels[$status] ?? ['label' => 'Không xác định', 'color' => 'dark'];
    }
}
