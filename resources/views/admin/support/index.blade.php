@extends('layouts.admin')

@section('title', 'Help Desk & Support Tickets')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-gray-800"><i class="fas fa-headset me-2 text-primary"></i>Help Desk</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="px-4">#{{ $ticket->id }}</td>
                            <td class="px-4">
                                <div class="fw-bold">{{ $ticket->employee->name }}</div>
                                <div class="small text-muted">{{ $ticket->employee->employee_code }}</div>
                            </td>
                            <td class="px-4">
                                {{ Str::limit($ticket->subject, 50) }}
                            </td>
                            <td class="px-4">
                                @if($ticket->status == 'pending')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                @elseif($ticket->status == 'in_progress')
                                    <span class="badge bg-info"><i class="fas fa-spinner me-1"></i>In Progress</span>
                                @else
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Resolved</span>
                                @endif
                            </td>
                            <td class="px-4 text-muted small">
                                {{ $ticket->created_at->format('d M, Y h:i A') }}
                            </td>
                            <td class="px-4 text-end">
                                <a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                    View <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <h5>No Support Tickets Found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tickets->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
