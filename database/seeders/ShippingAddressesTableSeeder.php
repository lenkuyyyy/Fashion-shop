<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ShippingAddressesTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy danh sách user_id trong db
        $userIds = DB::table('users')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            // Mỗi user tạo 1-3 địa chỉ giao hàng ngẫu nhiên
            $count = rand(1, 3);

            for ($i = 0; $i < $count; $i++) {
                DB::table('shipping_addresses')->insert([
                    'user_id' => $userId,
                    'name' => $faker->name,
                    'phone_number' => $faker->phoneNumber,
                    'address' => $faker->streetAddress,
                    'ward' => $faker->citySuffix,
                    'district' => $faker->city,
                    'city' => $faker->state,
                    'is_default' => $i === 0, // địa chỉ đầu tiên là mặc định
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
