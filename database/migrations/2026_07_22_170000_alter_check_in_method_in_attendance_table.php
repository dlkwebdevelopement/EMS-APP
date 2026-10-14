<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('attendance')) {
            DB::statement("ALTER TABLE attendance MODIFY COLUMN check_in_method VARCHAR(50) NOT NULL DEFAULT 'manual'");
            DB::statement("ALTER TABLE attendance MODIFY COLUMN check_out_method VARCHAR(50) NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed
    }
};
