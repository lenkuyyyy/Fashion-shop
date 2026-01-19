<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up(): void
       {
           Schema::create('products', function (Blueprint $table) {
               $table->id();
               $table->string('name', 255);
               $table->foreignId('category_id')->constrained()->onDelete('cascade');
               $table->foreignId('brand_id')->constrained()->onDelete('cascade');
               $table->string('sku', 50)->unique();
               $table->string('thumbnail', 255)->nullable();
               $table->text('description')->nullable();
               $table->string('short_description', 255)->nullable();
               $table->string('slug', 255)->unique();
               $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
               $table->timestamps();
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('products');
       }
   };