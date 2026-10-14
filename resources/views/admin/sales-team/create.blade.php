@extends('layouts.admin')

@section('title', 'Add Sales Team Member')
@section('page-title', 'Add Sales Team Member')
@section('page-subtitle', 'Provision a new authorized sales executive account with pipeline access')

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
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-user-plus fs-5"></i>
                <h5 class="fw-bold mb-0">Create Sales Executive Account</h5>
            </div>
        </div>

        <form action="{{ route('admin.sales-team.store') }}" method="POST">
            @csrf
            <div class="card-body p-4 p-md-5">

                <div class="alert alert-light border mb-4" style="border-radius: 8px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-shield-alt text-success fs-5"></i>
                        <span class="small text-muted">
                            Only Administrator can provision Sales Team accounts. The sales rep can log in via both the <strong>Web Sales Portal</strong> and the <strong>Mobile App</strong>.
                        </span>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">
                    <i class="fas fa-id-card text-success me-2"></i> Account & Profile Information
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Employee Code <span class="text-muted fw-normal">(Auto-suggested)</span></label>
                        <input type="text" name="employee_code" class="form-control font-monospace" value="{{ old('employee_code', $suggestedCode) }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Email Address (Login Username) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="sales.rahul@dlktech.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Mobile Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="e.g. 9876543210" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Account Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Re-type password" required>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4 pt-2">
                    <i class="fas fa-briefcase text-success me-2"></i> Role & Employment Details
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Designation <span class="text-danger">*</span></label>
                        <select name="designation" class="form-select" required>
                            <option value="Sales Executive" {{ old('designation') === 'Sales Executive' ? 'selected' : '' }}>Sales Executive</option>
                            <option value="Senior Sales Consultant" {{ old('designation') === 'Senior Sales Consultant' ? 'selected' : '' }}>Senior Sales Consultant</option>
                            <option value="Business Development Executive" {{ old('designation') === 'Business Development Executive' ? 'selected' : '' }}>Business Development Executive</option>
                            <option value="Lead Generation Specialist" {{ old('designation') === 'Lead Generation Specialist' ? 'selected' : '' }}>Lead Generation Specialist</option>
                            <option value="Sales Manager" {{ old('designation') === 'Sales Manager' ? 'selected' : '' }}>Sales Manager</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Department</label>
                        <input type="text" class="form-control bg-light" value="Sales & Business Development" readonly>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Basic Monthly Salary (₹)</label>
                        <input type="number" step="0.01" name="basic_salary" class="form-control" placeholder="e.g. 25000" value="{{ old('basic_salary', '0') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">Address / Location</label>
                    <textarea name="address" rows="2" class="form-control" placeholder="Residential / Office location details...">{{ old('address') }}</textarea>
                </div>

            </div>

            <div class="card-footer bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.sales-team.index') }}" class="btn btn-sm btn-secondary px-3">Cancel</a>
                <button type="submit" class="btn btn-sm btn-success px-4 fw-bold text-white shadow-sm" style="background-color: #1e7c42; border-color: #1e7c42;">
                    <i class="fas fa-check-circle me-1 text-white"></i> Create Sales Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
