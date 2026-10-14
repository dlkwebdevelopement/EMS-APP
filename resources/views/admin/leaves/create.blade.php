{{-- resources/views/admin/leaves/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'New Leave Application')

@section('content')
<div class="container-fluid px-0">
    
    <!-- Hero Strip -->
    <div class="page-hero-strip mb-4 fade-in-up">
        <div class="strip-rule"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Create Leave Application</h2>
                <p class="text-muted small mb-0">Submit a new time-off request for an employee</p>
            </div>
            <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card fade-in-up" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-file-medical"></i></div>
                    Leave Application Form
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.leaves.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Employee Selection -->
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="fas fa-user-tie me-1"></i> Employee Select <span class="text-danger">*</span>
                                </label>
                                <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror shadow-none" required>
                                    <option value="">Choose an employee...</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_code }}) — {{ $employee->department }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Leave Type -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="fas fa-tags me-1"></i> Leave Category <span class="text-danger">*</span>
                                </label>
                                <select name="leave_type" class="form-select @error('leave_type') is-invalid @enderror shadow-none" required>
                                    <option value="">Select Type</option>
                                    <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>🏥 Sick Leave</option>
                                    <option value="casual" {{ old('leave_type') == 'casual' ? 'selected' : '' }}>☕ Casual Leave</option>
                                    <option value="annual" {{ old('leave_type') == 'annual' ? 'selected' : '' }}>🏖️ Annual Leave</option>
                                    <option value="unpaid" {{ old('leave_type') == 'unpaid' ? 'selected' : '' }}>⛔ Unpaid Leave</option>
                                    <option value="maternity" {{ old('leave_type') == 'maternity' ? 'selected' : '' }}>👶 Maternity Leave</option>
                                    <option value="paternity" {{ old('leave_type') == 'paternity' ? 'selected' : '' }}>👨‍🍼 Paternity Leave</option>
                                </select>
                                @error('leave_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Placeholder for dynamic balance check -->
                            <div class="col-md-6 d-flex align-items-end">
                                <div id="balance-info" class="p-2 rounded-pill bg-light border w-100 text-center small text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Select type to check balance
                                </div>
                            </div>
                            
                            <!-- Dates -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="fas fa-calendar-alt me-1"></i> Start Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror shadow-none" 
                                       value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="fas fa-calendar-check me-1"></i> End Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror shadow-none" 
                                       value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Reason -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="fas fa-comment-dots me-1"></i> Detailed Reason <span class="text-danger">*</span>
                                </label>
                                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror shadow-none" 
                                          rows="4" required placeholder="Describe the reason for leave application...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Attachments -->
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="fas fa-paperclip me-1"></i> Supporting Documents (Optional)
                                </label>
                                <div class="input-group">
                                    <input type="file" name="attachment" class="form-control shadow-none @error('attachment') is-invalid @enderror">
                                    <span class="input-group-text bg-light border-start-0"><i class="fas fa-upload text-muted"></i></span>
                                </div>
                                <small class="text-muted mt-1 d-block italic">Allow: PDF, JPG, PNG (Max 2MB)</small>
                                @error('attachment')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Submission -->
                            <div class="col-12 pt-3 border-top mt-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i> Application will be submitted as <strong>Pending</strong>
                                    </div>
                                    <button type="submit" class="btn btn-success px-5 fw-bold">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Application
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Guidance Sidebar -->
        <div class="col-lg-4">
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <i class="fas fa-clock fa-2x" style="color: var(--gl);"></i>
                        <div>
                            <h6 class="mb-1" style="color: var(--gd);">Approval Process</h6>
                            <p class="small text-muted mb-0">Leave requests require admin approval before being finalized.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <i class="fas fa-calendar-week fa-2x" style="color: var(--gl);"></i>
                        <div>
                            <h6 class="mb-1" style="color: var(--gd);">Notice Period</h6>
                            <p class="small text-muted mb-0">Submit leave requests at least 3 days in advance.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="fas fa-file-alt fa-2x" style="color: var(--gl);"></i>
                        <div>
                            <h6 class="mb-1" style="color: var(--gd);">Documentation</h6>
                            <p class="small text-muted mb-0">Medical certificates required for sick leaves exceeding 3 days.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card mt-4 fade-in-up" style="animation-delay: 0.15s;">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-chart-simple"></i>
                    </div>
                    Quick Stats
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Pending Approvals</span>
                        <span class="badge badge-gold fs-6">{{ App\Models\Leave::where('status', 'pending')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Approved This Month</span>
                        <span class="badge badge-lime fs-6">
                            {{ App\Models\Leave::where('status', 'approved')->whereMonth('created_at', now()->month)->count() }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Leave Days</span>
                        <span class="badge badge-green fs-6">
                            {{ App\Models\Leave::whereYear('created_at', now()->year)->sum('total_days') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection