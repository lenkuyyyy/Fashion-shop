<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopAddressSeeder extends Seeder
{
    public function run(): void
    {
         $shopAddressId = DB::table('shop_addresses')->insertGetId([
            'name' => 'Kho hàng SHOP 447',
            'phone' => '0985022843',
           'province_id'      => 202,   // Thay bằng mã GHN thực tế của Hà Nội
            'district_id'      => 1450,  // Mã quận Hoài Đức theo GHN
            'ward_code'        => '21010', // Mã xã Vân Cánh theo GHN
            'address_detail'   => '123 Vân Canh, Hoài Đức, Hà Nội',
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
         DB::table('orders')->update(['shop_address_id' => $shopAddressId]);
    }
}
