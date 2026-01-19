<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'permissions' => json_encode(['manage_users', 'manage_products', 'view_reports']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'customer',
                'permissions' => json_encode(['view_products', 'place_orders']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
