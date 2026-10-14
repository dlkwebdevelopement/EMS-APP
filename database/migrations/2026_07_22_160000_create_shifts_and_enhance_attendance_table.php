<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Work Shifts Table
        if (!Schema::hasTable('work_shifts')) {
            Schema::create('work_shifts', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // General Shift, Morning Shift, Night Shift
                $table->time('start_time')->default('09:00:00');
                $table->time('end_time')->default('18:00:00');
                $table->integer('grace_period_minutes')->default(15);
                $table->integer('break_duration_minutes')->default(60);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });

            // Seed default shifts
            DB::table('work_shifts')->insert([
                [
                    'name' => 'General Shift (9 AM - 6 PM)',
                    'start_time' => '09:00:00',
                    'end_time' => '18:00:00',
                    'grace_period_minutes' => 15,
                    'break_duration_minutes' => 60,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Morning Shift (6 AM - 2 PM)',
                    'start_time' => '06:00:00',
                    'end_time' => '14:00:00',
                    'grace_period_minutes' => 15,
                    'break_duration_minutes' => 45,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Night Shift (10 PM - 6 AM)',
                    'start_time' => '22:00:00',
                    'end_time' => '06:00:00',
                    'grace_period_minutes' => 15,
                    'break_duration_minutes' => 45,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // 2. Add shift_id to employees table if not present
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'shift_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('shift_id')->nullable()->constrained('work_shifts')->onDelete('set null');
            });
        }

        // 3. Enhance Attendance Table with Break, Lunch, Late, Early Exit, & Shift fields
        if (Schema::hasTable('attendance')) {
            Schema::table('attendance', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance', 'shift_id')) {
                    $table->foreignId('shift_id')->nullable()->constrained('work_shifts')->onDelete('set null');
                }
                if (!Schema::hasColumn('attendance', 'break_start')) {
                    $table->timestamp('break_start')->nullable();
                }
                if (!Schema::hasColumn('attendance', 'break_end')) {
                    $table->timestamp('break_end')->nullable();
                }
                if (!Schema::hasColumn('attendance', 'lunch_start')) {
                    $table->timestamp('lunch_start')->nullable();
                }
                if (!Schema::hasColumn('attendance', 'lunch_end')) {
                    $table->timestamp('lunch_end')->nullable();
                }
                if (!Schema::hasColumn('attendance', 'total_break_minutes')) {
                    $table->integer('total_break_minutes')->default(0);
                }
                if (!Schema::hasColumn('attendance', 'late_minutes')) {
                    $table->integer('late_minutes')->default(0);
                }
                if (!Schema::hasColumn('attendance', 'early_exit_minutes')) {
                    $table->integer('early_exit_minutes')->default(0);
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
