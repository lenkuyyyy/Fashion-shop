<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('image_path_1')->nullable()->change();
            $table->string('image_path_2')->nullable()->change();
            $table->string('image_path_3')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('image_path_1')->change();
            $table->string('image_path_2')->change();
            $table->string('image_path_3')->change();
        });
    }
};