<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up(): void
       {
           Schema::create('payments', function (Blueprint $table) {
               $table->id();
               $table->foreignId('order_id')->constrained()->onDelete('cascade');
               $table->enum('method', ['cod', 'bank_transfer', 'online']);
               $table->decimal('amount', 10, 2);
               $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
               $table->string('transaction_id', 100)->nullable();
               $table->timestamp('paid_at')->nullable();
               $table->string('payment_gateway', 50)->nullable();
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('payments');
       }
   };
   