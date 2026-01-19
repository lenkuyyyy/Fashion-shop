<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Thêm cột import_price, kiểu decimal 10,2 và có thể null
            $table->decimal('import_price', 10, 2)->nullable()->after('price')
                ->comment('Giá nhập tại thời điểm tạo đơn hàng');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Xoá cột import_price khi rollback
            $table->dropColumn('import_price');
        });
    }
};
