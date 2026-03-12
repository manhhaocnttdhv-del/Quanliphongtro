<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->decimal('room_fee', 12, 0)->default(0);
            $table->decimal('electricity_fee', 12, 0)->default(0);
            $table->decimal('water_fee', 12, 0)->default(0);
            $table->decimal('service_fee', 12, 0)->default(0);
            $table->decimal('total_amount', 12, 0)->default(0);
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->string('payment_ref')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
