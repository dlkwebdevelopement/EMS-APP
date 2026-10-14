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
        // 1. Enhance Employees Table with Team Leader (TL) designation flag and role check
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'is_admin')) {
                $table->boolean('is_admin')->default(false);
            }
            
            if (!Schema::hasColumn('employees', 'is_team_leader')) {
                $table->boolean('is_team_leader')->default(false)->index();
            }
            if (!Schema::hasColumn('employees', 'role')) {
                $table->string('role', 50)->default('employee')->index();
            }
        });

        // 2. Enhance Leads Table with TL and Sales assignment
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'tl_id')) {
                $table->foreignId('tl_id')->nullable()->after('assigned_to')->constrained('employees')->nullOnDelete();
            }
            if (!Schema::hasColumn('leads', 'assigned_to_tl_at')) {
                $table->dateTime('assigned_to_tl_at')->nullable()->after('tl_id');
            }
            if (!Schema::hasColumn('leads', 'sales_id')) {
                $table->foreignId('sales_id')->nullable()->after('assigned_to_tl_at')->constrained('employees')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'sales_id')) {
                $table->dropForeign(['sales_id']);
                $table->dropColumn('sales_id');
            }
            if (Schema::hasColumn('leads', 'assigned_to_tl_at')) {
                $table->dropColumn('assigned_to_tl_at');
            }
            if (Schema::hasColumn('leads', 'tl_id')) {
                $table->dropForeign(['tl_id']);
                $table->dropColumn('tl_id');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'is_team_leader')) {
                $table->dropColumn('is_team_leader');
            }
        });
    }
};
