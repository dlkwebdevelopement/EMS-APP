@extends('layouts.admin')

@section('title', 'Account Deletion Requests')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-gray-800"><i class="fas fa-user-times me-2 text-danger"></i>Account Deletion Requests</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
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
                            <th class="px-4 py-3">Reason</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $requestItem)
                        <tr>
                            <td class="px-4">#{{ $requestItem->id }}</td>
                            <td class="px-4">
                                @if($requestItem->employee)
                                    <div class="fw-bold">{{ $requestItem->employee->name }}</div>
                                    <div class="small text-muted">{{ $requestItem->employee->employee_code }}</div>
                                @else
                                    <div class="fw-bold text-muted">Deleted Employee</div>
                                @endif
                            </td>
                            <td class="px-4">
                                {{ Str::limit($requestItem->reason, 60) }}
                                <button type="button" class="btn btn-link btn-sm p-0 ms-1" data-bs-toggle="modal" data-bs-target="#reasonModal{{ $requestItem->id }}">Read More</button>
                            </td>
                            <td class="px-4 text-muted small">
                                {{ $requestItem->created_at->format('d M, Y h:i A') }}
                            </td>
                            <td class="px-4">
                                @if($requestItem->status == 'pending')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                @elseif($requestItem->status == 'approved')
                                    <span class="badge bg-danger"><i class="fas fa-check me-1"></i>Approved (Deleted)</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Rejected</span>
                                @endif
                            </td>
                            <td class="px-4 text-end">
                                @if($requestItem->status == 'pending' && $requestItem->employee)
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('admin.account-deletions.approve', $requestItem) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this request? This will instantly HARD DELETE the employee and all their data!');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Approve & Delete Employee">
                                                <i class="fas fa-trash-alt me-1"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.account-deletions.reject', $requestItem) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Reject Request">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted small">No Actions</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                <h5>No Account Deletion Requests Found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>

@foreach($requests as $requestItem)
<!-- Reason Modal for ID {{ $requestItem->id }} -->
<div class="modal fade" id="reasonModal{{ $requestItem->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark">Reason for Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($requestItem->employee)
                    <p class="mb-3 text-muted"><strong>From:</strong> {{ $requestItem->employee->name }}</p>
                @endif
                <div class="p-3 bg-light rounded border text-dark" style="min-height: 100px;">
                    {!! nl2br(e($requestItem->reason)) !!}
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
