@extends('layouts.admin')

@section('title', 'Assigned Leads - Team Leader')
@section('page-title', 'Team Leader Leads')
@section('page-subtitle', 'Manage project leads assigned to you and oversee employee worker assignments')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter pills --}}
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 14px;">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('tl.leads.index') }}" 
                   class="btn btn-sm {{ !request('worker_status') ? 'btn-success text-white' : 'btn-outline-secondary' }}" 
                   style="border-radius: 20px;">
                    All Assigned Leads <span class="badge bg-white text-dark ms-1">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('tl.leads.index', ['worker_status' => 'has_workers']) }}" 
                   class="btn btn-sm {{ request('worker_status') === 'has_workers' ? 'btn-primary text-white' : 'btn-outline-secondary' }}" 
                   style="border-radius: 20px;">
                    <i class="fas fa-users me-1"></i> In Execution (Workers Assigned) <span class="badge bg-white text-dark ms-1">{{ $counts['with_workers'] }}</span>
                </a>
                <a href="{{ route('tl.leads.index', ['worker_status' => 'no_workers']) }}" 
                   class="btn btn-sm {{ request('worker_status') === 'no_workers' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}" 
                   style="border-radius: 20px;">
                    <i class="fas fa-exclamation-triangle me-1"></i> Awaiting Worker Assignment <span class="badge bg-white text-dark ms-1">{{ $counts['no_workers'] }}</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('tl.reports') }}" class="btn btn-sm btn-outline-secondary fw-bold" style="border-radius: 10px;">
                    <i class="fas fa-chart-line me-1"></i> TL Execution Reports
                </a>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Lead Code</th>
                        <th>Client / Enquiry</th>
                        <th>From Sales</th>
                        <th>Lead Status</th>
                        <th>Assigned Workers</th>
                        <th>Project Execution Timeline</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td>
                            <a href="{{ route('tl.leads.show', $lead) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $lead->lead_code }}
                            </a>
                            <div class="small text-muted">{{ $lead->created_at->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $lead->name }}</div>
                            <div class="small text-muted">{{ $lead->domain ?? 'General' }} | {{ $lead->service_needed ?? 'Standard' }}</div>
                        </td>
                        <td>
                            @if($lead->salesAgent)
                                <div class="small fw-bold text-dark">{{ $lead->salesAgent->name }}</div>
                            @else
                                <span class="text-muted small">Direct / Inbound</span>
                            @endif
                        </td>
                        <td>
                            @php $badge = $lead->status_badge; @endphp
                            <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        </td>
                        <td>
                            @if($lead->leadWorkers->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($lead->leadWorkers as $lw)
                                        @php $lwBadge = $lw->status_badge; @endphp
                                        <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">
                                            {{ $lw->worker ? $lw->worker->name : 'Staff' }}
                                            <span class="badge {{ $lwBadge['class'] }} ms-1" style="font-size: 0.6rem; padding: 2px 4px;">{{ $lwBadge['label'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-exclamation-circle me-1"></i> No Workers Assigned
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($lead->leadWorkers->count() > 0)
                                @php $firstWorker = $lead->leadWorkers->first(); @endphp
                                <div class="small fw-bold text-dark">
                                    {{ optional($firstWorker->start_date)->format('d M') }} &rarr; {{ optional($firstWorker->end_date)->format('d M Y') }}
                                </div>
                                @if($firstWorker->is_overdue)
                                    <span class="badge bg-danger text-white" style="font-size: 0.65rem;">Overdue</span>
                                @elseif($firstWorker->days_left !== null)
                                    <span class="badge bg-info text-white" style="font-size: 0.65rem;">{{ $firstWorker->days_left }} days left</span>
                                @endif
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @if($lead->leadWorkers->count() > 0)
                                    <button type="button" class="btn btn-success text-white" 
                                            style="background: #10b981; border-color: #10b981; color: #ffffff !important;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#assignWorkersModal{{ $lead->id }}" 
                                            title="Edit & Update Assigned Workers">
                                        <i class="fas fa-user-edit me-1 text-white" style="color: #ffffff !important;"></i>
                                        <span class="text-white" style="color: #ffffff !important;">Edit Workers ({{ $lead->leadWorkers->count() }})</span>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#assignWorkersModal{{ $lead->id }}" 
                                            title="Assign Workers">
                                        <i class="fas fa-users-cog me-1"></i> Assign Workers
                                    </button>
                                @endif
                                <a href="{{ route('tl.leads.show', $lead) }}" class="btn btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No matching leads assigned to you</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $leads->links() }}
            </div>
        @endif
    </div>

    {{-- MODALS PLACED OUTSIDE THE TABLE CONTAINER TO PREVENT BACKDROP OVERLAY BUGS --}}
    @foreach($leads as $lead)
        <div class="modal fade" id="assignWorkersModal{{ $lead->id }}" tabindex="-1" aria-labelledby="assignWorkersModalLabel{{ $lead->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content text-start border-0 shadow" style="border-radius: 16px; overflow: hidden;">
                    <form action="{{ route('tl.leads.assign-workers', $lead) }}" method="POST">
                        @csrf
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                            <div>
                                <h5 class="modal-title fw-bold text-white mb-0" id="assignWorkersModalLabel{{ $lead->id }}" style="color: #ffffff !important;">
                                    @if($lead->leadWorkers->count() > 0)
                                        <i class="fas fa-user-edit me-2 text-white" style="color: #ffffff !important;"></i> Edit & Update Assigned Workers
                                    @else
                                        <i class="fas fa-users-cog me-2 text-white" style="color: #ffffff !important;"></i> Assign Multiple Employees to Lead
                                    @endif
                                </h5>
                                <small class="text-white-50">Select or deselect employees to sync assignments, configure deadlines and instructions.</small>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 p-md-4">
                            <div class="alert alert-light border mb-4 p-3" style="border-radius: 12px; background: #f8fafc; border-color: #e2e8f0;">
                                <div class="fw-bold text-dark fs-6">{{ $lead->lead_code }} &mdash; {{ $lead->name }}</div>
                                <div class="small text-muted mt-0.5">Domain: {{ $lead->domain ?? 'General' }} &bull; Service: {{ $lead->service_needed ?? 'Standard' }}</div>
                                @if($lead->requirement)
                                    <div class="small text-dark mt-2 p-2.5 rounded-2 bg-white border" style="border-color: #e2e8f0;">
                                        <strong>Requirement:</strong> {{ $lead->requirement }}
                                    </div>
                                @endif
                            </div>

                            {{-- Modern Add Button Style Employee Multi-Selector --}}
                            @include('components.employee-worker-selector', ['lead' => $lead, 'employees' => $employees])

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted mb-1.5">Project Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" style="border-radius: 10px; padding: 10px 14px;"
                                           value="{{ optional($lead->leadWorkers->first())->start_date ? $lead->leadWorkers->first()->start_date->format('Y-m-d') : date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted mb-1.5">Project End Date (Submission Deadline) <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" style="border-radius: 10px; padding: 10px 14px;"
                                           value="{{ optional($lead->leadWorkers->first())->end_date ? $lead->leadWorkers->first()->end_date->format('Y-m-d') : date('Y-m-d', strtotime('+14 days')) }}" required>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold small text-muted mb-1.5">Instructions & Deliverables</label>
                                <textarea name="instructions" class="form-control" rows="3" style="border-radius: 10px; padding: 10px 14px;" placeholder="Provide task requirements, deliverables, milestones...">{{ optional($lead->leadWorkers->first())->instructions }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3 px-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px; padding: 8px 16px;" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn text-white fw-bold px-4 shadow-sm" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); color: #ffffff !important; border-radius: 8px; padding: 9px 22px; border: none;">
                                <i class="fas fa-check-circle me-1 text-white" style="color: #ffffff !important;"></i>
                                <span class="text-white" style="color: #ffffff !important;">
                                    @if($lead->leadWorkers->count() > 0)
                                        Update & Save Workers
                                    @else
                                        Save & Assign Workers
                                    @endif
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection
