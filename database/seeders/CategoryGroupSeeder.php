<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoryGroup;
class CategoryGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryGroup::insert([
        ['name' => 'Nam'],
        ['name' => 'Nữ'],
        ['name' => 'Trẻ em'],
      
    ]);
    }
}
