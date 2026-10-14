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
        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->timestamp('received_at')->nullable()->after('maintenance_at');
            // Update enum to include 'in_progress'
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE asset_maintenances MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->dropColumn('received_at');
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE asset_maintenances MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending'");
        });
    }
};
