<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CartsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $userIds = DB::table('users')->pluck('id')->toArray();
        $productVariantIds = DB::table('product_variants')->pluck('id')->toArray();

        // Tạo giỏ hàng cho user đăng nhập
        foreach ($userIds as $userId) {
            $itemsCount = rand(1, 5);
            $variants = $faker->randomElements($productVariantIds, $itemsCount);

            foreach ($variants as $variantId) {
                DB::table('carts')->insert([
                    'user_id' => $userId,
                    'session_id' => null,
                    'product_variant_id' => $variantId,
                    'quantity' => rand(1, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Tạo giỏ hàng cho khách (session_id không null)
        for ($i = 0; $i < 10; $i++) {
            $variants = $faker->randomElements($productVariantIds, rand(1, 4));
            $sessionId = $faker->uuid();

            foreach ($variants as $variantId) {
                DB::table('carts')->insert([
                    'user_id' => null,
                    'session_id' => $sessionId,
                    'product_variant_id' => $variantId,
                    'quantity' => rand(1, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
