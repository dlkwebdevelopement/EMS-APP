{{-- resources/views/admin/time-sheets/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Time Sheets')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock me-2 text-primary"></i>Employee Time Sheets
            </h1>
            <p class="text-muted mt-2">Review and approve employee time sheets</p>
        </div>
        <div>
            <a href="{{ route('admin.time-sheets.report') }}" class="btn btn-info">
                <i class="fas fa-chart-bar me-1"></i> Reports
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Hours</h6>
                            <h2 class="mb-0">{{ number_format($timeSheets->sum('total_hours'), 1) }}</h2>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Approved</h6>
                            <h2 class="mb-0">{{ $timeSheets->where('status', 'approved')->count() }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Pending</h6>
                            <h2 class="mb-0">{{ $timeSheets->where('status', 'pending')->count() }}</h2>
                        </div>
                        <i class="fas fa-hourglass-half fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Employees</h6>
                            <h2 class="mb-0">{{ $timeSheets->groupBy('employee_id')->count() }}</h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                <h6 class="mb-0 fw-semibold">Filter Time Sheets</h6>
                <button class="btn btn-link btn-sm ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body pt-0">
                <form method="GET" action="{{ route('admin.time-sheets.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Employee</label>
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
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Time Sheets Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-table me-2 text-primary"></i>Time Sheet Entries
                </h6>
                <div>
                    <span class="badge bg-success">{{ $timeSheets->where('status', 'approved')->count() }} Approved</span>
                    <span class="badge bg-warning">{{ $timeSheets->where('status', 'pending')->count() }} Pending</span>
                    <span class="badge bg-danger">{{ $timeSheets->where('status', 'rejected')->count() }} Rejected</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3">Employee</th>
                            <th class="py-3">Start Time</th>
                            <th class="py-3">End Time</th>
                            <th class="py-3">Break</th>
                            <th class="py-3">Total Hours</th>
                            <th class="py-3">Project</th>
                            <th class="py-3">Task Description</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timeSheets as $sheet)
                        <tr>
                            <td class="px-4">{{ \Carbon\Carbon::parse($sheet->date)->format('d M Y') }}</td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $sheet->employee->name }}</div>
                                    <small class="text-muted">{{ $sheet->employee->employee_code }}</small>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($sheet->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($sheet->end_time)->format('h:i A') }}</td>
                            <td>{{ $sheet->break_hours }} hrs</td>
                            <td class="fw-bold text-primary">{{ $sheet->total_hours }} hrs</td>
                            <td>
                                @if($sheet->project)
                                    <span class="badge bg-info">{{ $sheet->project }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ Str::limit($sheet->task_description, 40) }}</td>
                            <td>
                                @if($sheet->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($sheet->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-info" onclick="viewTimeSheet({{ $sheet->id }})" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($sheet->status == 'pending')
                                        <button class="btn btn-sm btn-outline-success" onclick="approveTimeSheet({{ $sheet->id }})" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="rejectTimeSheet({{ $sheet->id }})" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No time sheets found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $timeSheets->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Time Sheet Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ban me-2"></i>Reject Time Sheet
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" rows="4" required placeholder="Please provide a detailed reason for rejection..."></textarea>
                        <small class="text-muted">This reason will be shared with the employee.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function viewTimeSheet(id) {
        $('#viewModalBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading time sheet details...</p>
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
                let errorMessage = 'Failed to load time sheet details';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('#viewModalBody').html(`
                    <div class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                        <p>${errorMessage}</p>
                        <button class="btn btn-sm btn-outline-danger" onclick="location.reload()">Retry</button>
                    </div>
                `);
                showToast(errorMessage, 'error');
            }
        });
    }
    
    function approveTimeSheet(id) {
        Swal.fire({
            title: 'Approve Time Sheet?',
            text: 'Are you sure you want to approve this time sheet? This action cannot be undone.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '/admin/time-sheets/' + id + '/approve',
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).catch((error) => {
            showToast(error.message || 'An error occurred', 'error');
        });
    }
    
    function rejectTimeSheet(id) {
        $('#rejectForm').attr('action', '/admin/time-sheets/' + id + '/reject');
        $('#rejectModal').modal('show');
    }
    
    // Handle reject form submission
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const action = form.attr('action');
        const remarks = form.find('textarea[name="remarks"]').val();
        
        if (!remarks.trim()) {
            showToast('Please provide a rejection reason', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Reject Time Sheet?',
            text: 'Are you sure you want to reject this time sheet?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, reject it!',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: action,
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
        }).catch((error) => {
            showToast(error.message || 'An error occurred', 'error');
        });
        
        $('#rejectModal').modal('hide');
    });
    
    function showToast(message, type) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
</script>
@endpush
@endsection