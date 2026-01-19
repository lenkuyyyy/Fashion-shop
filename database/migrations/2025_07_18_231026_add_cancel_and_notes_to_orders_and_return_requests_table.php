<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 👉 Thêm cột cho bảng orders
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('cancellation_requested')->default(false)->after('note'); // Yêu cầu hủy từ user
            $table->text('cancel_reason')->nullable()->after('cancellation_requested'); // Lý do từ user
            $table->text('admin_cancel_note')->nullable()->after('cancel_reason'); // Phản hồi admin
            $table->boolean('cancel_confirmed')->default(false)->after('admin_cancel_note'); // Đã xác nhận hủy
        });

        // 👉 Thêm cột cho bảng return_requests
        Schema::table('return_requests', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('status'); // Lý do yêu cầu trả hàng/hoàn tiền từ user
            $table->text('admin_note')->nullable()->after('reason'); // Ghi chú phản hồi của admin
        });
    }

    public function down(): void
    {
        // 👉 Rollback các cột đã thêm
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_requested',
                'cancel_reason',
                'admin_cancel_note',
                'cancel_confirmed',
            ]);
        });

        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn(['reason', 'admin_note']);
        });
    }
};
