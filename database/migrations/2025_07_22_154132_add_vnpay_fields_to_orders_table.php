<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVnpayFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('vnp_txn_ref')->nullable();
            $table->string('vnp_transaction_no')->nullable();
            $table->string('vnp_response_code')->nullable();
            $table->string('vnp_bank_code')->nullable();
            $table->string('vnp_bank_tran_no')->nullable();
            $table->string('vnp_card_type')->nullable();
            $table->timestamp('vnp_pay_date')->nullable();
            $table->string('vnp_secure_hash')->nullable();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'vnp_txn_ref',
                'vnp_transaction_no',
                'vnp_response_code',
                'vnp_bank_code',
                'vnp_bank_tran_no',
                'vnp_card_type',
                'vnp_pay_date',
                'vnp_secure_hash',
            ]);
        });
    }
}