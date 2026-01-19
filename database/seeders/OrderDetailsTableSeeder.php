<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrderDetailsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $orderIds = DB::table('orders')->pluck('id')->toArray();
        $productVariantIds = DB::table('product_variants')->pluck('id')->toArray();

        foreach ($orderIds as $orderId) {
            // Mỗi order có 1-5 chi tiết
            $detailsCount = rand(1, 5);

            for ($i = 0; $i < $detailsCount; $i++) {
                $productVariantId = $faker->randomElement($productVariantIds);
                $quantity = rand(1, 5);
                $price = DB::table('product_variants')->where('id', $productVariantId)->value('price');
                $discount = $faker->randomFloat(2, 0, $price * 0.3); // tối đa giảm 30%
                $subtotal = ($price - $discount) * $quantity;

                DB::table('order_details')->insert([
                    'order_id' => $orderId,
                    'product_variant_id' => $productVariantId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ]);
            }
        }
    }
}
