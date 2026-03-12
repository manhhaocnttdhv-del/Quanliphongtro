<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->decimal('electricity_old', 10, 2)->default(0);
            $table->decimal('electricity_new', 10, 2)->default(0);
            $table->decimal('water_old', 10, 2)->default(0);
            $table->decimal('water_new', 10, 2)->default(0);
            $table->decimal('electricity_price', 10, 0)->default(3500);
            $table->decimal('water_price', 10, 0)->default(15000);
            $table->decimal('electricity_amount', 12, 0)->default(0);
            $table->decimal('water_amount', 12, 0)->default(0);
            $table->timestamps();
            $table->unique(['room_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilities');
    }
};
