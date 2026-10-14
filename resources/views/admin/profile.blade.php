{{-- resources/views/admin/profile.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account settings and security preferences')

@section('content')
<div class="container-fluid px-0">

    <div class="row g-4 stagger-children">
        
        {{-- Left Column: Profile Info --}}
        <div class="col-xl-4">
            
            {{-- Profile Card --}}
            <div class="card">
                <div class="card-body text-center p-4">
                    {{-- Profile Avatar with organic ring --}}
                    <div class="position-relative d-inline-block mb-4">
                        <div class="avatar-wrapper" style="position: relative;">
                            <div class="avatar-ring" style="position: absolute; inset: -8px; border-radius: 50%; background: linear-gradient(135deg, var(--gl), var(--gb), var(--gl)); opacity: 0.6;"></div>
                            <div class="user-avatar mx-auto" style="width: 120px; height: 120px; font-size: 3rem; background: linear-gradient(135deg, var(--gb), var(--gk)); position: relative; z-index: 1;">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                        </div>
                        <div class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1 border border-2 border-white" style="z-index: 2;">
                            <i class="fas fa-check-circle text-white" style="font-size: 0.7rem;"></i>
                        </div>
                    </div>
                    
                    <h4 class="mb-1" style="color: var(--gd); font-family: 'Playfair Display', serif;">{{ $admin->name }}</h4>
                    <p class="text-muted small mb-3">{{ $admin->email }}</p>
                    
                    <div class="glass-panel p-3 mb-3" style="background: rgba(126, 213, 58, 0.05); border-color: rgba(126, 213, 58, 0.15);">
                        <div class="row">
                            <div class="col-6 border-end">
                                <div class="text-muted small text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 1px;">Role</div>
                                <div class="fw-bold">
                                    <span class="badge badge-green">Administrator</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 1px;">Status</div>
                                <div class="fw-bold">
                                    <span class="badge badge-lime">
                                        <i class="fas fa-check-circle me-1" style="font-size: 0.5rem;"></i>Active
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-start">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted text-uppercase">Account Created</div>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($admin->created_at)->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon bg-info-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-sign-in-alt text-info"></i>
                            </div>
                            <div>
                                <div class="small text-muted text-uppercase">Last Login</div>
                                <div class="fw-semibold">{{ $admin->last_login_at ? \Carbon\Carbon::parse($admin->last_login_at)->format('d M Y, h:i A') : 'Never' }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-network-wired text-warning"></i>
                            </div>
                            <div>
                                <div class="small text-muted text-uppercase">Last IP Address</div>
                                <div class="fw-semibold">{{ $admin->last_login_ip ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Activity Timeline Card --}}
            <div class="card mt-4">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    Recent Activity
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item d-flex gap-3 mb-4">
                            <div class="timeline-icon">
                                <div class="bg-gradient-success rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-sign-in-alt text-white small"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold small">Last Login</div>
                                <div class="text-muted small">
                                    {{ $admin->last_login_at ? \Carbon\Carbon::parse($admin->last_login_at)->format('d M Y, h:i A') : 'No activity yet' }}
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item d-flex gap-3 mb-4">
                            <div class="timeline-icon">
                                <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-user-plus text-white small"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold small">Account Created</div>
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($admin->created_at)->format('d M Y, h:i A') }}
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item d-flex gap-3">
                            <div class="timeline-icon">
                                <div class="bg-gradient-info rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-shield-alt text-white small"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold small">Security Level</div>
                                <div class="text-muted small">High — 2FA Available</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        {{-- Right Column: Edit Forms --}}
        <div class="col-xl-8">
            
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show glass-panel mb-4" role="alert" style="border-left: 4px solid var(--gl);">
                    <i class="fas fa-check-circle me-2" style="color: var(--gl);"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show glass-panel mb-4" role="alert" style="border-left: 4px solid #dc2626;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error!</strong> Please check the form below.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            {{-- Update Profile Form --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    Edit Profile Information
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1 text-muted"></i>Full Name
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $admin->name) }}" 
                                       required
                                       placeholder="Enter your full name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1 text-muted"></i>Email Address
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $admin->email) }}" 
                                       required
                                       placeholder="admin@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="alert alert-info small mt-4 mb-0" style="background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.2); border-radius: var(--r-pill);">
                            <i class="fas fa-info-circle me-2" style="color: #0ea5e9;"></i>
                            Your email address will be used for login and important notifications.
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- Change Password Form --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    Change Password
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.change-password') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-key me-1 text-muted"></i>Current Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required
                                   placeholder="Enter your current password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-shield-alt me-1 text-muted"></i>New Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password" 
                                       required
                                       placeholder="Enter new password">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle me-1 text-muted"></i>Confirm New Password
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       required
                                       placeholder="Confirm new password">
                            </div>
                        </div>
                        
                        <div class="alert alert-warning small mt-4" style="background: rgba(201, 168, 76, 0.08); border: 1px solid rgba(201, 168, 76, 0.2); border-radius: var(--r-pill);">
                            <i class="fas fa-exclamation-triangle me-2" style="color: #c9a84c;"></i>
                            <strong>Password Requirements:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Minimum 8 characters long</li>
                                <li>Use a mix of uppercase, lowercase, numbers, and symbols</li>
                                <li>Don't use common or easily guessable passwords</li>
                                <li>Avoid using personal information like your name or email</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- Security Tips Card --}}
            <div class="card fade-in-up">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    Security Recommendations
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 glass-panel h-100" style="border-radius: var(--r-md); background: rgba(39, 163, 85, 0.03);">
                                <div class="stat-icon bg-gradient-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 44px; height: 44px; box-shadow: 0 4px 12px rgba(126,213,58,0.3);">
                                    <i class="fas fa-lock text-white" style="font-size: 0.9rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-2" style="font-size: 0.85rem;">Strong Passwords</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.72rem;">Use unique passwords with 8+ characters, mixed cases and symbols.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 glass-panel h-100" style="border-radius: var(--r-md); background: rgba(14, 165, 233, 0.03);">
                                <div class="stat-icon bg-gradient-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 44px; height: 44px; box-shadow: 0 4px 12px rgba(14,165,233,0.3);">
                                    <i class="fas fa-fingerprint text-white" style="font-size: 0.9rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-2" style="font-size: 0.85rem;">Multi-Factor</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.72rem;">Enable 2FA for an extra layer of protection on your account.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 glass-panel h-100" style="border-radius: var(--r-md); background: rgba(245, 158, 11, 0.03);">
                                <div class="stat-icon bg-gradient-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 44px; height: 44px; box-shadow: 0 4px 12px rgba(245,158,11,0.3);">
                                    <i class="fas fa-sign-out-alt text-white" style="font-size: 0.9rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-2" style="font-size: 0.85rem;">Secure Logout</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.72rem;">Always log out when using shared or public computers.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top text-center">
                        <small class="text-muted" style="font-size: 0.75rem;">
                            <i class="fas fa-shield-alt me-1 text-success"></i> 
                            Your account is protected by industry standard encryption.
                        </small>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Timeline styles */
    .timeline-item {
        position: relative;
    }
    
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 32px;
        bottom: -16px;
        width: 2px;
        background: linear-gradient(180deg, var(--gl), transparent);
    }
    
    .avatar-wrapper {
        transition: transform 0.3s ease;
    }
    
    .avatar-wrapper:hover {
        transform: translateY(-5px);
    }
    
    .avatar-ring {
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 0.4;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.05);
        }
    }
    
    /* Custom subtle backgrounds */
    .bg-primary-subtle {
        background: rgba(39, 163, 85, 0.1) !important;
    }
    .bg-info-subtle {
        background: rgba(14, 165, 233, 0.1) !important;
    }
    .bg-warning-subtle {
        background: rgba(201, 168, 76, 0.1) !important;
    }
    .bg-success-subtle {
        background: rgba(126, 213, 58, 0.1) !important;
    }
    
    /* Gradient backgrounds */
    .bg-gradient-success {
        background: linear-gradient(135deg, var(--gl), #5ab020) !important;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--gm), var(--gk)) !important;
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    }
    
    /* Form focus state override */
    .form-control:focus {
        border-color: var(--gl);
        box-shadow: 0 0 0 4px rgba(126, 213, 58, 0.15);
    }
    
    /* Alert styling */
    .alert {
        border-radius: var(--r-pill);
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
    }
    
    .alert-success {
        background: rgba(126, 213, 58, 0.12);
        border: 1px solid rgba(126, 213, 58, 0.3);
        color: #145a2c;
    }
    
    .alert-danger {
        background: rgba(220, 38, 38, 0.08);
        border: 1px solid rgba(220, 38, 38, 0.2);
        color: #991b1b;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add password strength indicator (optional enhancement)
    const passwordInput = document.getElementById('new_password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            updateStrengthIndicator(strength);
        });
    }
    
    function getPasswordStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;
        return score;
    }
    
    function updateStrengthIndicator(strength) {
        // You can implement a visual password strength indicator here
        console.log('Password strength:', strength);
    }
});
</script>
@endpush