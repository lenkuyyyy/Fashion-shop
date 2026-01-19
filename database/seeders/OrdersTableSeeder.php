<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class OrdersTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $userIds = DB::table('users')->pluck('id')->toArray();
        $shippingAddressIds = DB::table('shipping_addresses')->pluck('id')->toArray();
        $couponIds = DB::table('coupons')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            // Mỗi user tạo 1-5 đơn hàng
            $orderCount = rand(1, 5);

            for ($i = 0; $i < $orderCount; $i++) {
                // Chọn ngẫu nhiên shipping_address của user đó (lọc)
                $userShippingAddresses = DB::table('shipping_addresses')->where('user_id', $userId)->pluck('id')->toArray();
                if (empty($userShippingAddresses)) {
                    continue;
                }
                $shippingAddressId = $faker->randomElement($userShippingAddresses);

                // Chọn coupon ngẫu nhiên hoặc null
                $couponId = $faker->boolean(30) ? $faker->randomElement($couponIds) : null;

                // Tổng giá tạm tính, giả định
                $totalPrice = $faker->randomFloat(2, 100, 5000);

                DB::table('orders')->insert([
                    'order_code' => 'HN' . strtoupper(Str::random(8)),
                    'user_id' => $userId,
                    'total_price' => $totalPrice,
                    'status' => $faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
                    'payment_method' => $faker->randomElement(['cod', 'bank_transfer', 'online']),
                    'payment_status' => $faker->randomElement(['pending', 'completed', 'failed']),
                    'note' => $faker->optional()->sentence(),
                    'shipping_address_id' => $shippingAddressId,
                    'coupon_id' => $couponId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
