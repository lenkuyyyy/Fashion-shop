<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up(): void
       {
           Schema::create('product_variants', function (Blueprint $table) {
               $table->id();
               $table->foreignId('product_id')->constrained()->onDelete('cascade');
               $table->string('color', 50)->nullable();
               $table->string('size', 50)->nullable();
               $table->string('sku', 50)->unique();
               $table->decimal('price', 10, 2);
               $table->integer('stock_quantity');
               $table->string('image', 255)->nullable();
               $table->enum('status', ['active', 'inactive'])->default('active');
               $table->timestamps();
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('product_variants');
       }
   };