{{-- resources/views/admin/employees/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Add Employee')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <h1 class="h3 mb-0" style="color: #1b5e1f;">
            <i class="fas fa-user-plus me-2" style="color: #ffd700;"></i>Add New Employee
        </h1>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary" style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm fade-in-up" style="border-radius: 15px; border: none;">
        <div class="card-header bg-white py-3" style="border-bottom: 2px solid #ffd700; border-radius: 15px 15px 0 0;">
            <ul class="nav nav-tabs card-header-tabs" id="employeeTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                        <i class="fas fa-user me-1"></i> Personal
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="employment-tab" data-bs-toggle="tab" data-bs-target="#employment" type="button" role="tab">
                        <i class="fas fa-briefcase me-1"></i> Employment
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" role="tab">
                        <i class="fas fa-university me-1"></i> Bank Details
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" type="button" role="tab">
                        <i class="fas fa-phone-alt me-1"></i> Emergency
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="tab-content">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="personal">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required style="border-radius: 10px;">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required style="border-radius: 10px;">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required minlength="6" style="border-radius: 10px;">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone') }}" required maxlength="15" pattern="[0-9\+\-\s]+" title="Please enter a valid phone number" style="border-radius: 10px;">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Date of Birth</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth') }}" style="border-radius: 10px;">
                                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="2" style="border-radius: 10px;">{{ old('address') }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Profile Photo</label>
                                <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" name="profile_photo" accept="image/*" style="border-radius: 10px;">
                                <small class="text-muted">Recommended size: 300x300px</small>
                                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Employment Information Tab -->
                    <div class="tab-pane fade" id="employment">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Employee Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('employee_code') is-invalid @enderror" 
                                       id="employee_code" name="employee_code" value="{{ old('employee_code', $suggestedCode) }}" required style="border-radius: 10px;">
                                @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Department <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                       name="department" value="{{ old('department') }}" required style="border-radius: 10px;">
                                @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Designation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                                       name="designation" value="{{ old('designation') }}" required style="border-radius: 10px;">
                                @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold" style="color: #1b5e1f;">
                                    <i class="fas fa-user-shield me-1"></i> System Role & Position <span class="text-danger">*</span>
                                </label>
                                <select class="form-select border-success" name="role" style="border-radius: 10px;">
                                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee / Staff Worker</option>
                                    <option value="team_leader" {{ old('role') == 'team_leader' ? 'selected' : '' }}>⭐ Team Leader (TL Position)</option>
                                    <option value="sales" {{ old('role') == 'sales' ? 'selected' : '' }}>💼 Sales Executive / Sales Team</option>
                                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">Designate as Team Leader (TL)</div>
                                            <div class="small text-muted">Authorizes this employee to receive leads and assign multi-employee project workers with completion dates.</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_team_leader" id="isTeamLeaderCreateSwitch" value="1" style="width: 2.8em; height: 1.5em; cursor: pointer;" {{ old('is_team_leader') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold ms-2" for="isTeamLeaderCreateSwitch">
                                            Enable TL
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Employment Type</label>
                                <select class="form-select @error('employment_type') is-invalid @enderror" name="employment_type" style="border-radius: 10px;">
                                    <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="intern" {{ old('employment_type') == 'intern' ? 'selected' : '' }}>Intern</option>
                                </select>
                                @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Joining Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('joining_date') is-invalid @enderror" 
                                       id="joining_date" name="joining_date" value="{{ old('joining_date') }}" required style="border-radius: 10px;">
                                @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Basic Salary <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: #ffd700; color: #1b5e1f;">₹</span>
                                    <input type="number" step="0.01" min="0" max="9999999" class="form-control @error('basic_salary') is-invalid @enderror" 
                                           name="basic_salary" value="{{ old('basic_salary') }}" required style="border-radius: 0 10px 10px 0;">
                                </div>
                                @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Office WiFi SSID</label>
                                <input type="text" class="form-control @error('office_ssid') is-invalid @enderror" name="office_ssid" value="{{ old('office_ssid', 'office_wifi') }}" style="border-radius: 10px;">
                                <small class="text-muted">Auto-attendance will work on this WiFi</small>
                                @error('office_ssid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bank Details Tab -->
                    <div class="tab-pane fade" id="bank">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" value="{{ old('bank_name') }}" style="border-radius: 10px;">
                                @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Account Number</label>
                                <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" name="bank_account_number" value="{{ old('bank_account_number') }}" maxlength="30" style="border-radius: 10px;">
                                @error('bank_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">IFSC Code</label>
                                <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" name="ifsc_code" value="{{ old('ifsc_code') }}" style="border-radius: 10px;">
                                @error('ifsc_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">PAN Number</label>
                                <input type="text" class="form-control @error('pan_number') is-invalid @enderror" name="pan_number" value="{{ old('pan_number') }}" maxlength="10" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" title="Format: ABCDE1234F" style="text-transform: uppercase; border-radius: 10px;">
                                @error('pan_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Emergency Contact Tab -->
                    <div class="tab-pane fade" id="emergency">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Emergency Contact Name</label>
                                <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" style="border-radius: 10px;">
                                @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Emergency Contact Phone</label>
                                <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" maxlength="15" pattern="[0-9\+\-\s]+" style="border-radius: 10px;">
                                @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Relationship</label>
                                <select class="form-control @error('emergency_relationship') is-invalid @enderror" name="emergency_relationship" style="border-radius: 10px;">
                                    <option value="">Select Relationship</option>
                                    <option value="spouse" {{ old('emergency_relationship') == 'spouse' ? 'selected' : '' }}>Spouse</option>
                                    <option value="parent" {{ old('emergency_relationship') == 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="sibling" {{ old('emergency_relationship') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                    <option value="friend" {{ old('emergency_relationship') == 'friend' ? 'selected' : '' }}>Friend</option>
                                    <option value="other" {{ old('emergency_relationship') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('emergency_relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Alternate Phone</label>
                                <input type="text" class="form-control @error('emergency_alternate_phone') is-invalid @enderror" name="emergency_alternate_phone" value="{{ old('emergency_alternate_phone') }}" maxlength="15" pattern="[0-9\+\-\s]+" style="border-radius: 10px;">
                                @error('emergency_alternate_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" style="color: #1b5e1f;">Emergency Address</label>
                                <textarea class="form-control @error('emergency_address') is-invalid @enderror" name="emergency_address" rows="2" style="border-radius: 10px;">{{ old('emergency_address') }}</textarea>
                                @error('emergency_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #2e7d32, #1b5e1f); color: #ffd700; border-radius: 10px; padding: 10px 30px;">
                        <i class="fas fa-save me-1"></i> Create Employee
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary" style="border-radius: 10px;">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fade-in-up {
        animation: slideIn 0.5s ease-out forwards;
        opacity: 0;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .nav-tabs .nav-link {
        color: #2e7d32;
        border: none;
        padding: 10px 20px;
        transition: all 0.3s;
    }
    
    .nav-tabs .nav-link.active {
        color: #ffd700;
        background: linear-gradient(135deg, #2e7d32, #1b5e1f);
        border-radius: 10px;
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        color: #ffd700;
        background: #e8f5e9;
        border-radius: 10px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #ffd700;
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
    }
</style>

@push('scripts')
<script>
    document.getElementById('joining_date')?.addEventListener('change', function() {
        const joinDate = this.value;
        const codeInput = document.getElementById('employee_code');
        
        if (joinDate && codeInput) {
            const yearShort = joinDate.substring(2, 4);
            const currentCode = codeInput.value;
            
            // Only auto-update if the code follows our default DLKYY@ pattern
            // or if it's empty
            if (!currentCode || currentCode.match(/^DLK\d{2}@/)) {
                if (currentCode) {
                    // Replace the YY part
                    codeInput.value = 'DLK' + yearShort + currentCode.substring(5);
                } else {
                    // Start a new one
                    codeInput.value = 'DLK' + yearShort + '@001';
                }
            }
        }
    });
</script>
@endpush
@endsection