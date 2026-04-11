<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rent_requests', function (Blueprint $table) {
            // Ngày dự kiến vào ở của người thuê
            $table->date('move_in_date')->nullable()->after('note');
            // Giá thuê đã thương lượng (để lưu khi admin/landlord ghi đè giá phòng)
            $table->decimal('agreed_rent', 12, 0)->nullable()->after('move_in_date');
        });
    }

    public function down(): void
    {
        Schema::table('rent_requests', function (Blueprint $table) {
            $table->dropColumn(['move_in_date', 'agreed_rent']);
        });
    }
};
