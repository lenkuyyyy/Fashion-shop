<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up(): void
       {
           Schema::create('shipping_addresses', function (Blueprint $table) {
               $table->id();
               $table->foreignId('user_id')->constrained()->onDelete('cascade');
               $table->string('name', 100);
               $table->string('phone_number', 20);
               $table->string('address', 255);
               $table->string('ward', 100)->nullable();
               $table->string('district', 100)->nullable();
               $table->string('city', 100)->nullable();
               $table->boolean('is_default')->default(false);
               $table->timestamps();
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('shipping_addresses');
       }
   };