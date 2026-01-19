<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up(): void
       {
           Schema::create('coupons', function (Blueprint $table) {
               $table->id();
               $table->string('code', 50)->unique();
               $table->enum('discount_type', ['percent', 'fixed']);
               $table->decimal('discount_value', 10, 2);
               $table->decimal('min_order_value', 10, 2)->nullable();
               $table->decimal('max_discount', 10, 2)->nullable();
               $table->timestamp('start_date');
               $table->timestamp('end_date');
               $table->integer('usage_limit')->nullable();
               $table->integer('user_usage_limit')->nullable();
               $table->integer('used_count')->default(0);
               $table->json('applicable_categories')->nullable();
               $table->json('applicable_products')->nullable();
               $table->enum('status', ['active', 'inactive'])->default('active');
               $table->timestamps();
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('coupons');
       }
   };