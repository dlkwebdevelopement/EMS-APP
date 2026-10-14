<?php
// app/Models/Employee.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class Employee extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'employees';
    
    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'password',
        'phone',
        'push_token',
        'address',
        'date_of_birth',
        'anniversary_date',
        'gender',
        'marital_status',
        'personal_email',
        'permanent_address',
        'blood_group',
        'hobbies',
        'favorite_quote',
        'social_media',
        'joining_date',
        'department',
        'designation',
        'reporting_manager_id',
        'basic_salary',
        'hourly_rate',
        'employment_type',
        'is_fresher',
        'total_experience_years',
        'total_experience_months',
        'previous_organization',
        'previous_designation',
        'internship_details',
        'education_qualification',
        'education_institution',
        'education_passout_year',
        'education_score',
        'employment_status',
        'probation_end_date',
        'resignation_date',
        'last_working_day',
        'termination_date',
        'termination_reason',
        'password_reset_otp',
        'password_reset_otp_expires_at',
        'bank_name',
        'bank_account_number',
        'ifsc_code',
        'pan_number',
        'aadhaar_number',
        'uan_number',
        'pf_number',
        'pf_enabled',
        'pf_rate',
        'pf_amount',
        'esi_enabled',
        'pt_enabled',
        'tds_enabled',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_relationship',
        'emergency_alternate_phone',
        'emergency_address',
        'office_ssid',
        'profile_photo',
        'device_token',
        'role',
        'is_admin',
        'is_team_leader',
        'is_active',
        'email_verified_at',
        'shift_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'joining_date' => 'date',
        'date_of_birth' => 'date',
        'anniversary_date' => 'date',
        'probation_end_date' => 'date',
        'resignation_date' => 'date',
        'last_working_day' => 'date',
        'termination_date' => 'date',
        'is_active' => 'boolean',
        'is_team_leader' => 'boolean',
        'is_admin' => 'boolean',
        'basic_salary' => 'decimal:2',
        'social_media' => 'array'
    ];

    // Hierarchy Relationships
    public function reportingManager()
    {
        return $this->belongsTo(Employee::class, 'reporting_manager_id');
    }

    public function directReports()
    {
        return $this->hasMany(Employee::class, 'reporting_manager_id');
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function lifecycleHistories()
    {
        return $this->hasMany(EmployeeLifecycleHistory::class)->orderBy('created_at', 'desc');
    }

    // Existing Relationships
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function timeSheets()
    {
        return $this->hasMany(TimeSheet::class);
    }

    public function birthdayNotifications()
    {
        return $this->hasMany(BirthdayNotification::class);
    }

    public function assetAllocations()
    {
        return $this->hasMany(AssetAllocation::class);
    }

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'shift_id');
    }

    public function currentAssets()
    {
        return $this->hasMany(AssetAllocation::class)->whereNull('returned_at');
    }

    // Current leave balance (accessor)
    public function getCurrentLeaveBalanceAttribute()
    {
        $currentYear = date('Y');
        $balance = $this->leaveBalances()->where('year', $currentYear)->first();
        
        if (!$balance) {
            $balance = $this->leaveBalances()->create([
                'year' => $currentYear,
                'sick_leave_total' => 12,
                'sick_leave_used' => 0,
                'casual_leave_total' => 12,
                'casual_leave_used' => 0,
                'annual_leave_total' => 15,
                'annual_leave_used' => 0,
            ]);
        }
        
        return $balance;
    }

    public function getLeaveBalanceForYear($year)
    {
        return $this->leaveBalances()->where('year', $year)->first();
    }

    // Birthday & Anniversary Methods
    public function getBirthdayAttribute()
    {
        if (!$this->date_of_birth) return null;
        return Carbon::parse($this->date_of_birth);
    }

    public function getAnniversaryAttribute()
    {
        if (!$this->anniversary_date) return null;
        return Carbon::parse($this->anniversary_date);
    }

    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) return null;
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getYearsOfServiceAttribute()
    {
        if (!$this->joining_date) return 0;
        return Carbon::parse($this->joining_date)->diffInYears(now());
    }

    public function isBirthdayToday()
    {
        if (!$this->date_of_birth) return false;
        return Carbon::parse($this->date_of_birth)->format('m-d') === now()->format('m-d');
    }

    public function isAnniversaryToday()
    {
        if (!$this->anniversary_date) return false;
        return Carbon::parse($this->anniversary_date)->format('m-d') === now()->format('m-d');
    }

    public function experiences()
    {
        return $this->hasMany(EmployeeExperience::class)->orderBy('start_date', 'desc');
    }

    public function educations()
    {
        return $this->hasMany(EmployeeEducation::class)->orderBy('passout_year', 'desc');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function assignedTlLeads()
    {
        return $this->hasMany(Lead::class, 'tl_id');
    }

    public function salesLeads()
    {
        return $this->hasMany(Lead::class, 'sales_id');
    }

    public function workerAssignments()
    {
        return $this->hasMany(LeadWorker::class, 'employee_id');
    }

    public function assignedWorkerLeads()
    {
        return $this->belongsToMany(Lead::class, 'lead_workers', 'employee_id', 'lead_id')
                    ->withPivot(['id', 'assigned_by_tl_id', 'start_date', 'end_date', 'status', 'instructions', 'submission_notes', 'submitted_at'])
                    ->withTimestamps();
    }

    public function isTeamLeader(): bool
    {
        return (bool) ($this->is_team_leader || $this->role === 'team_leader' || $this->role === 'tl');
    }

    public function isSales(): bool
    {
        return (bool) ($this->role === 'sales' || str_contains(strtolower($this->department ?? ''), 'sales') || str_contains(strtolower($this->designation ?? ''), 'sales'));
    }

    public function leadFollowups()
    {
        return $this->hasMany(LeadFollowup::class, 'user_id');
    }

    public function assignedLeadsAsTl()
    {
        return $this->hasMany(Lead::class, 'tl_id');
    }

    public function leadWorkers()
    {
        return $this->hasMany(LeadWorker::class, 'employee_id');
    }

    /**
     * Standardized DLK Employee Code Generator (e.g. DLK26@378)
     */
    public static function generateNextEmployeeCode($date = null): string
    {
        $yearCode = $date ? date('y', strtotime($date)) : date('y');
        $prefix = "DLK{$yearCode}@";

        $employees = self::where('employee_code', 'like', $prefix . '%')->pluck('employee_code');

        $maxNumber = 0;
        foreach ($employees as $code) {
            $parts = explode('@', $code);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $num = intval($parts[1]);
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        if ($maxNumber === 0) {
            // Find highest existing code across any year to continue smoothly
            $allEmployees = self::where('employee_code', 'like', 'DLK%@%')->pluck('employee_code');
            $globalMax = 0;
            foreach ($allEmployees as $code) {
                $parts = explode('@', $code);
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $num = intval($parts[1]);
                    if ($num > $globalMax) $globalMax = $num;
                }
            }
            $maxNumber = $globalMax > 0 ? $globalMax + 1 : 1;
        } else {
            $maxNumber++;
        }

        return $prefix . str_pad($maxNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getFormattedExperienceAttribute()
    {
        $totalDays = 0;
        $hasExperience = false;

        try {
            $exps = $this->experiences;
            if ($exps && $exps->count() > 0) {
                foreach ($exps as $exp) {
                    if ($exp->start_date) {
                        $hasExperience = true;
                        $start = Carbon::parse($exp->start_date);
                        $end = $exp->is_current_job || !$exp->end_date ? now() : Carbon::parse($exp->end_date);
                        if ($end->gte($start)) {
                            $totalDays += $start->diffInDays($end);
                        }
                    }
                }
            }
        } catch (\Exception $e) {}

        if ($this->joining_date) {
            $joining = Carbon::parse($this->joining_date);
            if ($joining->isPast()) {
                $totalDays += $joining->diffInDays(now());
            }
        }

        if ($totalDays == 0 && ($this->total_experience_years || $this->total_experience_months)) {
            $totalDays = (($this->total_experience_years ?? 0) * 365) + (($this->total_experience_months ?? 0) * 30.4);
            if ($totalDays > 0) $hasExperience = true;
        }

        if ($this->is_fresher && !$hasExperience && $totalDays < 30) {
            return 'Fresher';
        }

        $totalMonths = (int) round($totalDays / 30.4375);

        if ($totalMonths <= 0) {
            return $this->is_fresher ? 'Fresher' : '1 Month';
        }

        $years = intdiv($totalMonths, 12);
        $months = $totalMonths % 12;

        if ($years == 0) {
            return $months . ' Month' . ($months > 1 ? 's' : '');
        }

        if ($months == 0) {
            return $years . ' Year' . ($years > 1 ? 's' : '');
        }

        return $years . '.' . $months . ' Yrs (' . $years . ' Yr ' . $months . ' Mo' . ($months > 1 ? 's' : '') . ')';
    }
}