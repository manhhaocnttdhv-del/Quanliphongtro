<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('rent_request_id')->nullable()->constrained()->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('deposit', 12, 0)->default(0);
            $table->decimal('monthly_rent', 12, 0);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'ended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
