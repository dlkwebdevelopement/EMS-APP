{{-- resources/views/admin/employees/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Employee Profile')
@section('page-title', 'Edit Employee Profile')
@section('page-subtitle', 'Update complete profile information for ' . $employee->name)

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold d-flex align-items-center justify-content-between">
            <span><i class="fas fa-edit text-primary me-2"></i> Edit Employee: {{ $employee->name }}</span>
            <div>
                <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-sm btn-outline-primary me-2">
                    <i class="fas fa-eye me-1"></i> View Profile
                </a>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                {{-- Navigation Tabs --}}
                <ul class="nav nav-tabs mb-4" id="employeeEditTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                            <i class="fas fa-user me-1 text-primary"></i> Personal Details
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="employment-tab" data-bs-toggle="tab" data-bs-target="#employment" type="button" role="tab">
                            <i class="fas fa-briefcase me-1 text-success"></i> Employment & Org
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience" type="button" role="tab">
                            <i class="fas fa-history me-1 text-warning"></i> Experience & Internship
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="education-tab" data-bs-toggle="tab" data-bs-target="#education" type="button" role="tab">
                            <i class="fas fa-graduation-cap me-1 text-info"></i> Education
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" role="tab">
                            <i class="fas fa-university me-1 text-secondary"></i> Bank & Statutory
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    {{-- TAB 1: Personal Details --}}
                    <div class="tab-pane fade show active" id="personal">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $employee->name) }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Official Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $employee->email) }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Personal Email</label>
                                <input type="email" class="form-control" name="personal_email" value="{{ old('personal_email', $employee->personal_email) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone', $employee->phone) }}" required maxlength="15" pattern="[0-9\+\-\s]+">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Marital Status</label>
                                <select name="marital_status" class="form-select">
                                    <option value="">Select Status</option>
                                    <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Current Address</label>
                                <textarea class="form-control" name="address" rows="2">{{ old('address', $employee->address) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Permanent Address</label>
                                <textarea class="form-control" name="permanent_address" rows="2">{{ old('permanent_address', $employee->permanent_address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: Employment & Org Hierarchy --}}
                    <div class="tab-pane fade" id="employment">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="department" value="{{ old('department', $employee->department) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Designation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="designation" value="{{ old('designation', $employee->designation) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">
                                    <i class="fas fa-user-shield me-1"></i> System Role & Position <span class="text-danger">*</span>
                                </label>
                                <select name="role" class="form-select border-success" id="employeeRoleSelect">
                                    <option value="employee" {{ old('role', $employee->role) == 'employee' ? 'selected' : '' }}>Employee / Staff Worker</option>
                                    <option value="team_leader" {{ old('role', $employee->role) == 'team_leader' || $employee->is_team_leader ? 'selected' : '' }}>⭐ Team Leader (TL Position)</option>
                                    <option value="sales" {{ old('role', $employee->role) == 'sales' ? 'selected' : '' }}>💼 Sales Executive / Sales Team</option>
                                    <option value="manager" {{ old('role', $employee->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="admin" {{ old('role', $employee->role) == 'admin' || $employee->is_admin ? 'selected' : '' }}>Administrator</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">Team Leader (TL) Position Authorization</div>
                                            <div class="small text-muted">Authorizes this employee to receive website/sales leads and assign multiple employees (workers) with project start & end dates.</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_team_leader" id="isTeamLeaderSwitch" value="1" style="width: 2.8em; height: 1.5em; cursor: pointer;" {{ old('is_team_leader', $employee->is_team_leader) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold ms-2" for="isTeamLeaderSwitch">
                                            {{ $employee->is_team_leader ? 'Active TL' : 'Grant TL' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Reporting Manager</label>
                                <select name="reporting_manager_id" class="form-select">
                                    <option value="">No Reporting Manager (Top Manager / Head)</option>
                                    @foreach($allEmployees as $mgr)
                                        <option value="{{ $mgr->id }}" {{ old('reporting_manager_id', $employee->reporting_manager_id) == $mgr->id ? 'selected' : '' }}>
                                            {{ $mgr->name }} ({{ $mgr->designation ?? 'Employee' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Work Shift & Roster</label>
                                <select name="shift_id" class="form-select">
                                    <option value="">Default General Shift (09:00 AM - 06:00 PM)</option>
                                    @foreach($shifts ?? \App\Models\WorkShift::all() as $sh)
                                        <option value="{{ $sh->id }}" {{ old('shift_id', $employee->shift_id) == $sh->id ? 'selected' : '' }}>
                                            {{ $sh->name }} ({{ \Carbon\Carbon::parse($sh->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($sh->end_time)->format('g:i A') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employment Lifecycle Status</label>
                                <select name="employment_status" class="form-select">
                                    <option value="active" {{ old('employment_status', $employee->employment_status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="probation" {{ old('employment_status', $employee->employment_status) == 'probation' ? 'selected' : '' }}>Probation</option>
                                    <option value="notice_period" {{ old('employment_status', $employee->employment_status) == 'notice_period' ? 'selected' : '' }}>Notice Period</option>
                                    <option value="resigned" {{ old('employment_status', $employee->employment_status) == 'resigned' ? 'selected' : '' }}>Resigned</option>
                                    <option value="terminated" {{ old('employment_status', $employee->employment_status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date of Joining <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="joining_date" value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Basic Monthly Salary (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="9999999" class="form-control" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: Experience & Internships --}}
                    <div class="tab-pane fade" id="experience">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" name="is_fresher" id="is_fresher" value="1" {{ old('is_fresher', $employee->is_fresher) ? 'checked' : '' }}>
                                    <label class="form-check-input-label fw-bold ms-2" for="is_fresher">Is Fresher (No Prior Full-Time Work Experience)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total Experience Years</label>
                                <input type="number" class="form-control" name="total_experience_years" value="{{ old('total_experience_years', $employee->total_experience_years ?? 0) }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total Experience Months</label>
                                <input type="number" class="form-control" name="total_experience_months" value="{{ old('total_experience_months', $employee->total_experience_months ?? 0) }}" min="0" max="11">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Previous Organization Name</label>
                                <input type="text" class="form-control" name="previous_organization" value="{{ old('previous_organization', $employee->previous_organization) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Previous Designation</label>
                                <input type="text" class="form-control" name="previous_designation" value="{{ old('previous_designation', $employee->previous_designation) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Internship Details</label>
                                <textarea class="form-control" name="internship_details" rows="3" placeholder="Enter internship company, role, duration, and certificates...">{{ old('internship_details', $employee->internship_details) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: Education Details --}}
                    <div class="tab-pane fade" id="education">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Highest Qualification / Degree</label>
                                <input type="text" class="form-control" name="education_qualification" value="{{ old('education_qualification', $employee->education_qualification) }}" placeholder="e.g. B.Tech Computer Science">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Institution / University</label>
                                <input type="text" class="form-control" name="education_institution" value="{{ old('education_institution', $employee->education_institution) }}" placeholder="e.g. Anna University">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Passout Year</label>
                                <input type="text" class="form-control" name="education_passout_year" value="{{ old('education_passout_year', $employee->education_passout_year) }}" placeholder="e.g. 2024">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Percentage / CGPA Score</label>
                                <input type="text" class="form-control" name="education_score" value="{{ old('education_score', $employee->education_score) }}" placeholder="e.g. 8.5 CGPA / 85%">
                            </div>
                        </div>
                    </div>

                    {{-- TAB 5: Bank & Statutory Details --}}
                    <div class="tab-pane fade" id="bank">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bank Name</label>
                                <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Number</label>
                                <input type="text" class="form-control" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">IFSC Code</label>
                                <input type="text" class="form-control" name="ifsc_code" value="{{ old('ifsc_code', $employee->ifsc_code) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">PAN Number</label>
                                <input type="text" class="form-control" name="pan_number" value="{{ old('pan_number', $employee->pan_number) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Aadhaar Number</label>
                                <input type="text" class="form-control" name="aadhaar_number" value="{{ old('aadhaar_number', $employee->aadhaar_number) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">UAN & PF Number</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="text" class="form-control" name="uan_number" value="{{ old('uan_number', $employee->uan_number) }}" placeholder="UAN">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" name="pf_number" value="{{ old('pf_number', $employee->pf_number) }}" placeholder="PF Number">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Save All Profile Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection