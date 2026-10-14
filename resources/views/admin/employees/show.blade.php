{{-- resources/views/admin/employees/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Employee Profile Details')
@section('page-title', 'Employee Profile Details')
@section('page-subtitle', 'Complete profile, experience, education & documents for ' . $employee->name)

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Column - Profile Summary Card --}}
        <div class="col-xl-4">
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 18px; overflow: hidden; background: #ffffff;">
                {{-- Decorative Cover Header --}}
                <div style="height: 90px; background: linear-gradient(135deg, #064e3b 0%, #0d3b1a 50%, #1e7c42 100%); position: relative;">
                    <div style="position: absolute; inset: 0; opacity: 0.12; background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 10px 10px;"></div>
                    <div class="position-absolute top-0 end-0 p-3">
                        <span class="badge" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); color: #ffffff; font-size: 0.68rem; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 20px; padding: 3px 9px;">
                            <i class="fas fa-fingerprint me-1"></i> EMS PROFILE
                        </span>
                    </div>
                </div>

                <div class="card-body text-center px-4 pb-4 pt-0" style="position: relative;">
                    {{-- Elevated Avatar --}}
                    <div class="position-relative d-inline-block" style="margin-top: -50px; margin-bottom: 12px;">
                        @if($employee->profile_photo)
                            <img src="{{ Storage::url($employee->profile_photo) }}" 
                                 class="rounded-circle" 
                                 style="width: 104px; height: 104px; object-fit: cover; border: 4px solid #ffffff; box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.18);">
                        @else
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" 
                                 style="width: 104px; height: 104px; font-size: 2.2rem; background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); border: 4px solid #ffffff; letter-spacing: 1px; box-shadow: 0 8px 20px -4px rgba(13, 59, 26, 0.32);">
                                {{ strtoupper(substr($employee->name, 0, 2)) }}
                            </div>
                        @endif
                        
                        {{-- Active Status Dot --}}
                        <span class="position-absolute bottom-0 end-0 rounded-circle" 
                              style="width: 18px; height: 18px; background: #10b981; border: 3px solid #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.15);" 
                              title="Status: {{ ucfirst($employee->employment_status ?? 'Active') }}">
                        </span>
                    </div>
                    
                    {{-- Name & Monospace Code Badge --}}
                    <h4 class="mb-1 fw-bold text-dark" style="letter-spacing: -0.02em;">{{ $employee->name }}</h4>
                    <div class="d-inline-flex align-items-center gap-1.5 mb-2.5 px-2.5 py-1" 
                         style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.78rem;">
                        <i class="fas fa-id-badge text-muted"></i>
                        <span class="font-monospace fw-bold text-dark">{{ $employee->employee_code }}</span>
                    </div>
                    
                    {{-- Role & Department Badges --}}
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-1.5 mb-3">
                        <span class="badge fw-semibold" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.75rem; padding: 5px 11px; border-radius: 8px;">
                            <i class="fas fa-code me-1 opacity-75"></i> {{ $employee->designation ?? 'Team Member' }}
                        </span>
                        <span class="badge fw-semibold" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-size: 0.75rem; padding: 5px 11px; border-radius: 8px;">
                            <i class="fas fa-building me-1 opacity-75"></i> {{ $employee->department ?? 'General' }}
                        </span>
                        @if($employee->is_team_leader || $employee->role === 'team_leader')
                            <span class="badge fw-bold" style="background: linear-gradient(135deg, #0d3b1a 0%, #166534 100%); color: #fef08a; border: 1px solid #22c55e; font-size: 0.75rem; padding: 5px 12px; border-radius: 8px; box-shadow: 0 2px 6px rgba(13, 59, 26, 0.15);">
                                <i class="fas fa-crown text-warning me-1"></i> Team Leader (TL)
                            </span>
                        @elseif($employee->role === 'sales')
                            <span class="badge fw-semibold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.75rem; padding: 5px 11px; border-radius: 8px;">
                                <i class="fas fa-funnel-dollar me-1 opacity-75"></i> Sales Team
                            </span>
                        @endif
                    </div>

                    {{-- Role Privileges Controls (TL & Admin) --}}
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <form action="{{ route('admin.employees.toggle-tl', $employee) }}" method="POST" class="m-0">
                            @csrf
                            @method('PATCH')
                            @if($employee->is_team_leader)
                                <button type="submit" class="btn btn-sm fw-bold d-inline-flex align-items-center gap-1.5" 
                                        style="background: #f0fdf4; color: #15803d; border: 1px solid #86efac; border-radius: 10px; font-size: 0.76rem; padding: 6px 13px;"
                                        title="Click to remove Team Leader privilege">
                                    <i class="fas fa-crown text-warning"></i>
                                    <span>TL Enabled</span>
                                    <i class="fas fa-check text-success ms-0.5" style="font-size: 0.68rem;"></i>
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm btn-light border fw-semibold d-inline-flex align-items-center gap-1.5" 
                                        style="color: #64748b; border-radius: 10px; font-size: 0.76rem; padding: 6px 13px; background: #ffffff;"
                                        title="Click to grant Team Leader role">
                                    <i class="fas fa-crown text-muted"></i>
                                    <span>Assign TL Role</span>
                                </button>
                            @endif
                        </form>

                        <form action="{{ route('admin.employees.toggle-admin', $employee) }}" method="POST" class="m-0">
                            @csrf
                            @method('PATCH')
                            @if($employee->is_admin || $employee->role === 'admin')
                                <button type="submit" class="btn btn-sm fw-bold text-white d-inline-flex align-items-center gap-1.5" 
                                        style="background: #0f172a; border: 1px solid #0f172a; border-radius: 10px; font-size: 0.76rem; padding: 6px 13px;"
                                        title="Click to revoke Admin access">
                                    <i class="fas fa-shield-alt text-warning"></i>
                                    <span>Admin Access</span>
                                    <i class="fas fa-check text-success ms-0.5" style="font-size: 0.68rem;"></i>
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm btn-light border fw-semibold d-inline-flex align-items-center gap-1.5" 
                                        style="color: #64748b; border-radius: 10px; font-size: 0.76rem; padding: 6px 13px; background: #ffffff;"
                                        title="Click to promote to Admin">
                                    <i class="fas fa-user-shield text-muted"></i>
                                    <span>Make Admin</span>
                                </button>
                            @endif
                        </form>
                    </div>

                    {{-- Refined Lifecycle Employment Status Widget --}}
                    @php
                        $statusConfig = [
                            'active' => ['bg' => '#ecfdf5', 'text' => '#065f46', 'border' => '#a7f3d0', 'dot' => '#10b981', 'glow' => '#a7f3d0', 'label' => 'Active Employee'],
                            'probation' => ['bg' => '#fffbeb', 'text' => '#92400e', 'border' => '#fde68a', 'dot' => '#f59e0b', 'glow' => '#fef3c7', 'label' => 'Probation Period'],
                            'notice_period' => ['bg' => '#fff7ed', 'text' => '#9a3412', 'border' => '#fed7aa', 'dot' => '#ea580c', 'glow' => '#ffedd5', 'label' => 'On Notice Period'],
                            'resigned' => ['bg' => '#fef2f2', 'text' => '#991b1b', 'border' => '#fecaca', 'dot' => '#ef4444', 'glow' => '#fee2e2', 'label' => 'Resigned'],
                            'terminated' => ['bg' => '#f8fafc', 'text' => '#334155', 'border' => '#cbd5e1', 'dot' => '#64748b', 'glow' => '#e2e8f0', 'label' => 'Terminated'],
                        ];
                        $curLifeStatus = strtolower($employee->employment_status ?? 'active');
                        $st = $statusConfig[$curLifeStatus] ?? $statusConfig['active'];
                    @endphp
                    <div class="p-3 mb-3 text-start" 
                         style="background: {{ $st['bg'] }}; border: 1px solid {{ $st['border'] }}; border-radius: 14px;">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.6px; color: {{ $st['text'] }}; opacity: 0.85;">
                                Lifecycle Status
                            </span>
                            <button type="button" class="btn btn-sm p-0 fw-bold text-decoration-none d-inline-flex align-items-center gap-1" 
                                    style="font-size: 0.74rem; color: {{ $st['text'] }};" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateLifecycleModal">
                                <i class="fas fa-sync-alt" style="font-size: 0.68rem;"></i> Change
                            </button>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle d-inline-block" 
                                      style="width: 10px; height: 10px; background: {{ $st['dot'] }}; box-shadow: 0 0 0 3px {{ $st['glow'] }};"></span>
                                <span class="fw-bold" style="font-size: 0.88rem; color: {{ $st['text'] }};">
                                    {{ $st['label'] }}
                                </span>
                            </div>
                            <span class="badge font-monospace" style="background: rgba(255, 255, 255, 0.85); color: {{ $st['text'] }}; border: 1px solid {{ $st['border'] }}; font-size: 0.68rem; padding: 3px 6px; border-radius: 6px;">
                                {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->diffForHumans(null, true) : 'Current' }} tenure
                            </span>
                        </div>
                    </div>
                    
                    {{-- Contact Details Info Tiles --}}
                    <div class="text-start space-y-2 pt-2 border-top">
                        <div class="d-flex align-items-center gap-3 py-1.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 36px; height: 36px; background: #eff6ff; color: #2563eb; flex-shrink: 0; border-radius: 10px;">
                                <i class="fas fa-envelope" style="font-size: 0.85rem;"></i>
                            </div>
                            <div class="text-truncate">
                                <div class="text-muted" style="font-size: 0.66rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Official Email</div>
                                <a href="mailto:{{ $employee->email }}" class="fw-semibold text-dark text-decoration-none text-truncate d-block" style="font-size: 0.82rem;">
                                    {{ $employee->email }}
                                </a>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 py-1.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 36px; height: 36px; background: #f0fdf4; color: #16a34a; flex-shrink: 0; border-radius: 10px;">
                                <i class="fas fa-phone-alt" style="font-size: 0.85rem;"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 0.66rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Phone</div>
                                @if($employee->phone)
                                    <a href="tel:{{ $employee->phone }}" class="fw-semibold text-dark text-decoration-none" style="font-size: 0.82rem;">
                                        {{ $employee->phone }}
                                    </a>
                                @else
                                    <span class="text-muted small">Not provided</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 py-1.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 36px; height: 36px; background: #fdf2f8; color: #db2777; flex-shrink: 0; border-radius: 10px;">
                                <i class="fas fa-calendar-alt" style="font-size: 0.85rem;"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 0.66rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Joining Date</div>
                                <div class="fw-semibold text-dark" style="font-size: 0.82rem;">
                                    {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Footer Action Buttons --}}
                    <div class="mt-3 pt-3 border-top d-flex gap-2 justify-content-center">
                        <a href="{{ route('admin.employees.edit', $employee) }}" 
                           class="btn btn-sm text-white fw-bold w-100 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5"
                           style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); border-radius: 10px; padding: 8px 14px;">
                            <i class="fas fa-edit"></i>
                            <span>Edit Full Profile</span>
                        </a>
                        <a href="{{ route('admin.employees.index') }}" 
                           class="btn btn-sm btn-light border fw-semibold d-inline-flex align-items-center justify-content-center gap-1"
                           style="border-radius: 10px; padding: 8px 14px; background: #ffffff; color: #475569;"
                           title="Return to Directory">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Work Shift Allocation Card --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.92rem;">
                        <i class="fas fa-business-time text-primary"></i> Work Shift & Roster
                    </span>
                    <button type="button" class="btn btn-sm btn-light border fw-semibold d-inline-flex align-items-center gap-1" 
                            style="border-radius: 8px; font-size: 0.74rem; padding: 4px 10px;"
                            data-bs-toggle="modal" data-bs-target="#updateShiftModal">
                        <i class="fas fa-clock text-primary"></i> Allocate Shift
                    </button>
                </div>
                <div class="card-body pt-0">
                    <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.66rem; letter-spacing: 0.5px;">Assigned Shift</div>
                    @if($employee->workShift)
                        <div class="p-3 border rounded-3 bg-light mt-1" style="border-color: #e2e8f0 !important;">
                            <div class="fw-bold text-dark fs-6">{{ $employee->workShift->name }}</div>
                            <div class="text-primary fw-semibold mt-1 small">
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($employee->workShift->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($employee->workShift->end_time)->format('g:i A') }}
                            </div>
                            <div class="text-muted small mt-1" style="font-size: 0.76rem;">
                                Grace Period: <strong>{{ $employee->workShift->grace_period_minutes }} mins</strong> &bull; Break Duration: <strong>{{ $employee->workShift->break_duration_minutes }} mins</strong>
                            </div>
                        </div>
                    @else
                        <div class="p-3 border rounded-3 bg-light mt-1" style="border-color: #e2e8f0 !important;">
                            <div class="fw-bold text-dark small">General Shift (Default: 9 AM - 6 PM)</div>
                            <div class="text-muted small mt-1" style="font-size: 0.76rem;">No custom shift allocated. Using company default roster.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Reporting Manager & Org Card --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.92rem;">
                        <i class="fas fa-sitemap text-success"></i> Reporting Line & Org
                    </span>
                    <button type="button" class="btn btn-sm btn-light border fw-semibold d-inline-flex align-items-center gap-1" 
                            style="border-radius: 8px; font-size: 0.74rem; padding: 4px 10px;"
                            data-bs-toggle="modal" data-bs-target="#updateManagerModal">
                        <i class="fas fa-user-tie text-success"></i> Change Manager
                    </button>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.66rem; letter-spacing: 0.5px;">Reporting Manager</div>
                        @if($employee->reportingManager)
                            <div class="p-2.5 border rounded-3 bg-light d-flex align-items-center justify-content-between mt-1" style="border-color: #e2e8f0 !important;">
                                <div>
                                    <div class="fw-bold text-dark small">{{ $employee->reportingManager->name }}</div>
                                    <small class="text-muted" style="font-size: 0.74rem;">{{ $employee->reportingManager->designation }} &bull; {{ $employee->reportingManager->email }}</small>
                                </div>
                                <a href="{{ route('admin.employees.show', $employee->reportingManager) }}" class="btn btn-xs btn-outline-success fw-bold" style="border-radius: 6px;">View</a>
                            </div>
                        @else
                            <div class="text-muted small py-1" style="font-size: 0.78rem;">No direct reporting manager assigned.</div>
                        @endif
                    </div>

                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.66rem; letter-spacing: 0.5px;">Direct Reports ({{ $employee->directReports->count() }})</div>
                        @forelse($employee->directReports as $report)
                            <div class="p-2 border rounded-3 mb-1.5 d-flex align-items-center justify-content-between bg-white" style="border-color: #e2e8f0 !important;">
                                <div>
                                    <div class="fw-semibold text-dark small">{{ $report->name }}</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">{{ $report->designation ?? 'Team Member' }}</small>
                                </div>
                                <a href="{{ route('admin.employees.show', $report) }}" class="btn btn-xs btn-light border" style="border-radius: 6px;">View</a>
                            </div>
                        @empty
                            <div class="text-muted small" style="font-size: 0.78rem;">No direct team reports.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Right Column - Detailed Profile Tabs & Cards --}}
        <div class="col-xl-8">
            @php
                $experiencesList = $employee->relationLoaded('experiences') ? $employee->experiences : collect([]);
                $educationsList = $employee->relationLoaded('educations') ? $employee->educations : collect([]);
                $documentsList = $employee->relationLoaded('documents') ? $employee->documents : collect([]);
            @endphp
            
            {{-- Multi Work Experience Entries --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-history text-warning me-2"></i> Work Experience History ({{ $experiencesList->count() }})</span>
                    <div>
                        <span class="badge bg-{{ $employee->is_fresher ? 'info' : 'success' }} me-2 fs-6">
                            {{ $employee->is_fresher ? 'Fresher' : $employee->formatted_experience }}
                        </span>
                        <button type="button" class="btn btn-xs btn-warning" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                            <i class="fas fa-plus me-1"></i> Add Work Experience
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Total Calculated Experience (Past Jobs + Current Company Tenure): <strong>{{ $employee->formatted_experience }}</strong></small>
                    </div>

                    @forelse($experiencesList as $exp)
                        <div class="p-3 border rounded mb-2 bg-light d-flex align-items-start justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $exp->company_name }}</h6>
                                <div class="text-primary fw-semibold small mb-1">{{ $exp->designation }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ optional($exp->start_date)->format('M Y') ?? 'N/A' }} - 
                                    {{ $exp->is_current_job ? 'Present' : (optional($exp->end_date)->format('M Y') ?? 'N/A') }}
                                </div>
                                @if($exp->job_description)
                                    <div class="mt-2 text-muted small">{{ $exp->job_description }}</div>
                                @endif
                            </div>
                            <form action="{{ route('admin.employees.experiences.delete', $exp->id) }}" method="POST" onsubmit="return confirm('Delete this experience entry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted">
                            No work experience history entries added yet. Click "+ Add Work Experience" to add previous jobs.
                        </div>
                    @endforelse

                    @if($employee->internship_details)
                        <div class="mt-3 pt-3 border-top">
                            <label class="text-muted small text-uppercase fw-bold">Internship Summary</label>
                            <p class="fw-semibold mb-0">{{ $employee->internship_details }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Multi Educational Qualifications --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-graduation-cap text-success me-2"></i> Educational Qualifications ({{ $educationsList->count() }})</span>
                    <button type="button" class="btn btn-xs btn-success" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                        <i class="fas fa-plus me-1"></i> Add Education Entry
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Level</th>
                                    <th>Degree / Qualification</th>
                                    <th>Institution / University</th>
                                    <th>Year</th>
                                    <th>Score</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($educationsList as $edu)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $edu->level }}</span></td>
                                        <td class="fw-bold">{{ $edu->degree_title }}</td>
                                        <td>{{ $edu->institution_name }}</td>
                                        <td>{{ $edu->passout_year ?? 'N/A' }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $edu->score_or_grade ?? 'N/A' }}</span></td>
                                        <td>
                                            <form action="{{ route('admin.employees.educations.delete', $edu->id) }}" method="POST" onsubmit="return confirm('Delete this education entry?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No educational qualifications added yet. Click "+ Add Education Entry".</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Extended Personal & Contact Information --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-user-circle text-info me-2"></i> Personal & Government ID Info
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Personal Email</label>
                            <p class="fw-semibold mb-0">{{ $employee->personal_email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Gender & Marital Status</label>
                            <p class="fw-semibold mb-0">
                                {{ strtoupper($employee->gender ?? 'N/A') }} • {{ strtoupper($employee->marital_status ?? 'N/A') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">PAN Number</label>
                            <p class="fw-semibold mb-0">{{ $employee->pan_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Aadhaar Number</label>
                            <p class="fw-semibold mb-0">{{ $employee->aadhaar_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">UAN & PF Number</label>
                            <p class="fw-semibold mb-0">UAN: {{ $employee->uan_number ?? 'N/A' }} • PF: {{ $employee->pf_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Current Address</label>
                            <p class="fw-semibold mb-0">{{ $employee->address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small text-uppercase fw-bold">Permanent Address</label>
                            <p class="fw-semibold mb-0">{{ $employee->permanent_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Document Management Section --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-folder-open text-primary me-2"></i> Employee Document Vault</span>
                    <button type="button" class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                        <i class="fas fa-upload me-1"></i> Upload Document
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Document Title</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentsList as $doc)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $doc->title }}</div>
                                            <small class="text-muted">{{ $doc->file_name }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ strtoupper($doc->document_type) }}</span>
                                        </td>
                                        <td>
                                            @if($doc->status === 'verified')
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Verified</span>
                                            @elseif($doc->status === 'rejected')
                                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending Verification</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-xs btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                @if($doc->status !== 'verified')
                                                    <form action="{{ route('admin.documents.verify', $doc) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-check"></i> Approve</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No documents uploaded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL: Add Experience Entry --}}
<div class="modal fade" id="addExperienceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.employees.experiences.add', $employee) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Work Experience Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Company / Organization Name</label>
                    <input type="text" name="company_name" class="form-control" placeholder="e.g. TCS / Infosys" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Designation / Role</label>
                    <input type="text" name="designation" class="form-control" placeholder="e.g. Software Engineer" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold">Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_current_job" value="1" class="form-check-input" id="adminIsCurrentJob">
                    <label class="form-check-label fw-bold text-primary" for="adminIsCurrentJob">Currently Working Here (Current Job / Employer)</label>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Job Responsibilities / Description</label>
                    <textarea name="job_description" class="form-control" rows="3" placeholder="Key responsibilities and achievements..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Save Work Experience</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Add Education Entry --}}
<div class="modal fade" id="addEducationModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.employees.educations.add', $employee) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Educational Qualification Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Education Level</label>
                    <select name="level" class="form-select" required>
                        <option value="Undergraduate">Undergraduate (Bachelor's)</option>
                        <option value="Postgraduate">Postgraduate (Master's)</option>
                        <option value="Diploma">Diploma</option>
                        <option value="12th">12th / Higher Secondary</option>
                        <option value="10th">10th / SSLC</option>
                        <option value="Doctorate">Doctorate / PhD</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Degree / Course Title</label>
                    <input type="text" name="degree_title" class="form-control" placeholder="e.g. B.Tech Computer Science" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Institution / College / School Name</label>
                    <input type="text" name="institution_name" class="form-control" placeholder="e.g. Anna University" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold">Passout Year</label>
                        <input type="text" name="passout_year" class="form-control" placeholder="e.g. 2024">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Score / CGPA</label>
                        <input type="text" name="score_or_grade" class="form-control" placeholder="e.g. 8.5 CGPA / 85%">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save Education Entry</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Change Lifecycle Status --}}
<div class="modal fade" id="updateLifecycleModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.employees.lifecycle-status', $employee) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Update Employment Lifecycle Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">New Status</label>
                    <select name="employment_status" class="form-select" required>
                        <option value="active" {{ $employee->employment_status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="probation" {{ $employee->employment_status === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="notice_period" {{ $employee->employment_status === 'notice_period' ? 'selected' : '' }}>Notice Period</option>
                        <option value="resigned" {{ $employee->employment_status === 'resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="terminated" {{ $employee->employment_status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Effective Date</label>
                    <input type="date" name="status_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Remarks / Termination Reason</label>
                    <textarea name="remarks" class="form-control" rows="3" placeholder="Enter details or reason for status update..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Upload Document --}}
<div class="modal fade" id="uploadDocModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.employees.documents.upload', $employee) }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Upload Employee Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Document Type</label>
                    <select name="document_type" class="form-select" required>
                        <option value="aadhaar_card">Aadhaar Card ID Proof</option>
                        <option value="pan_card">PAN Card ID Proof</option>
                        <option value="bank_passbook">Bank Passbook / Bankbook</option>
                        <option value="10th_certificate">10th / SSLC Marksheet & Certificate</option>
                        <option value="12th_certificate">12th / HSC Marksheet & Certificate</option>
                        <option value="degree_certificate">Graduate / Degree Certificate</option>
                        <option value="professional_certificate">Professional Certification</option>
                        <option value="experience_certificate">Work Experience / Relieving Certificate</option>
                        <option value="contract">Employment Contract / Offer Letter</option>
                        <option value="resume">Resume / CV</option>
                        <option value="other">Other Document</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Document Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. B.Tech Degree Certificate" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Document / License Number (Optional)</label>
                    <input type="text" name="document_number" class="form-control" placeholder="e.g. AADHAAR-1234-5678">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Select File (PDF / Image)</label>
                    <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Upload & Verify</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Update Reporting Manager --}}
<div class="modal fade" id="updateManagerModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.employees.update-manager', $employee) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-sitemap text-primary me-2"></i> Assign Reporting Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Select an employee from the organization dropdown list to assign as {{ $employee->name }}'s direct reporting manager.</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Reporting Manager</label>
                    <select name="reporting_manager_id" class="form-select">
                        <option value="">No Reporting Manager (Top Head / Unassigned)</option>
                        @foreach(\App\Models\Employee::where('id', '!=', $employee->id)->orderBy('name')->get() as $mgr)
                            <option value="{{ $mgr->id }}" {{ $employee->reporting_manager_id == $mgr->id ? 'selected' : '' }}>
                                {{ $mgr->name }} ({{ $mgr->designation ?? 'Employee' }} - {{ $mgr->department ?? 'General' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fw-bold">Update Reporting Manager</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Update Work Shift --}}
<div class="modal fade" id="updateShiftModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-clock text-primary me-2"></i> Allocate Work Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.employees.update-shift', $employee) }}" method="POST" class="mb-4">
                    @csrf
                    <p class="text-muted small">Select an existing shift roster to allocate to {{ $employee->name }}.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Work Shift</label>
                        <select name="shift_id" class="form-select">
                            <option value="">Default General Shift (09:00 AM - 06:00 PM)</option>
                            @foreach(\App\Models\WorkShift::all() as $sh)
                                <option value="{{ $sh->id }}" {{ $employee->shift_id == $sh->id ? 'selected' : '' }}>
                                    {{ $sh->name }} ({{ \Carbon\Carbon::parse($sh->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($sh->end_time)->format('g:i A') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary fw-bold">Allocate Selected Shift</button>
                </form>

                {{-- COLLAPSIBLE / FORM: Add New Custom Shift with Timepickers --}}
                <div class="bg-light p-3 rounded border">
                    <h6 class="fw-bold text-success mb-3"><i class="fas fa-plus-circle me-1"></i> Create &amp; Allocate New Custom Shift (with Timepickers)</h6>
                    <form action="{{ route('admin.work-shifts.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Shift Name / Title</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Afternoon Shift / Split Shift" required>
                        </div>

                        {{-- Shift Start / End --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small"><i class="fas fa-stopwatch text-success me-1"></i> Shift Start Time (12-Hr AM/PM)</label>
                                <div class="input-group">
                                    <select name="start_time_hour" class="form-select">
                                        @for($h=1; $h<=12; $h++)
                                            <option value="{{ sprintf('%02d', $h) }}" {{ $h == 9 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                                        @endfor
                                    </select>
                                    <span class="input-group-text">:</span>
                                    <select name="start_time_min" class="form-select">
                                        @for($m=0; $m<60; $m+=5)
                                            <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                                        @endfor
                                    </select>
                                    <select name="start_time_ampm" class="form-select fw-bold text-primary">
                                        <option value="AM" selected>AM</option>
                                        <option value="PM">PM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small"><i class="fas fa-stopwatch text-danger me-1"></i> Shift End Time (12-Hr AM/PM)</label>
                                <div class="input-group">
                                    <select name="end_time_hour" class="form-select">
                                        @for($h=1; $h<=12; $h++)
                                            <option value="{{ sprintf('%02d', $h) }}" {{ $h == 6 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                                        @endfor
                                    </select>
                                    <span class="input-group-text">:</span>
                                    <select name="end_time_min" class="form-select">
                                        @for($m=0; $m<60; $m+=5)
                                            <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                                        @endfor
                                    </select>
                                    <select name="end_time_ampm" class="form-select fw-bold text-danger">
                                        <option value="AM">AM</option>
                                        <option value="PM" selected>PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Grace Period & Break Duration --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Grace Period (Minutes)</label>
                                <input type="number" name="grace_period_minutes" class="form-control" value="15" min="0" max="120">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Break Duration (Minutes)</label>
                                <input type="number" name="break_duration_minutes" class="form-control" value="60" min="0" max="240">
                            </div>
                        </div>

                        {{-- Breaks 1–6 --}}
                        @php
                            $breakLabels = ['Lunch Break', 'Break 2', 'Break 3', 'Break 4', 'Break 5', 'Break 6'];
                            $breakColors = ['primary','warning','info','secondary','success','danger'];
                        @endphp
                        @foreach(range(1,6) as $n)
                        @php $lbl = $breakLabels[$n-1]; $clr = $breakColors[$n-1]; @endphp
                        <div class="card border-{{ $clr }} mb-2">
                            <div class="card-header py-1 bg-{{ $clr }} bg-opacity-10 d-flex align-items-center gap-2">
                                <div class="form-check mb-0">
                                    <input class="form-check-input break-toggle-cb" type="checkbox"
                                           name="break{{ $n }}_enabled" value="1"
                                           id="quickBreak{{ $n }}Enable"
                                           data-target="#quickBreak{{ $n }}Times"
                                           {{ $n == 2 ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-{{ $clr }}" for="quickBreak{{ $n }}Enable">
                                        {{ $lbl }}
                                    </label>
                                </div>
                            </div>
                            <div id="quickBreak{{ $n }}Times" class="card-body py-2 {{ $n == 2 ? '' : 'd-none' }}">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-success">Begin Time</label>
                                        <div class="input-group input-group-sm">
                                            <select name="break{{ $n }}_begin_hour" class="form-select form-select-sm">
                                                @for($h=1;$h<=12;$h++)
                                                    <option value="{{ sprintf('%02d',$h) }}"
                                                        {{ ($n==2 && $h==1) ? 'selected' : '' }}>
                                                        {{ sprintf('%02d',$h) }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <span class="input-group-text">:</span>
                                            <select name="break{{ $n }}_begin_min" class="form-select form-select-sm">
                                                @for($m=0;$m<60;$m+=5)
                                                    <option value="{{ sprintf('%02d',$m) }}">{{ sprintf('%02d',$m) }}</option>
                                                @endfor
                                            </select>
                                            <select name="break{{ $n }}_begin_ampm" class="form-select form-select-sm fw-bold text-primary">
                                                <option value="AM">AM</option>
                                                <option value="PM" {{ $n==2 ? 'selected':'' }}>PM</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-danger">End Time</label>
                                        <div class="input-group input-group-sm">
                                            <select name="break{{ $n }}_end_hour" class="form-select form-select-sm">
                                                @for($h=1;$h<=12;$h++)
                                                    <option value="{{ sprintf('%02d',$h) }}"
                                                        {{ ($n==2 && $h==2) ? 'selected' : '' }}>
                                                        {{ sprintf('%02d',$h) }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <span class="input-group-text">:</span>
                                            <select name="break{{ $n }}_end_min" class="form-select form-select-sm">
                                                @for($m=0;$m<60;$m+=5)
                                                    <option value="{{ sprintf('%02d',$m) }}">{{ sprintf('%02d',$m) }}</option>
                                                @endfor
                                            </select>
                                            <select name="break{{ $n }}_end_ampm" class="form-select form-select-sm fw-bold text-danger">
                                                <option value="AM">AM</option>
                                                <option value="PM" {{ $n==2 ? 'selected':'' }}>PM</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <button type="submit" class="btn btn-success fw-bold w-100 mt-2">
                            <i class="fas fa-check-circle me-1"></i> Save &amp; Allocate New Custom Shift
                        </button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
// Break enable/disable toggle inside Allocate Shift modal
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.break-toggle-cb').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var target = document.querySelector(this.dataset.target);
            if (target) {
                target.classList.toggle('d-none', !this.checked);
            }
        });
    });
});
</script>
@endpush
@endsection