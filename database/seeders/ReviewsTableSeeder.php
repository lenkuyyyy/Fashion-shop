<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ReviewsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $userIds = DB::table('users')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            // Mỗi user tạo 1-3 đánh giá sản phẩm
            $reviewCount = rand(1, 3);
            $reviewedProducts = $faker->randomElements($productIds, $reviewCount);

            foreach ($reviewedProducts as $productId) {
                DB::table('reviews')->insert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'rating' => rand(1, 5),
                    'comment' => $faker->optional()->sentence(),
                    'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
