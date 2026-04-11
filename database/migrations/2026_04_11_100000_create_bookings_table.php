<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Thông tin người thuê (nhập thủ công)
            $table->string('tenant_name');
            $table->string('tenant_phone');
            $table->string('tenant_cccd')->nullable();
            $table->integer('num_people')->default(1);
            $table->text('note')->nullable();

            // Tài chính
            $table->decimal('deposit_amount', 12, 0)->default(0);
            $table->date('move_in_date')->nullable();

            // Trạng thái
            $table->enum('status', ['pending', 'paid', 'cancelled', 'converted'])->default('pending');

            // Thời hạn giữ chỗ (24h)
            $table->timestamp('expired_at')->nullable();

            // Thanh toán
            $table->enum('payment_method', ['offline', 'online'])->default('offline');
            $table->string('payment_ref')->nullable(); // mã giao dịch online
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');

            // Sau khi chuyển thành hợp đồng
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
