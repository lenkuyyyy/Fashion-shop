<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Chỉ thêm cột nếu chưa tồn tại
            if (!Schema::hasColumn('orders', 'shop_address_id')) {
                $table->unsignedBigInteger('shop_address_id')->nullable()->after('shipping_address_id');

                $table->foreign('shop_address_id')
                    ->references('id')
                    ->on('shop_addresses')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shop_address_id')) {
                $table->dropForeign(['shop_address_id']);
                $table->dropColumn('shop_address_id');
            }
        });
    }
};
