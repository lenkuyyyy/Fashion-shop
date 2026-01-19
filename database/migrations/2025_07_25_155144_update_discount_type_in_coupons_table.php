<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sử dụng DB::statement để thay đổi ENUM một cách an toàn
        DB::statement("ALTER TABLE coupons MODIFY COLUMN discount_type ENUM('percent', 'fixed', 'free_shipping', 'fixed_shipping') NOT NULL");
    }

    public function down(): void
    {
        // Quay lại trạng thái cũ nếu cần
        DB::statement("ALTER TABLE coupons MODIFY COLUMN discount_type ENUM('percent', 'fixed') NOT NULL");
    }
};