<?php
// app/Models/TimeSheet.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimeSheet extends Model
{
    protected $table = 'time_sheets';
    
    protected $fillable = [
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'break_hours',
        'total_hours',
        'project',
        'task_description',
        'status',
        'remarks',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'break_hours' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public static function calculateTotalHours($start_time, $end_time, $break_hours)
    {
        $start = Carbon::parse($start_time);
        $end = Carbon::parse($end_time);
        $hours = $end->diffInHours($start) + ($end->diffInMinutes($start) % 60) / 60;
        return round($hours - $break_hours, 2);
    }
}