@extends('layouts.admin')

@section('title', 'Sales Help Desk & Support')
@section('page-title', 'Help Desk')
@section('page-subtitle', 'Submit tickets, raise queries to HR/Admin, and track resolution status')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #eaf6ed; color: #0d3b1a;">
            <i class="fas fa-check-circle me-2 text-success"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle text-white d-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px; background: #f59e0b;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Pending Review</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $pendingCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle text-white d-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px; background: #3b82f6;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">In Progress</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $inProgressCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle text-white d-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px; background: #10b981;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Resolved Tickets</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $resolvedCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Container: Submit Ticket & Recent Tickets --}}
    <div class="row g-4">
        {{-- LEFT: Submit New Ticket --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-plus-circle text-success"></i> Create Support Ticket
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="{{ route('sales.support.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Ticket Subject</label>
                            <input type="text" name="subject" class="form-control" required placeholder="e.g. Lead re-assignment issue or pipeline inquiry">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Description / Details</label>
                            <textarea name="message" rows="5" class="form-control" required placeholder="Describe the issue, lead reference number, or assistance needed..."></textarea>
                        </div>
                        <button type="submit" class="btn text-white fw-bold w-100 py-2.5 shadow-sm" 
                                style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); border-radius: 10px;">
                            <i class="fas fa-paper-plane me-1"></i> Submit to Help Desk
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: Ticket List --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-ticket-alt text-success"></i> My Support Tickets
                    </h6>
                    <div class="d-flex gap-1">
                        <a href="{{ route('sales.support') }}" class="btn btn-sm {{ !request('status') ? 'btn-success text-white' : 'btn-light text-muted' }}" style="border-radius: 8px; font-size: 0.75rem;">All</a>
                        <a href="{{ route('sales.support', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-light text-muted' }}" style="border-radius: 8px; font-size: 0.75rem;">Pending</a>
                        <a href="{{ route('sales.support', ['status' => 'resolved']) }}" class="btn btn-sm {{ request('status') === 'resolved' ? 'btn-success text-white' : 'btn-light text-muted' }}" style="border-radius: 8px; font-size: 0.75rem;">Resolved</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID & Subject</th>
                                <th>Status</th>
                                <th>Admin Response</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">#{{ $ticket->id }} - {{ $ticket->subject }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 260px;">{{ Str::limit($ticket->message, 50) }}</div>
                                </td>
                                <td>
                                    @if($ticket->status === 'resolved')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Resolved</span>
                                    @elseif($ticket->status === 'in_progress')
                                        <span class="badge bg-primary"><i class="fas fa-spinner fa-spin me-1"></i>In Progress</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->admin_reply)
                                        <span class="text-success small fw-semibold">
                                            <i class="fas fa-reply me-1"></i> {{ Str::limit($ticket->admin_reply, 40) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Awaiting review</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $ticket->created_at->format('d M, Y') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-headset fa-3x mb-3 text-muted opacity-50"></i>
                                    <div class="fw-bold">No support tickets found</div>
                                    <small>Use the form on the left to submit questions or issues to the Help Desk</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($tickets->hasPages())
                    <div class="p-3 border-top">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
