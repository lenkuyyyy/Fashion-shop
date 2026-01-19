<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationIdsToShippingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->unsignedInteger('province_id')->after('id')->nullable();
            $table->unsignedInteger('district_id')->after('province_id')->nullable();
            $table->string('ward_code', 100)->after('district_id')->nullable();

            // Nếu cần, thêm các chỉ mục (index) cho các cột mới
            $table->index('province_id');
            $table->index('district_id');
            $table->index('ward_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->dropIndex(['province_id']);
            $table->dropIndex(['district_id']);
            $table->dropIndex(['ward_code']);

            $table->dropColumn('province_id');
            $table->dropColumn('district_id');
            $table->dropColumn('ward_code');
        });
    }
}