<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $title
 * @property string|null $image
 * @property string|null $description
 * @property int $order
 * @property bool $status
 * @property int|null $news_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $views
 * @property-read string $image_url
 * @property-read \App\Models\News|null $news
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereNewsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide whereViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slide withoutTrashed()
 * @mixin \Eloquent
 */
class Slide extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'image',
        'description',
        'order',
        'status',
        'news_id',
        'views'
    ];

    /**
     * Tự động chuyển đổi kiểu dữ liệu.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean', // Tự động chuyển 0/1 trong CSDL thành false/true
    ];

    /**
     * Định nghĩa quan hệ với News.
     */
    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    /**
     * Tạo một thuộc tính ảo để lấy URL đầy đủ của ảnh.
     * Cách dùng: $slide->image_url
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        // Kiểm tra xem slide có ảnh và file có tồn tại không
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            // Trả về URL chính xác
            return Storage::url($this->image);
        }

        // Nếu không có ảnh, trả về ảnh mặc định
        return 'https://via.placeholder.com/1200x500.png?text=Slide+Image';
    }
}