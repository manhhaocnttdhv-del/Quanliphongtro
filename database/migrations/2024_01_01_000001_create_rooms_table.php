<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 12, 0);
            $table->decimal('area', 8, 2)->nullable();
            $table->integer('floor')->nullable();
            $table->text('description')->nullable();
            $table->decimal('electricity_price', 10, 0)->default(3500);
            $table->decimal('water_price', 10, 0)->default(15000);
            $table->decimal('service_fee', 10, 0)->default(0);
            $table->enum('status', ['available', 'rented'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
