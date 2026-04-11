<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Thêm giá trị 'reserved' vào ENUM status của bảng rooms.
     * MySQL không hỗ trợ ALTER ENUM trực tiếp qua Blueprint,
     * nên dùng raw SQL.
     */
    public function up(): void
    {
        // Sửa ENUM: available | reserved | rented
        DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('available', 'reserved', 'rented') NOT NULL DEFAULT 'available'");
    }

    public function down(): void
    {
        // Trước khi rollback, đặt các phòng reserved về available để tránh lỗi truncate
        DB::statement("UPDATE rooms SET status = 'available' WHERE status = 'reserved'");
        DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('available', 'rented') NOT NULL DEFAULT 'available'");
    }
};
