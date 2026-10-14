@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-gray-800">
            <a href="{{ route('admin.support.index') }}" class="text-decoration-none text-muted me-2"><i class="fas fa-arrow-left"></i></a>
            Ticket #{{ $ticket->id }}
        </h4>
        @if($ticket->status == 'pending')
            <span class="badge bg-warning text-dark px-3 py-2 fs-6"><i class="fas fa-clock me-1"></i>Pending</span>
        @elseif($ticket->status == 'in_progress')
            <span class="badge bg-info px-3 py-2 fs-6"><i class="fas fa-spinner me-1"></i>In Progress</span>
        @else
            <span class="badge bg-success px-3 py-2 fs-6"><i class="fas fa-check me-1"></i>Resolved</span>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">{{ $ticket->subject }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            @if($ticket->employee->profile_photo)
                                <img src="{{ Storage::url($ticket->employee->profile_photo) }}" class="rounded-circle" width="48" height="48" style="object-fit:cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:48px;height:48px;font-size:1.2rem;">
                                    {{ substr($ticket->employee->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ $ticket->employee->name }}</h6>
                            <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i>{{ $ticket->created_at->format('d M, Y h:i A') }}</small>
                        </div>
                    </div>
                    <div class="p-3 bg-light rounded-3 text-dark">
                        {!! nl2br(e($ticket->message)) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold">Update Ticket</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.support.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Admin Reply / Notes</label>
                            <textarea name="admin_reply" class="form-control" rows="4" placeholder="Enter notes or reply back to employee...">{{ old('admin_reply', $ticket->admin_reply) }}</textarea>
                            <small class="text-muted mt-1">This reply will be visible to the employee.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Update Ticket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
