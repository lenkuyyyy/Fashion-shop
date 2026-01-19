<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'min_order_value', 'max_discount',
        'start_date', 'end_date', 'usage_limit', 'user_usage_limit',
        'used_count', 'applicable_categories', 'applicable_products', 'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
    ];

    /**
     * [CẬP NHẬT] - Bỏ phần thập phân cho tất cả định dạng tiền tệ.
     */
    public function getFormattedValueAttribute(): string
    {
        switch ($this->discount_type) {
            case 'percent':
                return (int)$this->discount_value . '%';
            case 'fixed':
                return number_format($this->discount_value, 0, ',', '.') . ' ₫';
            case 'free_shipping':
                return 'Miễn phí vận chuyển';
            case 'fixed_shipping':
                return 'Giảm ' . number_format($this->discount_value, 0, ',', '.') . ' ₫ ship';
            default:
                return (string)(int)$this->discount_value;
        }
    }

    /**
     * [MỚI] - Thêm accessor để định dạng cho đơn hàng tối thiểu.
     */
    public function getFormattedMinOrderValueAttribute(): string
    {
        return $this->min_order_value ? number_format($this->min_order_value, 0, ',', '.') . ' ₫' : 'Không có';
    }

    public function getFriendlyDiscountTypeAttribute(): string
    {
        return match ($this->discount_type) {
            'percent' => 'Phần trăm',
            'fixed' => 'Cố định (₫)',
            'free_shipping' => 'Miễn phí vận chuyển',
            'fixed_shipping' => 'Giảm giá vận chuyển',
            default => 'Không xác định',
        };
    }
}