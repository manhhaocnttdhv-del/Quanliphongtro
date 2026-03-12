<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('province_name')->nullable()->after('description');
            $table->string('district_name')->nullable()->after('province_name');
            $table->string('ward_name')->nullable()->after('district_name');
            $table->string('address_detail')->nullable()->after('ward_name');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['province_name', 'district_name', 'ward_name', 'address_detail']);
        });
    }
};
