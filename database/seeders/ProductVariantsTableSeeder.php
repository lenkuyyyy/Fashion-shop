<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ProductVariantsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $productIds = DB::table('products')->pluck('id')->toArray();

        $colors = ['Đỏ', 'Xanh', 'Đen', 'Trắng', 'Vàng', 'Hồng', 'Xám'];
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        foreach ($productIds as $productId) {
            // Tạo từ 1 đến 4 biến thể cho mỗi sản phẩm
            $variantCount = rand(1, 4);

            for ($i = 0; $i < $variantCount; $i++) {
                $color = $faker->randomElement($colors);
                $size = $faker->randomElement($sizes);
                $sku = strtoupper(Str::random(8));

                DB::table('product_variants')->insert([
                    'product_id' => $productId,
                    'color' => $color,
                    'size' => $size,
                    'sku' => $sku,
                    'price' => $faker->randomFloat(2, 50, 500), // giá từ 50 đến 500
                    'stock_quantity' => $faker->numberBetween(0, 100),
                    'image' => 'products/variants/' . $faker->image('public/storage/products/variants', 400, 400, null, false),
                    'status' => $faker->randomElement(['active', 'inactive']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
