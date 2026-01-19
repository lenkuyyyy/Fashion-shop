<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_variant_id
 * @property int $quantity
 * @property string $price
 * @property string|null $import_price Giá nhập tại thời điểm tạo đơn hàng
 * @property string $discount
 * @property string $subtotal
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\ProductVariant $productVariant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail whereImportPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderDetail whereSubtotal($value)
 * @mixin \Eloquent
 */
class OrderDetail extends Model
{
    //
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'import_price', // Thêm cột import_price
        'price',
        'discount',
        'subtotal'
    ];

    public $timestamps = false; // OrderDetail không có timestamps

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
