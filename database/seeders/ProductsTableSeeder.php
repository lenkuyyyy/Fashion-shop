<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy danh sách category_id và brand_id có sẵn trong db
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        $brandIds = DB::table('brands')->pluck('id')->toArray();

        // Tạo 20 sản phẩm mẫu
        for ($i = 0; $i < 20; $i++) {
            $name = ucfirst($faker->words(3, true)); // tên sản phẩm ngẫu nhiên
            DB::table('products')->insert([
                'name' => $name,
                'category_id' => $faker->randomElement($categoryIds),
                'brand_id' => $faker->randomElement($brandIds),
                'sku' => strtoupper(Str::random(8)),
                'thumbnail' => 'products/thumbnails/' . $faker->image('public/storage/products/thumbnails', 400, 400, null, false), // giả sử lưu ảnh ở đây
                'description' => $faker->paragraph,
                'short_description' => $faker->sentence,
                'slug' => Str::slug($name . '-' . Str::random(4)),
                'status' => $faker->randomElement(['active', 'inactive', 'out_of_stock']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
