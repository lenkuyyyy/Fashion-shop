<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BrandsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Các thương hiệu thời trang phổ biến (ví dụ)
        $fixedBrands = [
            'Nike',
            'Adidas',
            'Zara',
            'H&M',
            'Uniqlo',
            'Puma',
            'Levi\'s',
            'The North Face',
            'Gucci',
            'Calvin Klein',
        ];

        foreach ($fixedBrands as $name) {
            DB::table('brands')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Thêm 5 thương hiệu ngẫu nhiên (nếu cần)
        for ($i = 0; $i < 5; $i++) {
            $name = ucfirst($faker->unique()->company);
            DB::table('brands')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'status' => $faker->randomElement(['active', 'inactive']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
