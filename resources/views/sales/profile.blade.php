@extends('layouts.admin')

@section('title', 'Sales Profile — ' . $salesUser->name)
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your personal details, credentials, and password')

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
                            {{ strtoupper(substr($salesUser->name, 0, 2)) }}
                        </div>
                        <div class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1 border border-2 border-white" title="Active Account">
                            <i class="fas fa-check-circle text-white" style="font-size: 0.75rem;"></i>
                        </div>
                    </div>

                    <h4 class="fw-bold text-dark mb-1">{{ $salesUser->name }}</h4>
                    <div class="text-muted small mb-3">{{ $salesUser->designation ?? 'Sales Executive' }} &bull; Sales & Marketing</div>

                    {{-- Employee Code Highlight --}}
                    <div class="p-3 rounded-3 mb-3 border text-center" 
                         style="background: #f0fdf4; border-color: #86efac !important;">
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Official Employee Code</div>
                        <div class="font-monospace fw-bold text-success fs-5 mt-1">
                            {{ $salesUser->employee_code ?? 'DLK-SALES' }}
                        </div>
                    </div>

                    <div class="text-start border-top pt-3 space-y-2">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small"><i class="fas fa-envelope me-1 text-success"></i> Email</span>
                            <span class="fw-semibold small text-dark">{{ $salesUser->email }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small"><i class="fas fa-phone me-1 text-success"></i> Phone</span>
                            <span class="fw-semibold small text-dark">{{ $salesUser->phone ?? 'Not recorded' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small"><i class="fas fa-id-badge me-1 text-success"></i> Role</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">Sales Operations</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small"><i class="fas fa-calendar-check me-1 text-success"></i> Joined On</span>
                            <span class="fw-semibold small text-dark">{{ optional($salesUser->created_at)->format('d M Y') ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Links Card --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-compass me-2 text-success"></i> Quick Access</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('sales.dashboard') }}" class="btn btn-sm btn-outline-success text-start fw-semibold py-2">
                            <i class="fas fa-th-large me-2"></i> Lead Portal Dashboard
                        </a>
                        <a href="{{ route('sales.leads.index') }}" class="btn btn-sm btn-outline-primary text-start fw-semibold py-2">
                            <i class="fas fa-funnel-dollar me-2"></i> Website Leads Pipeline
                        </a>
                        <a href="{{ route('sales.support') }}" class="btn btn-sm btn-outline-secondary text-start fw-semibold py-2">
                            <i class="fas fa-headset me-2"></i> Help Desk Support
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Update Forms --}}
        <div class="col-lg-8">
            {{-- Personal Details Form --}}
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-user-edit text-success"></i> Edit Profile Information
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="{{ route('sales.profile.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $salesUser->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $salesUser->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Contact Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $salesUser->phone) }}" placeholder="+91 98765 43210">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Designation</label>
                                <input type="text" class="form-control bg-light" value="{{ $salesUser->designation ?? 'Sales Executive' }}" disabled readonly>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn text-white fw-bold px-4" 
                                    style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); border-radius: 10px;">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password Form --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-key text-warning"></i> Security & Password Management
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="{{ route('sales.change-password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">New Password</label>
                                <input type="password" name="new_password" class="form-control" required placeholder="At least 6 characters">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control" required placeholder="Repeat new password">
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-4" style="border-radius: 10px;">
                                <i class="fas fa-lock me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
