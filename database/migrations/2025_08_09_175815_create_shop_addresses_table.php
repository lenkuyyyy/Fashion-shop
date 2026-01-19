<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên shop/kho
            $table->string('phone'); // SĐT liên hệ
            $table->unsignedBigInteger('province_id'); // Mã tỉnh/thành GHN
            $table->unsignedBigInteger('district_id'); // Mã quận/huyện GHN
            $table->string('ward_code', 20); // Mã phường/xã GHN
            $table->string('address_detail'); // Địa chỉ chi tiết
            $table->boolean('is_default')->default(true); // Mặc định = true
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_addresses');
    }
};
