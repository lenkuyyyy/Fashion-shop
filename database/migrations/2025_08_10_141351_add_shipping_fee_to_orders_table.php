<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingFeeToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_fee')) {
                // ưu tiên đặt sau shipping_address_id nếu có
                if (Schema::hasColumn('orders', 'shipping_address_id')) {
                    $table->decimal('shipping_fee', 10, 2)->nullable()->after('shipping_address_id');
                }
                // nếu không có shipping_address_id nhưng có total và bạn muốn sau total:
                else if (Schema::hasColumn('orders', 'total')) {
                    $table->decimal('shipping_fee', 10, 2)->nullable()->after('total');
                }
                // nếu cả 2 đều không có, thêm bình thường (được đặt ở cuối bảng)
                else {
                    $table->decimal('shipping_fee', 10, 2)->nullable();
                }
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_fee')) {
                $table->dropColumn('shipping_fee');
            }
        });
    }
}
