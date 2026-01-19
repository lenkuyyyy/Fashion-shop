<?php

     use Illuminate\Database\Migrations\Migration;
     use Illuminate\Database\Schema\Blueprint;
     use Illuminate\Support\Facades\Schema;

     return new class extends Migration
     {
         public function up(): void
         {
             Schema::create('users', function (Blueprint $table) {
                 $table->id();
                 $table->string('name', 100);
                 $table->string('email', 100)->unique();
                 $table->string('phone_number', 20)->nullable();
                 $table->string('password', 255);
                 $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
                 $table->timestamp('email_verified_at')->nullable();
                 $table->string('reset_password_token', 100)->nullable();
                 $table->timestamp('reset_password_expires_at')->nullable();
                 $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
                 $table->timestamps();
                 $table->softDeletes();
             });
         }

         public function down(): void
         {
             Schema::dropIfExists('users');
         }
     };