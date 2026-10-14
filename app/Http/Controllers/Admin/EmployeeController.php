<?php
// app/Http/Controllers/Admin/EmployeeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeExperience;
use App\Models\EmployeeEducation;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;


class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        $query = Employee::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Get departments for filter dropdown (cached for 1 hour)
        $departments = cache()->remember('employee_departments', 3600, function () {
            return Employee::distinct()->pluck('department');
        });

        $employees = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $suggestedCode = $this->generateEmployeeCode();
        return view('admin.employees.create', compact('suggestedCode'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                      => 'required|string|max:255',
            'email'                     => 'required|email|unique:employees,email',
            'password'                  => 'required|string|min:6',
            'phone'                     => 'required|string|max:20',
            'department'                => 'required|string|max:100',
            'designation'               => 'required|string|max:100',
            'basic_salary'              => 'required|numeric|min:0|max:999999.99',
            'joining_date'              => 'required|date',
            'employee_code'             => 'nullable|string|unique:employees,employee_code',
            'date_of_birth'             => 'nullable|date|before:today',
            'address'                   => 'nullable|string|max:500',
            'profile_photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'employment_type'           => 'nullable|string|in:full_time,part_time,contract,intern',
            'office_ssid'               => 'nullable|string|max:255',
            'bank_name'                 => 'nullable|string|max:100',
            'bank_account_number'       => 'nullable|string|max:50',
            'ifsc_code'                 => 'nullable|string|max:20',
            'pan_number'                => 'nullable|string|max:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'emergency_contact_name'    => 'nullable|string|max:100',
            'emergency_relationship'    => 'nullable|string|max:50',
            'emergency_contact_phone'   => 'nullable|string|max:20',
            'emergency_alternate_phone' => 'nullable|string|max:20',
            'emergency_address'         => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            $password = $validated['password'];
            
            // Handle profile photo upload
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')
                    ->store('employee_photos', 'public');
            }

            // Generate employee code if not provided
            $employeeCode = $request->employee_code ?? $this->generateEmployeeCode($request->joining_date);

            $employee = Employee::create([
                'employee_code'             => $employeeCode,
                'name'                      => $validated['name'],
                'email'                     => $validated['email'],
                'password'                  => Hash::make($password),
                'phone'                     => $validated['phone'],
                'address'                   => $validated['address'],
                'date_of_birth'             => $validated['date_of_birth'],
                'joining_date'              => $validated['joining_date'],
                'department'                => $validated['department'],
                'designation'               => $validated['designation'],
                'role'                      => $request->input('role', $request->boolean('is_team_leader') ? 'team_leader' : 'employee'),
                'is_team_leader'            => $request->boolean('is_team_leader') || ($request->input('role') === 'team_leader'),
                'is_admin'                  => $request->boolean('is_admin') || ($request->input('role') === 'admin'),
                'basic_salary'              => $validated['basic_salary'],
                'employment_type'           => $validated['employment_type'] ?? 'full_time',
                'office_ssid'               => $validated['office_ssid'] ?? 'office_wifi',
                'profile_photo'             => $profilePhotoPath,
                'is_active'                 => true,
                'bank_name'                 => $validated['bank_name'],
                'bank_account_number'       => $validated['bank_account_number'],
                'ifsc_code'                 => $validated['ifsc_code'],
                'pan_number'                => $validated['pan_number'],
                'emergency_contact_name'    => $validated['emergency_contact_name'] ?? null,
                'emergency_relationship'    => $validated['emergency_relationship'] ?? null,
                'emergency_contact_phone'   => $validated['emergency_contact_phone'] ?? null,
                'emergency_alternate_phone' => $validated['emergency_alternate_phone'] ?? null,
                'emergency_address'         => $validated['emergency_address'] ?? null,
            ]);

            // Create default leave balance for the current year
            LeaveBalance::create([
                'employee_id'        => $employee->id,
                'year'               => now()->year,
                'sick_leave_total'   => 12,
                'casual_leave_total' => 12,
                'annual_leave_total' => 15,
            ]);

            DB::commit();

            // Send welcome email with credentials
            try {
                Mail::to($employee->email)->send(new \App\Mail\EmployeeCreatedMail($employee, $password));
            } catch (\Exception $e) {
                Log::error('Failed to send employee creation email: ' . $e->getMessage());
            }

            // Clear departments cache
            cache()->forget('employee_departments');

            return redirect()->route('admin.employees.index')
                ->with('success', "Employee created successfully. Welcome email has been sent.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee creation failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to create employee. Please try again.');
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): View
    {
        try {
            $employee->load([
                'leaveBalances' => function ($query) {
                    $query->where('year', now()->year);
                },
                'currentAssets.asset',
                'assetAllocations.asset',
                'assetAllocations.allocatedBy',
                'documents',
                'experiences',
                'educations',
                'reportingManager',
                'directReports',
                'lifecycleHistories.admin'
            ]);
        } catch (\Exception $e) {
            // Fallback load core relations if optional tables are pending migration
            try {
                $employee->load(['currentAssets.asset', 'assetAllocations.asset']);
            } catch (\Exception $ex) {}
        }
        
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee): View
    {
        $allEmployees = Employee::where('id', '!=', $employee->id)->get();
        $shifts = \App\Models\WorkShift::all();
        return view('admin.employees.edit', compact('employee', 'allEmployees', 'shifts'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'name'                      => 'required|string|max:255',
            'email'                     => 'required|email|unique:employees,email,' . $employee->id,
            'phone'                     => 'required|string|max:20',
            'department'                => 'required|string|max:100',
            'designation'               => 'required|string|max:100',
            'basic_salary'              => 'required|numeric|min:0|max:999999.99',
            'joining_date'              => 'required|date',
            'date_of_birth'             => 'nullable|date|before:today',
            'address'                   => 'nullable|string|max:500',
            'personal_email'            => 'nullable|email|max:255',
            'permanent_address'         => 'nullable|string|max:500',
            'gender'                    => 'nullable|in:male,female,other',
            'marital_status'            => 'nullable|in:single,married,divorced,widowed',
            'profile_photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'employment_type'           => 'nullable|string|in:full_time,part_time,contract,intern',
            'employment_status'         => 'nullable|string|in:probation,active,notice_period,resigned,terminated',
            'reporting_manager_id'      => 'nullable|exists:employees,id',
            'shift_id'                  => 'nullable|exists:work_shifts,id',
            'is_fresher'                => 'nullable|boolean',
            'total_experience_years'    => 'nullable|integer|min:0',
            'total_experience_months'   => 'nullable|integer|min:0|max:11',
            'previous_organization'     => 'nullable|string|max:255',
            'previous_designation'      => 'nullable|string|max:255',
            'internship_details'        => 'nullable|string|max:1000',
            'education_qualification'   => 'nullable|string|max:255',
            'education_institution'     => 'nullable|string|max:255',
            'education_passout_year'    => 'nullable|string|max:10',
            'education_score'           => 'nullable|string|max:50',
            'office_ssid'               => 'nullable|string|max:255',
            'bank_name'                 => 'nullable|string|max:100',
            'bank_account_number'       => 'nullable|string|max:50',
            'ifsc_code'                 => 'nullable|string|max:20',
            'pan_number'                => 'nullable|string|max:10',
            'aadhaar_number'            => 'nullable|string|max:20',
            'uan_number'                => 'nullable|string|max:20',
            'pf_number'                 => 'nullable|string|max:30',
            'emergency_contact_name'    => 'nullable|string|max:100',
            'emergency_relationship'    => 'nullable|string|max:50',
            'emergency_contact_phone'   => 'nullable|string|max:20',
            'emergency_alternate_phone' => 'nullable|string|max:20',
            'emergency_address'         => 'nullable|string|max:500',
            'is_active'                 => 'nullable|boolean',
            'employee_code'             => 'required|string|unique:employees,employee_code,' . $employee->id,
        ]);

        DB::beginTransaction();
        
        try {
            if ($request->hasFile('profile_photo')) {
                if ($employee->profile_photo) {
                    \Storage::disk('public')->delete($employee->profile_photo);
                }
                $profilePhotoPath = $request->file('profile_photo')
                    ->store('employee_photos', 'public');
                $employee->profile_photo = $profilePhotoPath;
            }

            $validated['is_active'] = $request->has('is_active');
            $validated['is_fresher'] = $request->has('is_fresher');

            if ($request->has('role')) {
                $validated['role'] = $request->input('role');
            }
            if ($request->has('is_team_leader') || $request->input('role') === 'team_leader') {
                $validated['is_team_leader'] = $request->boolean('is_team_leader') || ($request->input('role') === 'team_leader');
            }
            if ($request->input('role') === 'admin') {
                $validated['is_admin'] = true;
            }

            $employee->update($validated);

            DB::commit();

            cache()->forget('employee_departments');

            return redirect()->route('admin.employees.show', $employee)
                ->with('success', 'Employee updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee update failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to update employee. Please try again.');
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        // Check if employee can be deleted
        if ($employee->attendance()->exists() || $employee->payrolls()->exists()) {
            return back()->with('error', 'Cannot delete employee with existing attendance or payroll records.');
        }

        DB::beginTransaction();
        
        try {
            // Delete profile photo if exists
            if ($employee->profile_photo) {
                \Storage::disk('public')->delete($employee->profile_photo);
            }

            // Delete leave balances
            $employee->leaveBalances()->delete();
            
            // Delete employee
            $employee->delete();

            DB::commit();

            // Clear departments cache
            cache()->forget('employee_departments');

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee deletion failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete employee. Please try again.');
        }
    }

    /**
     * Toggle employee active status.
     */
    public function toggleStatus(Employee $employee): RedirectResponse
    {
        try {
            $employee->is_active = !$employee->is_active;
            $employee->save();

            $status = $employee->is_active ? 'activated' : 'deactivated';
            
            return back()->with('success', "Employee {$status} successfully.");
            
        } catch (\Exception $e) {
            Log::error('Employee status toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update employee status.');
        }
    }

    /**
     * Reset employee password.
     */
    public function resetPassword(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'password' => 'nullable|string|min:6|max:50'
        ]);

        try {
            // Generate new password
            $newPassword = $request->filled('password') 
                ? $request->password 
                : Str::random(8);
            
            $employee->password = Hash::make($newPassword);
            $employee->save();

            // Send password reset email
            try {
                Mail::send('emails.password_reset', [
                    'employee' => $employee,
                    'password' => $newPassword
                ], function ($message) use ($employee) {
                    $message->to($employee->email)
                            ->subject('Your Password Has Been Reset');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send password reset email: ' . $e->getMessage());
            }

            return back()->with('success', 'Password reset successfully. New password has been sent via email.');

        } catch (\Exception $e) {
            Log::error('Password reset failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reset password. Please try again.');
        }
    }

    /**
     * Export employees to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Employee::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $employees = $query->orderBy('created_at', 'desc')->get();

        $fileName = 'employees_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($employees) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'ID', 'Employee Code', 'Name', 'Email', 'Phone', 
                'Department', 'Designation', 'Employment Type', 'Basic Salary',
                'Date of Birth', 'Joining Date', 'Office WiFi SSID',
                'Bank Name', 'Bank Account Number', 'IFSC Code', 'PAN Number',
                'Emergency Contact Name', 'Emergency Relationship',
                'Emergency Contact Phone', 'Emergency Alternate Phone',
                'Emergency Address', 'Status', 'Created At',
            ]);

            // Data rows
            foreach ($employees as $employee) {
                fputcsv($handle, [
                    $employee->id,
                    $employee->employee_code,
                    $employee->name,
                    $employee->email,
                    $employee->phone,
                    $employee->department,
                    $employee->designation,
                    $employee->employment_type,
                    $employee->basic_salary,
                    $employee->date_of_birth,
                    $employee->joining_date,
                    $employee->office_ssid,
                    $employee->bank_name,
                    $employee->bank_account_number,
                    $employee->ifsc_code,
                    $employee->pan_number,
                    $employee->emergency_contact_name,
                    $employee->emergency_relationship,
                    $employee->emergency_contact_phone,
                    $employee->emergency_alternate_phone,
                    $employee->emergency_address,
                    $employee->is_active ? 'Active' : 'Inactive',
                    $employee->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk activate employees.
     */
    public function bulkActivate(Request $request): RedirectResponse
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return back()->with('error', 'No employees selected.');
        }

        try {
            Employee::whereIn('id', $ids)->update(['is_active' => true]);
            cache()->forget('employee_departments');
            return back()->with('success', 'Selected employees activated successfully.');
        } catch (\Exception $e) {
            Log::error('Bulk activation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to activate selected employees.');
        }
    }

    /**
     * Bulk deactivate employees.
     */
    public function bulkDeactivate(Request $request): RedirectResponse
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return back()->with('error', 'No employees selected.');
        }

        try {
            Employee::whereIn('id', $ids)->update(['is_active' => false]);
            cache()->forget('employee_departments');
            return back()->with('success', 'Selected employees deactivated successfully.');
        } catch (\Exception $e) {
            Log::error('Bulk deactivation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to deactivate selected employees.');
        }
    }

    /**
     * Bulk delete employees.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return back()->with('error', 'No employees selected.');
        }

        DB::beginTransaction();
        try {
            $employees = Employee::whereIn('id', $ids)->get();
            
            foreach ($employees as $employee) {
                // Check if employee can be deleted
                if ($employee->attendance()->exists() || $employee->payrolls()->exists()) {
                    continue; // Skip if they have records
                }
                
                if ($employee->profile_photo) {
                    \Storage::disk('public')->delete($employee->profile_photo);
                }
                $employee->leaveBalances()->delete();
                $employee->delete();
            }

            DB::commit();
            cache()->forget('employee_departments');
            return back()->with('success', 'Selected employees deleted successfully (except those with attendance/payroll records).');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete selected employees.');
        }
    }

    /**
     * Generate unique employee code.
     */
    private function generateEmployeeCode($date = null): string
    {
        return Employee::generateNextEmployeeCode($date);
    }

    /**
     * Upload document for employee (Admin side)
     */
    public function uploadDocument(Request $request, Employee $employee)
    {
        $request->validate([
            'document_type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $file = $request->file('document_file');
        $filePath = $file->store("employee_documents/{$employee->id}", 'public');

        $docType = $request->document_type;
        $title = $request->title;

        try {
            \App\Models\EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_type' => $docType,
                'title' => $title,
                'document_number' => $request->document_number,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => 'verified',
                'verified_by' => auth('admin')->id(),
                'verified_at' => now(),
            ]);
        } catch (\Exception $e) {
            $fallbackType = in_array($docType, ['contract', 'id_proof', 'certificate', 'experience_letter', 'resume', 'other']) ? $docType : 'other';
            \App\Models\EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_type' => $fallbackType,
                'title' => "[" . strtoupper($docType) . "] " . $title,
                'document_number' => $request->document_number,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => 'verified',
                'verified_by' => auth('admin')->id(),
                'verified_at' => now(),
            ]);
        }

        return back()->with('success', 'Document uploaded and verified successfully.');
    }

    /**
     * Verify document
     */
    public function verifyDocument(Request $request, \App\Models\EmployeeDocument $document)
    {
        $document->update([
            'status' => 'verified',
            'rejection_reason' => null,
            'verified_by' => auth('admin')->id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Document verified successfully.');
    }

    /**
     * Reject document
     */
    public function rejectDocument(Request $request, \App\Models\EmployeeDocument $document)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'verified_by' => auth('admin')->id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Document rejected.');
    }

    /**
     * Update employee lifecycle status
     */
    public function updateLifecycleStatus(Request $request, Employee $employee)
    {
        $request->validate([
            'employment_status' => 'required|in:probation,active,notice_period,resigned,terminated',
            'remarks' => 'nullable|string|max:500',
            'status_date' => 'nullable|date',
            'termination_reason' => 'nullable|string|max:500',
        ]);

        $previousStatus = $employee->employment_status;
        $newStatus = $request->employment_status;

        $updateData = [
            'employment_status' => $newStatus,
        ];

        if ($newStatus === 'resigned') {
            $updateData['resignation_date'] = $request->status_date ?? now();
            $updateData['is_active'] = false;
        } elseif ($newStatus === 'terminated') {
            $updateData['termination_date'] = $request->status_date ?? now();
            $updateData['termination_reason'] = $request->termination_reason;
            $updateData['is_active'] = false;
        } elseif ($newStatus === 'active') {
            $updateData['is_active'] = true;
        }

        $employee->update($updateData);

        \App\Models\EmployeeLifecycleHistory::create([
            'employee_id' => $employee->id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'change_date' => $request->status_date ?? now(),
            'remarks' => $request->remarks,
            'changed_by_admin_id' => auth('admin')->id(),
        ]);

        return back()->with('success', 'Employee lifecycle status updated successfully.');
    }

    public function updateManager(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'reporting_manager_id' => 'nullable|exists:employees,id',
        ]);

        if ($request->reporting_manager_id == $employee->id) {
            return back()->withErrors(['reporting_manager_id' => 'An employee cannot be assigned as their own reporting manager.']);
        }

        $employee->update([
            'reporting_manager_id' => $request->reporting_manager_id ?: null,
        ]);

        return back()->with('success', 'Reporting manager updated successfully.');
    }

    public function updateShift(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'shift_id' => 'nullable|exists:work_shifts,id',
        ]);

        $employee->update([
            'shift_id' => $request->shift_id ?: null,
        ]);

        return back()->with('success', 'Work shift allocated successfully.');
    }

    public function storeShift(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'grace_period_minutes'   => 'nullable|integer|min:0|max:120',
            'break_duration_minutes' => 'nullable|integer|min:0|max:240',
            'employee_id'            => 'nullable|exists:employees,id',
        ]);

        // Resolve main shift times (12-hr selects or raw input)
        if ($request->filled('start_time_hour')) {
            $startStr = $request->start_time_hour . ':' . ($request->start_time_min ?? '00') . ':00 ' . ($request->start_time_ampm ?? 'AM');
            $endStr   = $request->end_time_hour . ':' . ($request->end_time_min ?? '00') . ':00 ' . ($request->end_time_ampm ?? 'PM');
        } else {
            $startStr = $request->input('start_time', '09:00:00 AM');
            $endStr   = $request->input('end_time', '06:00:00 PM');
        }
        $startCarbon = \Carbon\Carbon::parse($startStr);
        $endCarbon   = \Carbon\Carbon::parse($endStr);
        $startTime   = $startCarbon->format('H:i:s');
        $endTime     = $endCarbon->format('H:i:s');

        // Calculate shift duration
        $endForDiff = clone $endCarbon;
        if ($endForDiff->lt($startCarbon)) { $endForDiff->addDay(); }
        $diff = $startCarbon->diff($endForDiff);
        $shiftDuration = sprintf('%02d:%02d', $diff->h + ($diff->days * 24), $diff->i);

        // Helper: parse a break block from 12-hr select fields (e.g. break1_begin_hour etc)
        $parseBreakTime = function (string $prefix) use ($request) {
            $h   = $request->input("{$prefix}_hour");
            $m   = $request->input("{$prefix}_min", '00');
            $ap  = $request->input("{$prefix}_ampm", 'AM');
            if (!$h) { return null; }
            return \Carbon\Carbon::parse("{$h}:{$m}:00 {$ap}");
        };

        $breakData = [];
        foreach (range(1, 6) as $n) {
            $enabled = $request->boolean("break{$n}_enabled");
            $breakData["break{$n}_enabled"] = $enabled;
            if ($enabled) {
                $bStart = $parseBreakTime("break{$n}_begin");
                $bEnd   = $parseBreakTime("break{$n}_end");
                if ($bStart && $bEnd) {
                    $bEndCopy = clone $bEnd;
                    if ($bEndCopy->lt($bStart)) { $bEndCopy->addDay(); }
                    $bDiff = $bStart->diff($bEndCopy);
                    $breakData["break{$n}_begin_time"] = $bStart->format('H:i:s');
                    $breakData["break{$n}_end_time"]   = $bEnd->format('H:i:s');
                    $breakData["break{$n}_duration"]   = sprintf('%02d:%02d', $bDiff->h + ($bDiff->days * 24), $bDiff->i);
                } else {
                    $breakData["break{$n}_begin_time"] = null;
                    $breakData["break{$n}_end_time"]   = null;
                    $breakData["break{$n}_duration"]   = null;
                }
            } else {
                $breakData["break{$n}_begin_time"] = null;
                $breakData["break{$n}_end_time"]   = null;
                $breakData["break{$n}_duration"]   = null;
            }
        }

        $shift = \App\Models\WorkShift::create(array_merge([
            'name'                   => $request->name,
            'start_time'             => $startTime,
            'end_time'               => $endTime,
            'shift_duration'         => $shiftDuration,
            'grace_period_minutes'   => $request->grace_period_minutes ?? 15,
            'break_duration_minutes' => $request->break_duration_minutes ?? 60,
            'is_default'             => false,
        ], $breakData));

        if ($request->employee_id) {
            $emp = Employee::find($request->employee_id);
            if ($emp) {
                $emp->update(['shift_id' => $shift->id]);
            }
        }

        return back()->with('success', "New Work Shift '{$shift->name}' created and allocated successfully.");
    }

    public function addExperience(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'job_description' => 'nullable|string|max:1000',
        ]);

        $employee->update(['is_fresher' => false]);

        EmployeeExperience::create([
            'employee_id' => $employee->id,
            'company_name' => $validated['company_name'],
            'designation' => $validated['designation'],
            'start_date' => $validated['start_date'],
            'end_date' => $request->boolean('is_current_job') ? null : $validated['end_date'],
            'is_current_job' => $request->boolean('is_current_job'),
            'job_description' => $validated['job_description'] ?? null,
        ]);

        return back()->with('success', 'Work experience entry added successfully.');
    }

    public function deleteExperience($id): RedirectResponse
    {
        $exp = EmployeeExperience::findOrFail($id);
        $exp->delete();

        return back()->with('success', 'Work experience record deleted successfully.');
    }

    public function addEducation(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'level' => 'required|string|max:100',
            'degree_title' => 'required|string|max:255',
            'institution_name' => 'required|string|max:255',
            'passout_year' => 'nullable|string|max:10',
            'score_or_grade' => 'nullable|string|max:50',
        ]);

        EmployeeEducation::create([
            'employee_id' => $employee->id,
            'level' => $validated['level'],
            'degree_title' => $validated['degree_title'],
            'institution_name' => $validated['institution_name'],
            'passout_year' => $validated['passout_year'] ?? null,
            'score_or_grade' => $validated['score_or_grade'] ?? null,
        ]);

        return back()->with('success', 'Educational qualification entry added successfully.');
    }

    public function deleteEducation($id): RedirectResponse
    {
        $edu = EmployeeEducation::findOrFail($id);
        $edu->delete();

        return back()->with('success', 'Educational qualification record deleted successfully.');
    }

    /**
     * Toggle Admin Role & Portal Privileges for Employee.
     */
    public function toggleAdminRole(Employee $employee, Request $request)
    {
        $currentIsAdmin = $employee->is_admin || $employee->role === 'admin' || $employee->role === 'manager';
        $newAdminState = !$currentIsAdmin;
        $newRole = $newAdminState ? 'admin' : 'employee';
        
        $employee->update([
            'role' => $newRole,
            'is_admin' => $newAdminState
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_admin' => $newAdminState,
                'role' => $newRole,
                'message' => $newAdminState 
                    ? "Admin privileges ENABLED for {$employee->name}" 
                    : "Admin privileges DISABLED for {$employee->name}"
            ]);
        }
        
        return redirect()->back()->with('success', $newAdminState 
            ? "Admin privileges ENABLED for {$employee->name}" 
            : "Admin privileges DISABLED for {$employee->name}");
    }

    /**
     * Toggle Team Leader (TL) Designation for Employee.
     */
    public function toggleTlRole(Employee $employee, Request $request)
    {
        $newTlState = !$employee->is_team_leader;
        $employee->is_team_leader = $newTlState;
        if ($newTlState && $employee->role === 'employee') {
            $employee->role = 'team_leader';
        } elseif (!$newTlState && $employee->role === 'team_leader') {
            $employee->role = 'employee';
        }
        $employee->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_team_leader' => $newTlState,
                'role' => $employee->role,
                'message' => $newTlState 
                    ? "Team Leader (TL) designation ENABLED for {$employee->name}" 
                    : "Team Leader (TL) designation REMOVED for {$employee->name}"
            ]);
        }
        
        return redirect()->back()->with('success', $newTlState 
            ? "Team Leader (TL) designation ENABLED for {$employee->name}" 
            : "Team Leader (TL) designation REMOVED for {$employee->name}");
    }

    /**
     * Update Individual Employee PF Settings (Enable/Disable, PF Rate %, Fixed Amount).
     */
    public function updatePfSettings(Employee $employee, Request $request)
    {
        $pfEnabled = $request->boolean('pf_enabled');
        $pfRate = $request->input('pf_rate', 12.00);
        $pfAmount = $request->input('pf_amount') ?: null;
        $esiEnabled = $request->has('esi_enabled') ? $request->boolean('esi_enabled') : $employee->esi_enabled;
        $ptEnabled = $request->has('pt_enabled') ? $request->boolean('pt_enabled') : $employee->pt_enabled;
        $tdsEnabled = $request->has('tds_enabled') ? $request->boolean('tds_enabled') : $employee->tds_enabled;

        $employee->update([
            'pf_enabled'  => $pfEnabled,
            'pf_rate'     => $pfRate,
            'pf_amount'   => $pfAmount,
            'esi_enabled' => $esiEnabled,
            'pt_enabled'  => $ptEnabled,
            'tds_enabled' => $tdsEnabled,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'pf_enabled' => $employee->pf_enabled,
                'pf_rate' => $employee->pf_rate,
                'pf_amount' => $employee->pf_amount,
                'esi_enabled' => $employee->esi_enabled,
                'pt_enabled' => $employee->pt_enabled,
                'tds_enabled' => $employee->tds_enabled,
                'message' => "Statutory Settings updated successfully for {$employee->name}."
            ]);
        }

        return redirect()->back()->with('success', "Statutory Settings updated successfully for {$employee->name}.");
    }

}