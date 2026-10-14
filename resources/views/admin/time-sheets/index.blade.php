{{-- resources/views/admin/time-sheets/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Time Sheets')
@section('page-title', 'Employee Time Sheets')
@section('page-subtitle', 'Review and approve employee time sheet entries')

@section('content')
<div class="container-fluid px-0">

    {{-- Stats Cards Row --}}
    <div class="row g-4 mb-5 stagger-children">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ number_format($timeSheets->sum('total_hours'), 1) }}</h3>
                    <p>Total Hours</p>
                    <div class="stats-trend up">
                        <i class="fas fa-chart-line"></i> All entries
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card lime-card">
                <div class="stats-icon">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $timeSheets->where('status', 'approved')->count() }}</h3>
                    <p>Approved</p>
                    <div class="stats-trend">
                        <i class="fas fa-check"></i> Verified entries
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card gold-card">
                <div class="stats-icon">
                    <i class="fas fa-hourglass-half fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $timeSheets->where('status', 'pending')->count() }}</h3>
                    <p>Pending</p>
                    <div class="stats-trend">
                        <i class="fas fa-clock"></i> Awaiting review
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card teal-card">
                <div class="stats-icon">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $timeSheets->groupBy('employee_id')->count() }}</h3>
                    <p>Employees</p>
                    <div class="stats-trend">
                        <i class="fas fa-user-check"></i> Active submitters
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-filter"></i>
            </div>
            Filter Time Sheets
            <div class="ms-auto d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-sliders-h me-1"></i> Filters
                </button>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.time-sheets.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Employee</label>
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
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex w-100 gap-2">
                            <button type="submit" class="btn btn-success flex-grow-1">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                            <a href="{{ route('admin.time-sheets.index') }}" class="btn btn-outline-primary" title="Clear Filters">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Time Sheets Table --}}
    <div class="card fade-in-up" style="animation-delay: 0.2s;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="header-icon">
                    <i class="fas fa-table"></i>
                </div>
                Time Sheet Entries
            </div>
            <div>
                <a href="{{ route('admin.time-sheets.create') }}" class="btn btn-sm btn-success me-2">
                    <i class="fas fa-plus me-1"></i> New Entry
                </a>
                <a href="{{ route('admin.time-sheets.report') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-chart-pie me-1"></i> Analytics Report
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Break</th>
                            <th>Total Hours</th>
                            <th>Project</th>
                            <th>Task Description</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timeSheets as $sheet)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($sheet->date)->format('d M Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($sheet->date)->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.7rem;">
                                        {{ strtoupper(substr($sheet->employee->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $sheet->employee->name ?? 'Unknown' }}</div>
                                        <small class="text-muted">{{ $sheet->employee->employee_code ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($sheet->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($sheet->end_time)->format('h:i A') }}</td>
                            <td>{{ $sheet->break_hours }} h</td>
                            <td>
                                <span class="fw-bold" style="color: var(--gl);">{{ $sheet->total_hours }} hrs</span>
                            </td>
                            <td>
                                @if($sheet->project)
                                    <span class="badge badge-green">{{ $sheet->project }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span title="{{ $sheet->task_description }}">
                                    {{ Str::limit($sheet->task_description, 40) }}
                                </span>
                            </td>
                            <td>
                                @if($sheet->status == 'pending')
                                    <span class="badge badge-gold">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                @elseif($sheet->status == 'approved')
                                    <span class="badge badge-lime">
                                        <i class="fas fa-check me-1"></i>Approved
                                    </span>
                                @else
                                    <span class="badge badge-red">
                                        <i class="fas fa-times me-1"></i>Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group gap-1">
                                    <button class="btn btn-xs rounded-circle btn-outline-primary" onclick="viewTimeSheet({{ $sheet->id }})" title="View Details" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($sheet->status == 'pending')
                                        <button class="btn btn-xs rounded-circle btn-outline-success" onclick="approveTimeSheet({{ $sheet->id }})" title="Approve" style="width: 32px; height: 32px; padding: 0;">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-xs rounded-circle btn-outline-danger" onclick="rejectTimeSheet({{ $sheet->id }})" title="Reject" style="width: 32px; height: 32px; padding: 0;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-clock fa-3x opacity-25" style="color: var(--gd);"></i>
                                </div>
                                <h5 class="text-muted">No Time Sheet Entries Found</h5>
                                <p class="small text-muted mb-4">No time sheet entries match your criteria or none have been submitted yet.</p>
                                <a href="{{ route('admin.time-sheets.create') }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus me-1"></i> Add Your First Entry
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($timeSheets->hasPages())
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-center">
                {{ $timeSheets->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: transparent; border: none;">
            <div class="modal-body p-0" id="viewModalBody">
                <div class="text-center py-5">
                    <div class="loader mx-auto"></div>
                    <p class="mt-3 text-muted">Loading time sheet details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; border-radius: 28px 28px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-ban me-2"></i>Reject Time Sheet
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" rows="4" required placeholder="Please provide a detailed reason for rejection..."></textarea>
                        <small class="text-muted mt-2 d-block">This reason will be shared with the employee.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewTimeSheet(id) {
        $('#viewModalBody').html(`
            <div class="text-center py-5">
                <div class="loader mx-auto"></div>
                <p class="mt-3 text-muted">Loading time sheet details...</p>
            </div>
        `);
        
        $.ajax({
            url: '/admin/time-sheets/' + id,
            method: 'GET',
            success: function(response) {
                $('#viewModalBody').html(response);
                $('#viewModal').modal('show');
            },
            error: function(xhr) {
                $('#viewModalBody').html(`
                    <div class="text-center py-5 text-danger">
                        <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                        <p>Failed to load time sheet details</p>
                        <button class="btn btn-sm btn-outline-danger" onclick="location.reload()">Retry</button>
                    </div>
                `);
                showToast('Failed to load time sheet details', 'error');
            }
        });
    }
    
    function approveTimeSheet(id) {
        Swal.fire({
            title: 'Approve Time Sheet?',
            text: 'Are you sure you want to approve this time sheet?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '/admin/time-sheets/' + id + '/approve',
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            showToast('Time sheet approved successfully', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(response.message || 'Failed to approve');
                        }
                    },
                    error: function(xhr) {
                        throw new Error(xhr.responseJSON?.message || 'Failed to approve time sheet');
                    }
                });
            }
        }).catch((error) => {
            showToast(error.message || 'An error occurred', 'error');
        });
    }
    
    function rejectTimeSheet(id) {
        $('#rejectForm').attr('action', '/admin/time-sheets/' + id + '/reject');
        $('#rejectModal').modal('show');
    }
    
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        const remarks = $(this).find('textarea[name="remarks"]').val();
        
        if (!remarks.trim()) {
            showToast('Please provide a rejection reason', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Reject Time Sheet?',
            text: 'Are you sure you want to reject this time sheet?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#27a355',
            confirmButtonText: 'Yes, reject it!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: $(this).attr('action'),
                    method: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        remarks: remarks
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Time sheet rejected successfully', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(response.message || 'Failed to reject');
                        }
                    },
                    error: function(xhr) {
                        throw new Error(xhr.responseJSON?.message || 'Failed to reject time sheet');
                    }
                });
            }
        });
        $('#rejectModal').modal('hide');
    });
</script>
@endpush