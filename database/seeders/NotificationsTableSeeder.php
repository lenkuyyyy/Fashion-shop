<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;



class NotificationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $userIds = DB::table('users')->pluck('id')->toArray();
        $orderIds = DB::table('orders')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            // Mỗi user có thể có 1-5 thông báo
            $notificationCount = rand(1, 5);



            for ($i = 0; $i < $notificationCount; $i++) {
                DB::table('notifications')->insert([
                    'user_id' => $userId,
                    'title' => $faker->sentence(3),
                    'message' => $faker->paragraph(),
                    'type' => $faker->randomElement(['email', 'push', 'system']),
                    'is_read' => $faker->boolean(50),
                    'order_id' => $faker->optional()->randomElement($orderIds),
                    'created_at' => now(),
                ]);
            }
        }
    }
}
