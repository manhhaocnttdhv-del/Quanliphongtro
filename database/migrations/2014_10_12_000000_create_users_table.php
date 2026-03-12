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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('id_card')->nullable(); // CCCD
            $table->date('dob')->nullable();       // Ngày sinh
            $table->string('gender')->nullable();  // Giới tính
            $table->string('province_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('ward_name')->nullable();
            $table->string('address_detail')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'landlord', 'tenant'])->default('tenant');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
