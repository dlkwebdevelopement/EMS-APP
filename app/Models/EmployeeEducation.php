<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_educations';

    protected $fillable = [
        'employee_id',
        'level',
        'degree_title',
        'institution_name',
        'board_or_university',
        'passout_year',
        'score_or_grade',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
