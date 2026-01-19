<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Các danh mục quần áo phổ biến
        $fixedCategories = [
            'Áo thun',
            'Quần jean',
            'Đầm váy',
            'Áo khoác',
            'Giày dép',
            'Phụ kiện',
            'Quần short',
            'Áo sơ mi',
            'Đồ thể thao',
            'Túi xách',
        ];

        foreach ($fixedCategories as $name) {
            DB::table('categories')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tạo thêm 5 danh mục ngẫu nhiên (nếu muốn)
        for ($i = 0; $i < 5; $i++) {
            $name = ucfirst($faker->unique()->word());
            DB::table('categories')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'status' => $faker->randomElement(['active', 'inactive']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
