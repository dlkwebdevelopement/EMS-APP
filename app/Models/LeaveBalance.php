<?php
// app/Models/LeaveBalance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table = 'leave_balances';

    protected $fillable = [
        'employee_id',
        'year',
        'sick_leave_total',
        'sick_leave_used',
        'casual_leave_total',
        'casual_leave_used',
        'annual_leave_total',
        'annual_leave_used',
        'unpaid_leave_total',
        'unpaid_leave_used',
    ];

    protected $casts = [
        'year'               => 'integer',
        'sick_leave_total'   => 'integer',
        'sick_leave_used'    => 'integer',
        'casual_leave_total' => 'integer',
        'casual_leave_used'  => 'integer',
        'annual_leave_total' => 'integer',
        'annual_leave_used'  => 'integer',
        'unpaid_leave_total' => 'integer',
        'unpaid_leave_used'  => 'integer',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // =========================================================================
    // ACCESSORS — remaining days per type
    // =========================================================================

    public function getSickBalanceAttribute(): int
    {
        return max(0, $this->sick_leave_total - $this->sick_leave_used);
    }

    public function getCasualBalanceAttribute(): int
    {
        return max(0, $this->casual_leave_total - $this->casual_leave_used);
    }

    public function getAnnualBalanceAttribute(): int
    {
        return max(0, $this->annual_leave_total - $this->annual_leave_used);
    }

    public function getUnpaidBalanceAttribute(): int
    {
        return max(0, $this->unpaid_leave_total - $this->unpaid_leave_used);
    }

    // =========================================================================
    // ACCESSORS — totals
    // =========================================================================

    public function getTotalLeavesAttribute(): int
    {
        return $this->sick_leave_total + $this->casual_leave_total + $this->annual_leave_total;
    }

    public function getTotalUsedAttribute(): int
    {
        return $this->sick_leave_used + $this->casual_leave_used + $this->annual_leave_used;
    }

    public function getTotalRemainingAttribute(): int
    {
        return max(0, $this->total_leaves - $this->total_used);
    }

    public function getRemainingPercentageAttribute(): float
    {
        $total = $this->total_leaves;

        if ($total === 0) {
            return 0.0;
        }

        return ($this->total_remaining / $total) * 100;
    }
}