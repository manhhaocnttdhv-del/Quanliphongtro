<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('notes');
            $table->string('transaction_id')->nullable()->unique()->after('payment_ref');
        });

        // Update existing status values: 'pending' -> 'unpaid'
        DB::table('invoices')->where('status', 'pending')->update(['status' => 'unpaid']);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'transaction_id']);
        });
    }
};
