<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class WishlistsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $userIds = DB::table('users')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            // Mỗi user có 1-5 sản phẩm yêu thích
            $wishlistCount = rand(1, 5);
            $wishlistProductIds = $faker->randomElements($productIds, $wishlistCount);

            foreach ($wishlistProductIds as $productId) {
                DB::table('wishlists')->insert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
