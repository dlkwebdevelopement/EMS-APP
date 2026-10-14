<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $table = 'attendance';
    
    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'check_in',
        'check_out',
        'break_start',
        'break_end',
        'lunch_start',
        'lunch_end',
        'total_break_minutes',
        'break_duration_seconds',
        'lunch_duration_seconds',
        'overtime_seconds',
        'late_minutes',
        'early_exit_minutes',
        'status',
        'overtime_hours',
        'check_in_method',
        'check_out_method',
        'check_in_location',
        'check_out_location',
        'remarks'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'lunch_start' => 'datetime',
        'lunch_end' => 'datetime',
        'overtime_hours' => 'decimal:2',
        'total_break_minutes' => 'integer',
        'break_duration_seconds' => 'integer',
        'lunch_duration_seconds' => 'integer',
        'overtime_seconds' => 'integer',
        'late_minutes' => 'integer',
        'early_exit_minutes' => 'integer'
    ];

    protected $appends = [
        'formatted_overtime',
        'formatted_break_time',
        'formatted_lunch_time',
        'formatted_total_break_time',
        'formatted_working_hours',
        'working_hours',
        'working_minutes'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'shift_id');
    }

    public static function formatSeconds($totalSeconds)
    {
        if (!$totalSeconds || $totalSeconds <= 0) {
            return '0s';
        }
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        $parts = [];
        if ($hours > 0) $parts[] = "{$hours}h";
        if ($minutes > 0) $parts[] = "{$minutes}m";
        if ($seconds > 0 || empty($parts)) $parts[] = "{$seconds}s";

        return implode(' ', $parts);
    }

    public function getFormattedOvertimeAttribute()
    {
        if ($this->overtime_seconds > 0) {
            return static::formatSeconds($this->overtime_seconds);
        }
        if ($this->overtime_hours > 0) {
            return static::formatSeconds(round($this->overtime_hours * 3600));
        }
        return '0s';
    }

    public function getFormattedBreakTimeAttribute()
    {
        return static::formatSeconds($this->break_duration_seconds ?? 0);
    }

    public function getFormattedLunchTimeAttribute()
    {
        return static::formatSeconds($this->lunch_duration_seconds ?? 0);
    }

    public function getFormattedTotalBreakTimeAttribute()
    {
        $totalSecs = ($this->break_duration_seconds ?? 0) + ($this->lunch_duration_seconds ?? 0);
        if ($totalSecs <= 0 && $this->total_break_minutes > 0) {
            $totalSecs = $this->total_break_minutes * 60;
        }
        return static::formatSeconds($totalSecs);
    }

    public function getWorkingDurationSecondsAttribute()
    {
        if (!$this->check_in) {
            return 0;
        }
        $checkIn = \Carbon\Carbon::parse($this->check_in);
        $checkOut = $this->check_out ? \Carbon\Carbon::parse($this->check_out) : \Carbon\Carbon::now();
        
        $totalSeconds = abs($checkOut->diffInSeconds($checkIn));
        
        $totalBreakSecs = ($this->break_duration_seconds ?? 0) + ($this->lunch_duration_seconds ?? 0);
        if ($totalBreakSecs <= 0 && $this->total_break_minutes > 0) {
            $totalBreakSecs = $this->total_break_minutes * 60;
        }

        $workingSeconds = $totalSeconds - $totalBreakSecs;
        return $workingSeconds > 0 ? $workingSeconds : 0;
    }

    public function getFormattedWorkingHoursAttribute()
    {
        return static::formatSeconds($this->working_duration_seconds);
    }

    public function getWorkingHoursAttribute()
    {
        return floor($this->working_duration_seconds / 3600);
    }

    public function getWorkingMinutesAttribute()
    {
        return floor(($this->working_duration_seconds % 3600) / 60);
    }

    public static function autoMarkMissedCheckouts($employeeId = null)
    {
        $now = Carbon::now();
        
        $query = self::whereNull('check_out');
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        $pending = $query->get();
        
        foreach ($pending as $attendance) {
            $shift = WorkShift::find($attendance->shift_id);
            $shiftEndStr = $shift ? $shift->end_time : '18:00:00';
            
            $attendanceDate = Carbon::parse($attendance->date)->toDateString();
            $shiftEnd = Carbon::parse($attendanceDate . ' ' . $shiftEndStr);
            
            $isPastDate = Carbon::parse($attendance->date)->lt(Carbon::today());
            $isTodayAndShiftEnded = Carbon::parse($attendance->date)->isToday() && $now->gt($shiftEnd->addHours(2));
            
            if ($isPastDate || $isTodayAndShiftEnded) {
                $attendance->update([
                    'status' => 'absent',
                    'remarks' => 'Auto-marked absent: Missed check-out'
                ]);
            }
        }
    }
}