<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;


/**
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property int $brand_id
 * @property string $sku
 * @property string|null $thumbnail
 * @property string|null $description
 * @property string|null $short_description
 * @property string $slug
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Brand $brand
 * @property-read Category $category
 * @property-read string $price_range
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductVariant> $variants
 * @property-read int|null $variants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wishlist> $wishlists
 * @property-read int|null $wishlists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'brand_id',
        'sku',
        'thumbnail',
        'description',
        'short_description',
        'slug',
        'status',

    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Quan hệ với model Category

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderDetails()
    {
        return $this->hasManyThrough(
            \App\Models\OrderDetail::class,
            \App\Models\ProductVariant::class,
            'product_id', // Khóa ngoại trên bảng product_variants
            'product_variant_id', // Khóa ngoại trên bảng order_details
            'id', // Khóa chính trên bảng products
            'id'  // Khóa chính trên bảng product_variants
        );
    }

    /**
     * Lấy khoảng giá của sản phẩm dựa trên các biến thể (variants).
     *
     * - Nếu sản phẩm có biến thể:
     *     + Lấy giá thấp nhất và cao nhất trong các biến thể.
     *     + Nếu giống nhau → hiển thị 1 mức giá, ví dụ: "100.000 VNĐ".
     *     + Nếu khác nhau → hiển thị khoảng giá, ví dụ: "100.000 - 200.000 VNĐ".
     * - Nếu không có biến thể → trả về "Liên hệ".
     *
     * @return string Chuỗi định dạng giá, có phân cách hàng nghìn và đơn vị VNĐ.
     */
    public function getPriceRangeAttribute()
    {
        if ($this->variants()->exists()) {
            // Lấy giá thấp nhất từ các biến thể
            $minPrice = $this->variants()->min('price');

            // Lấy giá cao nhất từ các biến thể
            $maxPrice = $this->variants()->max('price');

            // Định dạng giá theo dạng 100.000 VNĐ 
            $format = fn($price) => number_format($price, 0, ',', '.') . ' VNĐ';

            // Nếu giá thấp nhất và cao nhất giống nhau, trả về một mức giá
            // Nếu khác nhau, trả về khoảng giá
            // Ví dụ: "100.000 VNĐ" hoặc "100.000 - 200.000 VNĐ"
            return $minPrice == $maxPrice
                ? $format($minPrice)
                : $format($minPrice) . ' - ' . $format($maxPrice);
        }

        // Trả về "Liên hệ" nếu không có biến thể
        return 'Liên hệ';
    }

    
}
