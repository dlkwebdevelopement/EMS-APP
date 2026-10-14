<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    protected $table = 'work_shifts';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'grace_period_minutes',
        'break_duration_minutes',
        'is_2hour_permission_enabled',
        'is_default',
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
        'break3_enabled',
        'break3_begin_time',
        'break3_end_time',
        'break3_duration',
        'break4_enabled',
        'break4_begin_time',
        'break4_end_time',
        'break4_duration',
        'break5_enabled',
        'break5_begin_time',
        'break5_end_time',
        'break5_duration',
        'break6_enabled',
        'break6_begin_time',
        'break6_end_time',
        'break6_duration',
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
        'single_punch_policy'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'grace_period_minutes' => 'integer',
        'break_duration_minutes' => 'integer',
        'break1_enabled' => 'boolean',
        'break2_enabled' => 'boolean',
        'break3_enabled' => 'boolean',
        'break4_enabled' => 'boolean',
        'break5_enabled' => 'boolean',
        'break6_enabled' => 'boolean',
        'mark_absent_aaa' => 'boolean',
        'mark_absent_l_wo_l' => 'boolean',
        'no_of_present_for_weekly_off' => 'decimal:2',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'shift_id');
    }
}
