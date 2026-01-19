<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryGroup extends Model
{
    protected $fillable = ['name'];

    public function categories()
    {
        return $this->hasMany(Category::class, 'group_id');
    }
}
