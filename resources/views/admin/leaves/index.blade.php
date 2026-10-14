{{-- resources/views/admin/leaves/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Leave Management')

@section('content')
<style>
    .page-hero-strip h2 { color: #ffffff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    .page-hero-strip p { color: rgba(255,255,255,0.95) !important; }
    .card-header { color: #0f172a !important; font-weight: 700 !important; }
    .form-label { color: #0f172a !important; font-weight: 700 !important; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-select, .form-control { color: #0f172a !important; font-weight: 600 !important; border-color: #cbd5e1 !important; background-color: #ffffff !important; }
    .table th { color: #0f172a !important; font-weight: 700 !important; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; background-color: #f1f5f9 !important; }
    .table td { color: #0f172a !important; font-size: 0.88rem; font-weight: 500; }
    .stats-label { color: #334155 !important; font-weight: 700 !important; }
    .stats-val { color: #0f172a !important; font-weight: 800 !important; }
</style>
<div class="container-fluid px-0">
    
    <!-- Hero Strip Section -->
    <div class="page-hero-strip mb-4 fade-in-up">
        <div class="strip-rule"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Leave Requests</h2>
                <p class="small mb-0">Review and manage employee time-off applications</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.leaves.report') }}" class="btn btn-light text-dark fw-bold shadow-sm px-3">
                    <i class="fas fa-chart-pie me-1 text-primary"></i> Analytics
                </a>
                <button type="button" class="btn btn-success text-white fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#quickCreateLeaveModal">
                    <i class="fas fa-plus me-1"></i> + New Request
                </button>
                <button type="button" class="btn btn-success text-white fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#editLeaveBalanceModal">
                    <i class="fas fa-balance-scale me-1"></i> Edit Leave Balances
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Premium Modern Layout -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="p-3 rounded-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-hourglass-half fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">Pending</div>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.4rem;">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="p-3 rounded-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-user-check fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">On Leave Today</div>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.4rem;">{{ $stats['on_leave_today'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="p-3 rounded-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-calendar-alt fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">Upcoming</div>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.4rem;">{{ $stats['upcoming'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="p-3 rounded-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-check-circle fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">Approved (Month)</div>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.4rem;">{{ $stats['approved_month'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="p-3 rounded-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-ban fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">Rejected (Month)</div>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.4rem;">{{ $stats['rejected_month'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="p-3 rounded-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3">
                <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-history fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">Total Days (Month)</div>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.4rem;">{{ $stats['total_days_month'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4 shadow-sm border-0 rounded-3">
        <div class="card-header bg-white fw-bold py-3 d-flex align-items-center gap-2">
            <i class="fas fa-sliders-h text-primary"></i> Filter Leave Applications
        </div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.leaves.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>Approved</option>
                        <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Leave Type</label>
                    <select name="leave_type" class="form-select">
                        <option value="">All Categories</option>
                        <option value="sick"      {{ request('leave_type') == 'sick'      ? 'selected' : '' }}>Sick Leave</option>
                        <option value="casual"    {{ request('leave_type') == 'casual'    ? 'selected' : '' }}>Casual Leave</option>
                        <option value="annual"    {{ request('leave_type') == 'annual'    ? 'selected' : '' }}>Annual Leave</option>
                        <option value="unpaid"    {{ request('leave_type') == 'unpaid'    ? 'selected' : '' }}>Unpaid Leave</option>
                        <option value="maternity" {{ request('leave_type') == 'maternity' ? 'selected' : '' }}>Maternity Leave</option>
                        <option value="paternity" {{ request('leave_type') == 'paternity' ? 'selected' : '' }}>Paternity Leave</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-1">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaves Table -->
    <div class="card fade-in-up" style="animation-delay: 0.15s;">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-list-alt"></i>
            </div>
            Leave Applications
            <div class="ms-auto">
                <span class="badge bg-gradient-success">
                    <i class="fas fa-envelope me-1"></i> {{ $leaves->total() }} Records
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th class="text-center">Days</th>
                            <th>Reason</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                        @php
                            $typeConfig = [
                                'sick' => ['icon' => 'fa-thermometer-half', 'badge' => 'badge-info'],
                                'casual' => ['icon' => 'fa-coffee', 'badge' => 'badge-green'],
                                'annual' => ['icon' => 'fa-umbrella-beach', 'badge' => 'badge-lime'],
                                'unpaid' => ['icon' => 'fa-ban', 'badge' => 'badge-gold'],
                                'maternity' => ['icon' => 'fa-baby', 'badge' => 'badge-info'],
                                'paternity' => ['icon' => 'fa-child', 'badge' => 'badge-info'],
                            ];
                            $config = $typeConfig[$leave->leave_type] ?? ['icon' => 'fa-calendar', 'badge' => 'badge-green'];
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width: 36px; height: 36px; font-size: 0.75rem;">
                                        {{ $leave->employee ? strtoupper(substr($leave->employee->name, 0, 2)) : '??' }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--gd);">
                                            {{ $leave->employee->name ?? 'Unknown Employee' }}
                                        </div>
                                        <small class="text-muted">{{ $leave->employee->employee_code ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $config['badge'] }}">
                                    <i class="fas {{ $config['icon'] }} me-1"></i>
                                    {{ ucfirst($leave->leave_type ?? 'Unknown') }}
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <div>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</div>
                                    <i class="fas fa-arrow-down text-muted" style="font-size: 10px;"></i>
                                    <div>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</div>
                                </div>
                            </td>
                            <td class="text-center fw-bold" style="color: var(--gb);">
                                {{ $leave->total_days }}
                            </td>
                            <td>
                                <span title="{{ $leave->reason }}">
                                    {{ Str::limit($leave->reason ?? '', 35) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($leave->created_at)->format('d M Y') }}</small>
                            </td>
                            <td>
                                @if($leave->status == 'pending')
                                    <span class="badge badge-gold">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                @elseif($leave->status == 'approved')
                                    <span class="badge badge-lime">
                                        <i class="fas fa-check me-1"></i>Approved
                                    </span>
                                @elseif($leave->status == 'rejected')
                                    <span class="badge badge-red">
                                        <i class="fas fa-times me-1"></i>Rejected
                                    </span>
                                @else
                                    <span class="badge badge-green">
                                        <i class="fas fa-ban me-1"></i>{{ ucfirst($leave->status ?? 'Unknown') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.leaves.show', $leave) }}"
                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($leave->status == 'pending' && $leave->employee)
                                        <button class="btn btn-sm btn-outline-success"
                                                title="Approve"
                                                onclick="approveLeave({{ $leave->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger"
                                                title="Reject"
                                                onclick="rejectLeave({{ $leave->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if(in_array($leave->status, ['pending', 'approved']))
                                        <button class="btn btn-sm btn-outline-warning"
                                                title="Cancel"
                                                onclick="cancelLeave({{ $leave->id }})">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No leave records found.</p>
                                <small class="text-muted">Try adjusting your filters</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-center">
                {{ $leaves->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc2626, #b91c1c); border: none;">
                <h5 class="modal-title text-white" id="rejectModalLabel">
                    <i class="fas fa-ban me-2"></i>Reject Leave Application
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" rows="3" required
                                  placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban me-1"></i> Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: Create New Leave Application --}}
<div class="modal fade" id="quickCreateLeaveModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.leaves.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle text-success me-2"></i> Submit New Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Employee <span class="text-danger">*</span></label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">-- Choose Employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code }}) — {{ $emp->department }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Leave Category <span class="text-danger">*</span></label>
                        <select name="leave_type" class="form-select" required>
                            <option value="casual">☕ Casual Leave</option>
                            <option value="sick">🏥 Sick Leave</option>
                            <option value="annual">🏖️ Annual Leave</option>
                            <option value="unpaid">⛔ Unpaid Leave</option>
                            <option value="maternity">👶 Maternity Leave</option>
                            <option value="paternity">👨‍🍼 Paternity Leave</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Supporting Document (Optional)</label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Reason for Leave <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Specify the reason for leave..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success fw-bold">
                    <i class="fas fa-paper-plane me-1"></i> Submit Leave Application
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function approveLeave(id) {
        Swal.fire({
            title: 'Approve Leave?',
            text: "Are you sure you want to approve this leave request?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/leaves/' + id + '/approve';
                form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function rejectLeave(id) {
        document.getElementById('rejectForm').action = '/admin/leaves/' + id + '/reject';
        $('#rejectModal').modal('show');
    }

    function cancelLeave(id) {
        Swal.fire({
            title: 'Cancel Leave?',
            text: "Are you sure you want to cancel this leave?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/leaves/' + id + '/cancel';
                form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function exportLeaves() {
        const status = $('select[name="status"]').val();
        const fromDate = $('input[name="from_date"]').val();
        const toDate = $('input[name="to_date"]').val();
        
        Swal.fire({
            title: 'Export Leave Data',
            html: '<span class="text-muted">Choose your preferred export format</span>',
            icon: 'info',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: '<i class="fas fa-file-csv me-1"></i> CSV (Excel)',
            cancelButtonText: '<i class="fas fa-file-pdf me-1"></i> PDF',
            denyButtonText: 'Cancel',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success mx-1',
                cancelButton: 'btn btn-primary mx-1',
                denyButton: 'btn btn-secondary mx-1'
            }
        }).then((result) => {
            let format = '';
            if (result.isConfirmed) format = 'csv';
            else if (result.dismiss === Swal.DismissReason.cancel) format = 'pdf';
            else return;

            window.location.href = '{{ route("admin.leaves.export") }}?format=' + format + '&status=' + status + '&from_date=' + fromDate + '&to_date=' + toDate;
        });
    }
</script>

<!-- Edit / Update Employee Leave Balances Modal -->
<div class="modal fade" id="editLeaveBalanceModal" tabindex="-1" aria-labelledby="editLeaveBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="editLeaveBalanceModalLabel">
                    <i class="fas fa-user-edit me-2"></i> Edit & Update Employee Leave Balances
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.leaves.balance.update') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- EMPLOYEE SELECTOR -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1">Select Employee:</label>
                        <select class="form-select border-success fw-semibold" name="employee_id" required>
                            <option value="all" class="fw-bold text-success">🌟 All Employees (Bulk Update)</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code ?? ('EMP-'.$emp->id) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1">Processing Year:</label>
                        <input type="number" class="form-control" name="year" value="{{ date('Y') }}" required>
                    </div>

                    <!-- CASUAL LEAVE (CL) -->
                    <div class="row g-2 mb-3 bg-light p-2 rounded border">
                        <div class="col-12"><strong class="text-success">Casual Leave (CL) Quota</strong></div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Total Annual Quota</label>
                            <input type="number" class="form-control" name="casual_leave_total" value="12" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Used Days</label>
                            <input type="number" class="form-control" name="casual_leave_used" value="0" min="0" required>
                        </div>
                    </div>

                    <!-- SICK LEAVE (SL) -->
                    <div class="row g-2 mb-3 bg-light p-2 rounded border">
                        <div class="col-12"><strong class="text-primary">Sick Leave (SL) Quota</strong></div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Total Annual Quota</label>
                            <input type="number" class="form-control" name="sick_leave_total" value="12" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Used Days</label>
                            <input type="number" class="form-control" name="sick_leave_used" value="0" min="0" required>
                        </div>
                    </div>

                    <!-- ANNUAL / PAID LEAVE (EL/PL) -->
                    <div class="row g-2 mb-3 bg-light p-2 rounded border">
                        <div class="col-12"><strong class="text-warning">Annual / Earned Leave (EL/PL) Quota</strong></div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Total Annual Quota</label>
                            <input type="number" class="form-control" name="annual_leave_total" value="15" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Used Days</label>
                            <input type="number" class="form-control" name="annual_leave_used" value="0" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fas fa-save me-1"></i> Update Leave Balances</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush