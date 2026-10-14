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
            $table->string('bill_image')->nullable()->after('remarks');
        });

        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->string('bill_image')->nullable()->after('replacement_asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_allocations', function (Blueprint $table) {
            $table->dropColumn('bill_image');
        });

        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->dropColumn('bill_image');
        });
    }
};
