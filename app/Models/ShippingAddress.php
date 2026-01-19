<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $phone_number
 * @property string $address
 * @property string|null $ward
 * @property string|null $district
 * @property string|null $city
 * @property int $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereWard($value)
 * @mixin \Eloquent
 */
class ShippingAddress extends Model
{
    //
    protected $fillable = [
        'user_id',
        'name',
        'full_name',
        'phone_number',
        'address',
        'ward',
        'district',
        'city',
        'full_address',
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->ward . ', ' . $this->district . ', ' . $this->city;
    }
}
