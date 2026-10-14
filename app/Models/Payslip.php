<?php
// app/Models/Payslip.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    protected $table = 'payslips';
    
    protected $fillable = [
        'employee_id',
        'payroll_id',
        'file_path',
        'file_name',
        'amount',
        'generated_date',
        'is_downloaded',
        'downloaded_at',
        'is_emailed',
        'emailed_at'
    ];

    protected $casts = [
        'generated_date' => 'datetime',
        'downloaded_at' => 'datetime',
        'emailed_at' => 'datetime',
        'amount' => 'decimal:2',
        'is_downloaded' => 'boolean',
        'is_emailed' => 'boolean'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}