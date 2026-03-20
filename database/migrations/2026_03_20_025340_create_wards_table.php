<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name', 100);
            $table->string('type', 50)->nullable(); // Phường / Xã / Thị trấn
            $table->string('district_code', 10)->index();
            $table->string('province_code', 10)->index();
            $table->foreign('district_code')->references('code')->on('districts')->onDelete('cascade');
            $table->foreign('province_code')->references('code')->on('provinces')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
