<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $image
 * @property string $slug
 * @property bool $status
 * @property \Illuminate\Support\Carbon $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $views
 * @property-read string $image_url
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News withoutTrashed()
 * @mixin \Eloquent
 */
class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'image',
        'slug',
        'status',
        'published_at',
        'views'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'status' => 'boolean',
    ];

    /**
     * Lấy URL đầy đủ của ảnh.
     * Thuộc tính này sẽ được gọi bằng: $news->image_url
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        // Kiểm tra xem bài viết có ảnh và file ảnh có thực sự tồn tại trong storage không
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            // Nếu có, trả về URL đầy đủ đến file
            return Storage::url($this->image);
        }

        // Nếu không có ảnh, trả về một ảnh placeholder mặc định
        return 'https://via.placeholder.com/800x600.png?text=No+Image';
    }
}