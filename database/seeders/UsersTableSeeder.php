<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1 Admin cố định
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone_number' => '0900000000',
            'password' => Hash::make('admin123'),
            'status' => 'active',
            'email_verified_at' => now(),
            'role_id' => 1, // admin
        ]);

        // 10 khách hàng ngẫu nhiên
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'password' => Hash::make('password'),
                'status' => $faker->randomElement(['active', 'inactive', 'banned']),
                'email_verified_at' => $faker->boolean(80) ? now() : null,
                'reset_password_token' => null,
                'reset_password_expires_at' => null,
                'role_id' => 2, // customer
            ]);
        }
    }
}
