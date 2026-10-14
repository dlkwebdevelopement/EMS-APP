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
        Schema::table('asset_allocations', function (Blueprint $table) {
            $table->unsignedBigInteger('allocated_by')->nullable()->after('employee_id');
            $table->foreign('allocated_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_allocations', function (Blueprint $table) {
            $table->dropForeign(['allocated_by']);
            $table->dropColumn('allocated_by');
        });
    }
};
