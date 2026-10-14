{{-- resources/views/admin/leaves/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Leave Details')

@section('content')
<div class="container-fluid px-0">
    
    <!-- Hero Section with Stats -->
    <div class="page-hero-strip mb-4 fade-in-up">
        <div class="strip-rule"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Leave Application Details</h2>
                <p class="text-muted small mb-0">RID: #LEV-{{ str_pad($leave->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="d-flex gap-2">
                @if($leave->status === 'pending')
                    <form action="{{ route('admin.leaves.approve', $leave) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Approve this leave?')">
                            <i class="fas fa-check-circle me-2"></i>Approve Request
                        </button>
                    </form>
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModalContent">
                        <i class="fas fa-times-circle me-2"></i>Reject Request
                    </button>
                @endif
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Requester Profile Hero -->
    <div class="card mb-4 border-0 shadow-sm fade-in-up" style="background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); border-radius: 16px;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="user-avatar border border-4 border-white shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem; background: var(--gl);">
                        {{ strtoupper(substr($leave->employee?->name ?? '??', 0, 2)) }}
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="fw-bold text-dark mb-1">{{ $leave->employee?->name ?? 'Unknown Requester' }}</h3>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                    <i class="fas fa-briefcase me-1 small"></i> {{ $leave->employee?->designation ?? 'Designation N/A' }}
                                </span>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold">
                                    <i class="fas fa-building me-1 small"></i> {{ $leave->employee?->department ?? 'Department N/A' }}
                                </span>
                                <span class="text-muted small">
                                    <i class="fas fa-id-card me-1 ms-2"></i> ID: #{{ $leave->employee?->employee_code ?? 'CODE-N/A' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($leave->employee_id)
                                <a href="{{ route('admin.employees.show', ['employee' => $leave->employee_id]) }}" class="btn btn-outline-primary btn-sm rounded-pill shadow-sm px-4">
                                    <i class="fas fa-user-circle me-1 small"></i> View Profile
                                </a>
                            @else
                                <span class="badge bg-light text-muted px-3 py-2 rounded-pill">
                                    <i class="fas fa-exclamation-triangle me-1 small"></i> Profile Unavailable
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info Hero Cards -->
    <div class="row g-4 mb-4 stagger-children">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stats-info">
                    <p>Leave Duration</p>
                    <h3 style="font-size: 1.1rem;">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card lime-card">
                <div class="stats-icon"><i class="fas fa-clock"></i></div>
                <div class="stats-info">
                    <p>Total Applied Days</p>
                    <h3>{{ $leave->total_days }} <small class="fs-6">days</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            @php
                $statusMap = [
                    'pending'   => ['icon' => 'fa-hourglass-half', 'grade' => 'linear-gradient(135deg, #f1c40f 0%, #f39c12 100%)'],
                    'approved'  => ['icon' => 'fa-check-circle',   'grade' => 'linear-gradient(135deg, #2ecc71 0%, #27ae60 100%)'],
                    'rejected'  => ['icon' => 'fa-times-circle',   'grade' => 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)'],
                    'cancelled' => ['icon' => 'fa-ban',            'grade' => 'linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%)'],
                ];
                $s = $statusMap[$leave->status] ?? $statusMap['pending'];
            @endphp
            <div class="stats-card" style="background: {{ $s['grade'] }};">
                <div class="stats-icon"><i class="fas {{ $s['icon'] }}"></i></div>
                <div class="stats-info">
                    <p>Request Status</p>
                    <h3>{{ ucfirst($leave->status) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card teal-card">
                <div class="stats-icon">
                    @php
                        $iconMap = [
                            'sick'      => 'fa-virus',
                            'casual'    => 'fa-coffee',
                            'annual'    => 'fa-umbrella-beach',
                            'unpaid'    => 'fa-hand-holding-usd',
                            'maternity' => 'fa-baby',
                            'paternity' => 'fa-child',
                        ];
                        $icon = $iconMap[$leave->leave_type] ?? 'fa-tag';
                    @endphp
                    <i class="fas {{ $icon }}"></i>
                </div>
                <div class="stats-info">
                    <p>Leave Category</p>
                    <h3>{{ ucfirst($leave->leave_type) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Application Details -->
        <div class="col-lg-8">
            <div class="card mb-4 fade-in-up" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-file-invoice"></i></div>
                    Detailed Application Info
                </div>
                <div class="card-body">
                    <!-- Requester Details Grid (Consolidated) -->
                    <div class="row g-4 mb-4 pb-4 border-bottom">
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Department</label>
                            <div class="fw-bold text-dark d-flex align-items-center">
                                <div class="p-2 rounded bg-success bg-opacity-10 text-success me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-building small"></i>
                                </div>
                                {{ $leave->employee?->department ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Designation</label>
                            <div class="fw-bold text-dark d-flex align-items-center">
                                <div class="p-2 rounded bg-primary bg-opacity-10 text-primary me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-briefcase small"></i>
                                </div>
                                {{ $leave->employee?->designation ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Phone Number</label>
                            <div class="fw-bold text-dark d-flex align-items-center">
                                <div class="p-2 rounded bg-info bg-opacity-10 text-info me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-phone-alt small"></i>
                                </div>
                                {{ $leave->employee?->phone ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Email Address</label>
                            <div class="fw-bold text-dark d-flex align-items-center text-truncate" title="{{ $leave->employee?->email ?? 'N/A' }}">
                                <div class="p-2 rounded bg-warning bg-opacity-10 text-warning me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-envelope small"></i>
                                </div>
                                {{ $leave->employee?->email ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Reason for Absence</label>
                            <div class="p-4 rounded-3 border bg-light">
                                <h6 class="mb-0 lh-base text-dark" style="font-weight: 500;">{{ $leave->reason }}</h6>
                            </div>
                        </div>
                        
                        @if($leave->attachment)
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Supporting Evidence</label>
                            <div class="d-flex align-items-center p-3 rounded-3 border-dashed" style="border: 1px dashed #7ed53a; background: rgba(126, 213, 58, 0.05);">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="fas fa-file-pdf text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Attached Document</h6>
                                    <small class="text-muted">Uploaded on {{ $leave->created_at->format('d M Y') }}</small>
                                </div>
                                <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary px-3 shadow-none">
                                    <i class="fas fa-eye me-1"></i>View File
                                </a>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-6 text-center">
                            <div class="p-4 rounded-3 border-dashed bg-light h-100 d-flex flex-column justify-content-center" style="border: 1px dashed rgba(0,0,0,0.1);">
                                <label class="text-muted small text-uppercase fw-bold mb-2">Request Submitted On</label>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-paper-plane text-primary opacity-50"></i>
                                    <span class="fw-bold">{{ $leave->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 text-center">
                            @if($leave->status === 'approved' && $leave->approved_at)
                            <div class="p-4 rounded-3 border border-success border-opacity-25 bg-success bg-opacity-5 h-100 d-flex flex-column justify-content-center">
                                <label class="text-success small text-uppercase fw-bold mb-2">Decision Finalized On</label>
                                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                    <i class="fas fa-calendar-check text-success opacity-50"></i>
                                    <span class="fw-bold text-success">{{ \Carbon\Carbon::parse($leave->approved_at)->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-2 pt-2 border-top border-success border-opacity-10">
                                    <div class="user-avatar" style="width: 24px; height: 24px; font-size: 0.7rem; background: #7ed53a;">
                                        {{ $leave->approvedBy ? strtoupper(substr($leave->approvedBy->name, 0, 2)) : 'AD' }}
                                    </div>
                                    <small class="text-muted">Approved by <strong class="text-success">{{ $leave->approvedBy?->name ?? 'Administrator' }}</strong></small>
                                </div>
                            </div>
                            @elseif($leave->status === 'pending')
                            <div class="p-4 rounded-3 border border-warning border-opacity-25 bg-warning bg-opacity-5 h-100 d-flex flex-column justify-content-center">
                                <label class="text-warning small text-uppercase fw-bold mb-1">Awaiting Decision</label>
                                <p class="mb-0 small text-muted italic">Processing... Waiting for Admin action</p>
                            </div>
                            @else
                             <div class="p-4 rounded-3 border bg-light h-100 d-flex flex-column justify-content-center opacity-75">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Status: {{ ucfirst($leave->status) }}</label>
                                <p class="mb-0 small text-muted">Archived request</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($leave->status === 'rejected')
                    <div class="mt-4 p-4 rounded-3 bg-danger bg-opacity-5 border border-danger border-opacity-10">
                        <h6 class="text-danger fw-bold mb-2"><i class="fas fa-exclamation-circle me-2"></i>Rejection Reason</h6>
                        <p class="mb-0 text-dark opacity-75 small fw-semibold">{{ $leave->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Impact Assessment -->
            <div class="card mb-4 fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-sync-alt"></i></div>
                    Impact on Policy Category
                </div>
                <div class="card-body">
                    @if($leaveBalance)
                    <div class="row text-center g-3">
                        @php
                            $field = $leave->leave_type . '_leave';
                            $total = $leaveBalance->{$field . '_total'} ?? 0;
                            $used = $leaveBalance->{$field . '_used'} ?? 0;
                            $remaining = $total - $used;
                        @endphp
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border bg-light">
                                <div class="small fw-bold text-muted text-uppercase mb-1">Total Entitlement</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $total }} <small style="font-size: 0.8rem;">days</small></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border bg-light shadow-sm">
                                <div class="small fw-bold text-muted text-uppercase mb-1">Currently Utilized</div>
                                <h4 class="fw-bold mb-0 text-primary">{{ $used }} <small style="font-size: 0.8rem;">days</small></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border border-success border-opacity-25 bg-success bg-opacity-10">
                                <div class="small fw-bold text-success text-uppercase mb-1">Final Remaining</div>
                                <h4 class="fw-bold mb-0 text-success">
                                    {{ $remaining - ($leave->status === 'approved' ? 0 : $leave->total_days) }}
                                    <small style="font-size: 0.8rem;">days</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4 bg-light rounded-3 border border-dashed">
                        <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                        <p class="text-muted mb-0">No leave balance record found for this employee this year.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Components -->
        <div class="col-lg-4">
            <!-- Employee Snapshot -->
            <div class="card mb-4 fade-in-up" style="animation-delay: 0.3s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-user-circle"></i></div>
                    Employee Snapshot
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="user-avatar mx-auto mb-3 border border-white shadow-sm" style="width: 80px; height: 80px; font-size: 2.2rem; border-width: 4px !important;">
                            {{ strtoupper(substr($leave->employee->name ?? '??', 0, 2)) }}
                        </div>
                        <h5 class="fw-bold mb-1 text-dark">{{ $leave->employee->name ?? 'Unknown' }}</h5>
                        <p class="text-muted small mb-0">{{ $leave->employee->designation ?? 'N/A' }}</p>
                        <code class="text-primary mt-2 d-inline-block px-3 py-1 bg-primary bg-opacity-10 rounded-pill fw-bold" style="font-size: 0.8rem;">{{ $leave->employee->employee_code ?? 'N/A' }}</code>
                    </div>
                    
                    <hr class="my-4 opacity-10">
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-bold text-uppercase">Dept.</span>
                            <span class="badge badge-green px-3 py-2 fw-bold" style="background: rgba(126, 213, 58, 0.1); color: #7ed53a; border: 1px solid rgba(126, 213, 58, 0.2);">{{ $leave->employee->department ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-bold text-uppercase">Email</span>
                            <span class="fw-bold small text-dark">{{ $leave->employee->email ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-bold text-uppercase">Joined</span>
                            <span class="fw-bold small text-dark">{{ $leave->employee->joining_date ? \Carbon\Carbon::parse($leave->employee->joining_date)->format('d M Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Balance Summary -->
            @if($leaveBalance)
            <div class="card fade-in-up" style="animation-delay: 0.4s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-chart-pie"></i></div>
                    Balance Status ({{ $leaveBalance->year }})
                </div>
                <div class="card-body">
                    @php
                        $types = [
                            'sick'      => ['label' => 'Sick Leave',      'class' => 'bg-info'],
                            'casual'    => ['label' => 'Casual Leave',    'class' => 'bg-success'],
                            'annual'    => ['label' => 'Annual Leave',    'class' => 'bg-primary'],
                            'maternity' => ['label' => 'Maternity Leave', 'class' => 'bg-danger'],
                            'paternity' => ['label' => 'Paternity Leave', 'class' => 'bg-warning'],
                        ];
                    @endphp

                    @foreach($types as $key => $t)
                        @php
                            $total = $leaveBalance->{$key . '_leave_total'} ?? 0;
                            $used = $leaveBalance->{$key . '_leave_used'} ?? 0;
                            $percent = $total > 0 ? min(100, ($used / $total) * 100) : 0;
                        @endphp
                        @if($total > 0)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="small fw-bold text-dark">{{ $t['label'] }}</span>
                                <span class="small fw-bold">{{ $used }}/{{ $total }}</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px; background: rgba(0,0,0,0.05);">
                                <div class="progress-bar {{ $t['class'] }} progress-bar-striped" role="progressbar" 
                                     style="width: {{ $percent }}%; border-radius: 4px;"></div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Logic -->
<div class="modal fade" id="rejectModalContent" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-panel border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="fas fa-times-circle me-2"></i>Reject Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.leaves.reject', $leave) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Include an internal note explaining why this time-off request was denied. This will be shared with the employee.</p>
                    <label class="form-label small fw-bold text-uppercase opacity-75">Reason for Denial</label>
                    <textarea name="rejection_reason" class="form-control shadow-none border-danger border-opacity-10" rows="3" required placeholder="e.g. Schedule conflict with project rollout..."></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-danger px-4 fw-bold">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection