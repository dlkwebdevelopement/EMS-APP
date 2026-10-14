@extends('layouts.admin')

@section('title', 'Edit Sales Representative')
@section('page-title', 'Edit Sales Representative')
@section('page-subtitle', 'Update representative profile, designation, pipeline access, and security credentials')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">

    {{-- Breadcrumb & Back --}}
    <div class="mb-3">
        <a href="{{ route('admin.sales-team.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Sales Team
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-exclamation-circle fs-5"></i>
                <strong>Please correct the following errors:</strong>
            </div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md); overflow: hidden;">
        <div class="card-header bg-success text-white py-3" style="background: linear-gradient(135deg, #1e7c42, #0d3b1a) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-user-edit fs-5"></i>
                    <h5 class="fw-bold mb-0">Edit: {{ $employee->name }}</h5>
                </div>
                <span class="badge bg-light text-dark font-monospace">{{ $employee->employee_code }}</span>
            </div>
        </div>

        <form action="{{ route('admin.sales-team.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body p-4 p-md-5">

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">
                    <i class="fas fa-id-card text-success me-2"></i> Profile & Credentials
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Employee Code</label>
                        <input type="text" name="employee_code" class="form-control font-monospace" value="{{ old('employee_code', $employee->employee_code) }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Email Address (Login Username) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Mobile Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" required>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4 pt-2">
                    <i class="fas fa-lock text-success me-2"></i> Password Management <span class="text-muted fw-normal fs-6">(Leave blank to keep existing password)</span>
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Re-type new password">
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4 pt-2">
                    <i class="fas fa-briefcase text-success me-2"></i> Role & Employment Details
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Designation <span class="text-danger">*</span></label>
                        <select name="designation" class="form-select" required>
                            <option value="Sales Executive" {{ old('designation', $employee->designation) === 'Sales Executive' ? 'selected' : '' }}>Sales Executive</option>
                            <option value="Senior Sales Consultant" {{ old('designation', $employee->designation) === 'Senior Sales Consultant' ? 'selected' : '' }}>Senior Sales Consultant</option>
                            <option value="Business Development Executive" {{ old('designation', $employee->designation) === 'Business Development Executive' ? 'selected' : '' }}>Business Development Executive</option>
                            <option value="Lead Generation Specialist" {{ old('designation', $employee->designation) === 'Lead Generation Specialist' ? 'selected' : '' }}>Lead Generation Specialist</option>
                            <option value="Sales Manager" {{ old('designation', $employee->designation) === 'Sales Manager' ? 'selected' : '' }}>Sales Manager</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Account Access Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $employee->is_active) ? 'selected' : '' }}>✅ Active - Full Portal & App Access</option>
                            <option value="0" {{ !old('is_active', $employee->is_active) ? 'selected' : '' }}>🚫 Deactivated - Login Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Basic Monthly Salary (₹)</label>
                        <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ old('basic_salary', $employee->basic_salary) }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">Address / Location</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $employee->address) }}</textarea>
                </div>

            </div>

            <div class="card-footer bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.sales-team.index') }}" class="btn btn-sm btn-secondary px-3">Cancel</a>
                <button type="submit" class="btn btn-sm btn-success px-4 fw-bold text-white shadow-sm" style="background-color: #1e7c42; border-color: #1e7c42;">
                    <i class="fas fa-save me-1 text-white"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
