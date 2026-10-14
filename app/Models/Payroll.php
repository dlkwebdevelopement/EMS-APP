<?php
// app/Models/Payroll.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payrolls';
    
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'present_days',
        'absent_days',
        'leave_days',
        'paid_leave_days',
        'overtime_hours',
        'overtime_amount',
        'allowances',
        'allowances_breakdown',
        'deductions',
        'deductions_breakdown',
        'gross_salary',
        'net_salary',
        'status',
        'processed_date',
        'paid_date',
        'notes'
    ];

    protected $casts = [
        'allowances_breakdown' => 'array',
        'deductions_breakdown' => 'array',
        'processed_date' => 'datetime',
        'paid_date' => 'datetime',
        'basic_salary' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payslip()
    {
        return $this->hasOne(Payslip::class);
    }
}