<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PaymentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $orders = DB::table('orders')->select('id', 'total_price', 'payment_method')->get();

        foreach ($orders as $order) {
            // 70% đơn hàng có thanh toán
            if ($faker->boolean(70)) {
                DB::table('payments')->insert([
                    'order_id' => $order->id,
                    'method' => $order->payment_method,
                    'amount' => $order->total_price,
                    'status' => $faker->randomElement(['pending', 'completed', 'failed']),
                    'transaction_id' => $faker->optional()->uuid(),
                    'paid_at' => $faker->optional()->dateTimeBetween('-1 month', 'now'),
                    'payment_gateway' => $order->payment_method === 'online' ? $faker->randomElement(['PayPal', 'Stripe', 'VNPay']) : null,
                ]);
            }
        }
    }
}
