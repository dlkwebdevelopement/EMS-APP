{{-- resources/views/admin/notifications/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Notifications Center')
@section('page-title', 'System Notifications')
@section('page-subtitle', 'Real-time alerts for Leaves, Assets, Payroll & Attendance')

@section('content')
<div class="fade-in">
    {{-- Summary Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 me-3"><i class="fas fa-bell"></i></div>
                    <div>
                        <h6 class="mb-0 text-white-50">Total Alerts</h6>
                        <h4 class="mb-0 text-white fw-bold">{{ $summary['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 me-3"><i class="fas fa-dot-circle"></i></div>
                    <div>
                        <h6 class="mb-0 text-white-50">Unread Alerts</h6>
                        <h4 class="mb-0 text-white fw-bold">{{ $summary['unread'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 me-3"><i class="fas fa-laptop-code"></i></div>
                    <div>
                        <h6 class="mb-0 text-dark-50">Asset Issues</h6>
                        <h4 class="mb-0 text-dark fw-bold">{{ $summary['assets'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 me-3"><i class="fas fa-envelope-open-text"></i></div>
                    <div>
                        <h6 class="mb-0 text-white-50">Leave Requests</h6>
                        <h4 class="mb-0 text-white fw-bold">{{ $summary['leaves'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Notifications Panel --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-bell text-success me-2"></i>Notifications Log
                </h5>
                @if($summary['unread'] > 0)
                    <span class="badge bg-danger text-white">{{ $summary['unread'] }} Unread</span>
                @endif
            </div>

            <div class="d-flex gap-2">
                @if($summary['unread'] > 0)
                    <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-check-double me-1"></i> Mark All Read
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.notifications.clear-all') }}" method="POST" onsubmit="return confirm('Clear all notification history?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash-alt me-1"></i> Clear All
                    </button>
                </form>
            </div>
        </div>

        {{-- Type Filter Pills --}}
        <div class="p-3 bg-light border-bottom d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm {{ !$type ? 'btn-success' : 'btn-outline-secondary' }}">
                All Alerts
            </a>
            <a href="{{ route('admin.notifications.index', ['type' => 'asset_issue']) }}" class="btn btn-sm {{ $type === 'asset_issue' ? 'btn-success' : 'btn-outline-secondary' }}">
                <i class="fas fa-laptop me-1"></i> Asset Issues
            </a>
            <a href="{{ route('admin.notifications.index', ['type' => 'leave_request']) }}" class="btn btn-sm {{ $type === 'leave_request' ? 'btn-success' : 'btn-outline-secondary' }}">
                <i class="fas fa-calendar-minus me-1"></i> Leaves
            </a>
            <a href="{{ route('admin.notifications.index', ['type' => 'payroll']) }}" class="btn btn-sm {{ $type === 'payroll' ? 'btn-success' : 'btn-outline-secondary' }}">
                <i class="fas fa-money-bill-wave me-1"></i> Payroll
            </a>
            <a href="{{ route('admin.notifications.index', ['type' => 'attendance']) }}" class="btn btn-sm {{ $type === 'attendance' ? 'btn-success' : 'btn-outline-secondary' }}">
                <i class="fas fa-user-clock me-1"></i> Attendance
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Type</th>
                            <th>Title & Message</th>
                            <th>Recipient / Reporter</th>
                            <th>Date & Time</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notif)
                        <tr class="{{ !$notif->is_read ? 'table-warning' : '' }}">
                            <td class="ps-4">
                                @php
                                    $iconClass = [
                                        'asset_issue' => 'fa-laptop text-danger',
                                        'leave_request' => 'fa-envelope-open-text text-warning',
                                        'payroll' => 'fa-money-bill-wave text-success',
                                        'attendance' => 'fa-user-clock text-info',
                                    ][$notif->type] ?? 'fa-bell text-primary';
                                @endphp
                                <div class="rounded-circle bg-white shadow-sm p-2 text-center" style="width: 36px; height: 36px;">
                                    <i class="fas {{ $iconClass }}"></i>
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark d-block mb-1">{{ $notif->title }}</strong>
                                <span class="text-muted text-sm">{{ $notif->message }}</span>
                            </td>
                            <td>
                                @if($notif->employee)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle text-secondary me-2 fs-5"></i>
                                        <div>
                                            <strong class="text-dark">{{ $notif->employee->name }}</strong><br>
                                            <small class="text-muted">{{ $notif->employee->employee_code }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-dark text-white"><i class="fas fa-user-shield me-1"></i>Admin / Global</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted fw-bold">{{ $notif->created_at ? $notif->created_at->format('d M Y, h:i A') : 'N/A' }}</small>
                                <br>
                                <small class="text-muted">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</small>
                            </td>
                            <td class="text-center">
                                @if(!$notif->is_read)
                                    <span class="badge bg-danger text-white"><i class="fas fa-dot-circle me-1"></i>Unread</span>
                                @else
                                    <span class="badge bg-light text-secondary border">Read</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if(!$notif->is_read)
                                    <form action="{{ route('admin.notifications.mark-read', $notif) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success text-white" title="Mark as Read">
                                            <i class="fas fa-check"></i> Read
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-light text-muted" disabled><i class="fas fa-check-circle"></i></button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-bell-slash fs-1 d-block mb-2 opacity-50"></i>
                                No notifications found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
