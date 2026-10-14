<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLifecycleHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'previous_status',
        'new_status',
        'change_date',
        'remarks',
        'changed_by_admin_id',
    ];

    protected $casts = [
        'change_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'changed_by_admin_id');
    }
}
