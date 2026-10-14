@extends('layouts.admin')

@section('title', 'Shift Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Shift Management</h2>
            <p class="text-muted small mb-0">Configure company work roster timings, break policies, and weekly off conditions.</p>
        </div>
        <a href="{{ route('admin.shifts.create') }}" class="btn btn-primary fw-bold">
            <i class="fas fa-plus me-1"></i> Add Shift
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <ul class="nav nav-pills mb-4" id="shiftTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 fw-bold me-2" id="shifts-tab" data-bs-toggle="tab" data-bs-target="#shifts" type="button" role="tab" aria-controls="shifts" aria-selected="true" style="border-radius: 50rem;">
                <i class="fas fa-clipboard-list me-2"></i> Configured Shifts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 fw-bold" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab" aria-controls="permissions" aria-selected="false" style="border-radius: 50rem;">
                <i class="fas fa-user-clock me-2"></i> 2-Hour Permission Requests
            </button>
        </li>
    </ul>

    <div class="tab-content" id="shiftTabsContent">
        <div class="tab-pane fade show active" id="shifts" role="tabpanel" aria-labelledby="shifts-tab">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Shift Name</th>
                            <th>Timings (Start - End)</th>
                            <th>Duration</th>
                            <th>Lunch Break</th>
                            <th>Break 2</th>
                            <th>Weekly Offs</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary font-monospace">{{ $shift->code ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $shift->name }}</div>
                                    @if($shift->is_default)
                                        <span class="badge bg-success-soft text-success small" style="font-size: 0.7rem; padding: 2px 6px;">Default Shift</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark">
                                        <i class="far fa-clock text-muted me-1"></i>
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }}
                                    </div>
                                    @if($shift->grace_period_minutes > 0)
                                        <small class="text-muted">Grace: {{ $shift->grace_period_minutes }} mins</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary">{{ $shift->shift_duration ?? 'N/A' }} hrs</span>
                                </td>
                                <td>
                                    @if($shift->break1_enabled)
                                        <div class="small">
                                            <div>{{ \Carbon\Carbon::parse($shift->break1_begin_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($shift->break1_end_time)->format('g:i A') }}</div>
                                            <span class="text-muted">Duration: {{ $shift->break1_duration ?? 'N/A' }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    @if($shift->break2_enabled)
                                        <div class="small">
                                            <div>{{ \Carbon\Carbon::parse($shift->break2_begin_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($shift->break2_end_time)->format('g:i A') }}</div>
                                            <span class="text-muted">Duration: {{ $shift->break2_duration ?? 'N/A' }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <div><strong>1st:</strong> {{ $shift->first_weekly_off ?? 'None' }}</div>
                                        <div>
                                            <strong>2nd:</strong> {{ $shift->second_weekly_off ?? 'None' }} 
                                            @if($shift->second_weekly_off_weeks)
                                                ({{ $shift->second_weekly_off_weeks }})
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end px-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.shifts.edit', $shift) }}" class="btn btn-sm btn-light text-primary border shadow-sm" title="Edit Shift" style="border-radius: 8px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.shifts.destroy', $shift) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this work shift roster?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-light text-danger border shadow-sm" type="submit" title="Delete Shift" style="border-radius: 8px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-clock fa-2x mb-3 text-light"></i>
                                    <div>No shifts configured yet.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($shifts->hasPages())
                <div class="card-footer border-0 bg-white py-3">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    </div>
            </div>
        </div>

        <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Timing (Start - End)</th>
                                    <th>Duration</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th class="text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary-soft text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                                    {{ substr($permission->employee->name ?? 'E', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $permission->employee->name ?? 'N/A' }}</div>
                                                    <div class="small text-muted">{{ $permission->employee->employee_code ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($permission->date)->format('M d, Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="text-dark">
                                                <i class="far fa-clock text-muted me-1"></i>
                                                {{ \Carbon\Carbon::parse($permission->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($permission->end_time)->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-soft text-info">{{ $permission->hours }} hrs</span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 150px;" title="{{ $permission->reason }}">{{ $permission->reason ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            @if($permission->status === 'approved')
                                                <span class="badge bg-success-soft text-success"><i class="fas fa-check me-1"></i> Approved</span>
                                            @elseif($permission->status === 'rejected')
                                                <span class="badge bg-danger-soft text-danger"><i class="fas fa-times me-1"></i> Rejected</span>
                                            @else
                                                <span class="badge bg-warning-soft text-warning"><i class="fas fa-clock me-1"></i> Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-end px-4">
                                            @if($permission->status === 'pending')
                                                <div class="d-flex justify-content-end gap-2">
                                                    <form action="{{ route('admin.shifts.permissions.approve', $permission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this permission request?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light text-success border shadow-sm" title="Approve" style="border-radius: 8px;">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.shifts.permissions.reject', $permission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this permission request?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm" title="Reject" style="border-radius: 8px;">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-muted small">Processed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-envelope-open-text fa-2x mb-3 text-light"></i>
                                            <div>No permission requests found.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($permissions) && $permissions->hasPages())
                        <div class="card-footer border-0 bg-white py-3">
                            {{ $permissions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        background-color: var(--gd);
        color: #fff !important;
    }
    .bg-info-soft {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .text-info {
        color: #0dcaf0 !important;
    }
</style>
@endpush
@endsection
