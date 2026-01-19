<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Thứ tự gọi seeder theo quan hệ FK (bảng cha trước, bảng con sau)
    $this->call([
        RolesTableSeeder::class,         
        CategoriesTableSeeder::class,
        BrandsTableSeeder::class,
        UsersTableSeeder::class,
        ShippingAddressesTableSeeder::class,
        CouponsTableSeeder::class,
        ProductsTableSeeder::class,
        ProductVariantsTableSeeder::class,
        OrdersTableSeeder::class,
        OrderDetailsTableSeeder::class,
        PaymentsTableSeeder::class,
        WishlistsTableSeeder::class,
        CartsTableSeeder::class,
        ReviewsTableSeeder::class,
        NotificationsTableSeeder::class,
        CategoryGroupSeeder::class, // Thêm seeder cho CategoryGroup
        ShopAddressSeeder::class, // Thêm seeder cho ShopAddress
    ]);
    }
}
