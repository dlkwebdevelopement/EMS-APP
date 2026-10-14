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
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->string('shift_duration')->nullable();
            $table->boolean('break1_enabled')->default(false);
            $table->time('break1_begin_time')->nullable();
            $table->time('break1_end_time')->nullable();
            $table->string('break1_duration')->nullable();
            $table->boolean('break2_enabled')->default(false);
            $table->time('break2_begin_time')->nullable();
            $table->time('break2_end_time')->nullable();
            $table->string('break2_duration')->nullable();
            $table->string('break_details_policy')->nullable();
            $table->string('second_weekly_off_weeks')->nullable();
            $table->string('first_weekly_off')->nullable();
            $table->string('second_weekly_off')->nullable();
            $table->string('second_wo_type')->nullable();
            $table->string('half_day_shift')->nullable();
            $table->string('remove_duplicate_punch')->nullable();
            $table->string('max_early_hour_allow')->nullable();
            $table->string('max_ot_allow')->nullable();
            $table->decimal('no_of_present_for_weekly_off', 8, 2)->default(0.00);
            $table->boolean('mark_absent_aaa')->default(false);
            $table->boolean('mark_absent_l_wo_l')->default(false);
            $table->string('single_punch_policy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'shift_duration',
                'break1_enabled',
                'break1_begin_time',
                'break1_end_time',
                'break1_duration',
                'break2_enabled',
                'break2_begin_time',
                'break2_end_time',
                'break2_duration',
                'break_details_policy',
                'second_weekly_off_weeks',
                'first_weekly_off',
                'second_weekly_off',
                'second_wo_type',
                'half_day_shift',
                'remove_duplicate_punch',
                'max_early_hour_allow',
                'max_ot_allow',
                'no_of_present_for_weekly_off',
                'mark_absent_aaa',
                'mark_absent_l_wo_l',
                'single_punch_policy',
            ]);
        });
    }
};
