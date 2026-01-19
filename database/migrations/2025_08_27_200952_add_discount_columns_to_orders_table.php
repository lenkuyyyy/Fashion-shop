<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
        $table->decimal('order_discount', 15, 2)->default(0)->after('shipping_fee');
        $table->decimal('shipping_discount', 15, 2)->default(0)->after('order_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn('order_discount');
        $table->dropColumn('shipping_discount');
    });
    }
};
