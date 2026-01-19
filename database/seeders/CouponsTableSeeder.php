<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $startDate = Carbon::now()->subDays(rand(1, 10));
            $endDate = (clone $startDate)->addDays(rand(10, 30));
            $discountType = $faker->randomElement(['percent', 'fixed']);
            $discountValue = $discountType === 'percent' ? $faker->numberBetween(5, 50) : $faker->randomFloat(2, 10, 100);

            DB::table('coupons')->insert([
                'code' => strtoupper(Str::random(8)),
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'min_order_value' => $faker->randomFloat(2, 50, 200),
                'max_discount' => $discountType === 'percent' ? $faker->randomFloat(2, 20, 100) : null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usage_limit' => $faker->numberBetween(10, 100),
                'user_usage_limit' => $faker->numberBetween(1, 5),
                'used_count' => 0,
                'applicable_categories' => null, // Hoặc có thể json_encode một số category ids nếu muốn
                'applicable_products' => null,   // Hoặc json_encode một số product ids nếu muốn
                'status' => $faker->randomElement(['active', 'inactive']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
