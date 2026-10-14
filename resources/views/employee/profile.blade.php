@extends('layouts.admin')

@section('title', 'My Profile — ' . $employee->name)
@section('page-title', 'Employee Profile')
@section('page-subtitle', 'Manage your personal details, emergency contacts, and account security')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #eaf6ed; color: #0d3b1a;">
            <i class="fas fa-check-circle me-2 text-success"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- LEFT COLUMN: Identification Card & Info --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-body p-4 text-center">
                    {{-- Avatar --}}
                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow mx-auto"
                             style="width: 100px; height: 100px; background: linear-gradient(135deg, #0d3b1a, #1e7c42); font-size: 2.2rem; border: 3px solid #e2e8f0;">
                            @if($employee->profile_photo)
                                <img src="{{ Storage::url($employee->profile_photo) }}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                            @else
                                {{ strtoupper(substr($employee->name, 0, 2)) }}
                            @endif
                        </div>
                    </div>

                    <h4 class="fw-bold text-dark mb-1">{{ $employee->name }}</h4>
                    <div class="text-muted small mb-3">{{ $employee->designation ?? 'Staff Member' }} &bull; {{ $employee->department ?? 'General' }}</div>

                    {{-- Employee Code Highlight --}}
                    <div class="p-3 rounded-3 mb-3 border text-center" 
                         style="background: #f0fdf4; border-color: #86efac !important;">
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Employee Code</div>
                        <div class="fs-5 fw-bold font-monospace text-success">
                            <i class="fas fa-id-badge me-1"></i> {{ $employee->employee_code }}
                        </div>
                        <div class="small text-muted mt-1" style="font-size: 0.72rem;">Official Company Identification</div>
                    </div>

                    <div class="list-group list-group-flush text-start small">
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="far fa-envelope me-2 text-primary"></i> Email</span>
                            <span class="fw-semibold text-dark">{{ $employee->email }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-phone-alt me-2 text-success"></i> Phone</span>
                            <span class="fw-semibold text-dark">{{ $employee->phone ?? '-' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="far fa-calendar-alt me-2 text-warning"></i> Joining Date</span>
                            <span class="fw-semibold text-dark">{{ optional($employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date) : null)->format('d M Y') ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-user-tag me-2 text-secondary"></i> Status</span>
                            <span class="badge {{ $employee->is_active ? 'bg-success' : 'bg-danger' }}">{{ $employee->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Update Profile & Change Password --}}
        <div class="col-lg-8">
            {{-- Update Profile Form --}}
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-user-cog text-success me-2"></i> Update Contact & Personal Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Employee Code</label>
                                <input type="text" class="form-control font-monospace bg-light" value="{{ $employee->employee_code }}" readonly disabled>
                                <small class="text-muted">Assigned by Administration — Non-editable</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Mobile Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Profile Photo</label>
                                <input type="file" name="profile_photo" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Emergency Contact Person</label>
                                <input type="text" name="emergency_contact_name" class="form-control" 
                                       value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}" placeholder="Name of parent / spouse">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Emergency Phone Number</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" 
                                       value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}" placeholder="+91 98765 43210">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark">Residential Address</label>
                            <textarea name="address" rows="3" class="form-control" placeholder="Street, City, Pincode...">{{ old('address', $employee->address) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success px-4 fw-bold text-white shadow-sm" 
                                style="background-color: #1e7c42; border-color: #1e7c42; border-radius: 8px;">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            {{-- Change Password Form --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-lock text-primary me-2"></i> Security & Password
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('employee.change-password') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">New Password <span class="text-danger">*</span></label>
                                <input type="password" name="new_password" class="form-control" minlength="6" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="new_password_confirmation" class="form-control" minlength="6" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-outline-secondary px-4 fw-semibold" style="border-radius: 8px;">
                            <i class="fas fa-key me-1"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
