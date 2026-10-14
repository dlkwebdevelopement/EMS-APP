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
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->integer('maternity_leave_total')->default(90)->after('annual_leave_used');
            $table->integer('maternity_leave_used')->default(0)->after('maternity_leave_total');
            $table->integer('paternity_leave_total')->default(7)->after('maternity_leave_used');
            $table->integer('paternity_leave_used')->default(0)->after('paternity_leave_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropColumn(['maternity_leave_total', 'maternity_leave_used', 'paternity_leave_total', 'paternity_leave_used']);
        });
    }
};
