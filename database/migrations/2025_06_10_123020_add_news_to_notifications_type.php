<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewsToNotificationsType extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Update the ENUM to include 'news'
            $table->enum('type', ['system', 'email', 'order', 'product', 'news', 'promotion', 'other'])->change();
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Revert to the original ENUM list (without 'news')
            $table->enum('type', ['system', 'email', 'order', 'product', 'promotion', 'other'])->change();
        });
    }
}