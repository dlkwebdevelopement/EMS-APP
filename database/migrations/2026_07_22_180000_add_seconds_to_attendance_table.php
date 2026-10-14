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
        if (Schema::hasTable('attendance')) {
            Schema::table('attendance', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance', 'break_duration_seconds')) {
                    $table->integer('break_duration_seconds')->default(0);
                }
                if (!Schema::hasColumn('attendance', 'lunch_duration_seconds')) {
                    $table->integer('lunch_duration_seconds')->default(0);
                }
                if (!Schema::hasColumn('attendance', 'overtime_seconds')) {
                    $table->integer('overtime_seconds')->default(0);
                }
            });
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
