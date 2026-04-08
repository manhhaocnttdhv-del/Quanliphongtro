<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('approval_status')->default('approved')->after('status');
            // 'pending' = chờ duyệt, 'approved' = đã duyệt, 'rejected' = bị từ chối
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
};
