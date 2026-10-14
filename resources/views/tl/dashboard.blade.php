@extends('layouts.admin')

@section('title', 'Team Leader Dashboard')
@section('page-title', 'TL Execution Command Hub')
@section('page-subtitle', 'Welcome back, ' . $tlUser->name . ' — Manage leads, assign multi-worker project teams & track submission deadlines')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════
       TL KPI METRIC CARDS
    ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- Total Leads Assigned to TL --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle text-white d-flex align-items-center justify-content-center" 
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                        <i class="fas fa-crown fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Assigned Leads</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalAssignedLeads) }}</h3>
                        <small class="text-muted"><i class="fas fa-user-plus me-1 text-warning"></i> {{ $unassignedWorkersCount }} without workers</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active In Progress Tasks --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle text-white d-flex align-items-center justify-content-center" 
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);">
                        <i class="fas fa-spinner fa-spin fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Active Projects</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($inProgressTasks) }}</h3>
                        <small class="text-primary fw-semibold"><i class="fas fa-users me-1"></i> {{ $totalWorkerTasks }} total workers assigned</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submissions Pending Review --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle text-white d-flex align-items-center justify-content-center" 
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                        <i class="fas fa-file-signature fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Submissions for Review</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($submittedTasks) }}</h3>
                        <small class="text-warning fw-semibold"><i class="fas fa-bell me-1"></i> Ready for TL Approval</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completed Projects --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle text-white d-flex align-items-center justify-content-center" 
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Completed Tasks</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($completedTasks) }}</h3>
                        @if($overdueTasks > 0)
                            <small class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> {{ $overdueTasks }} overdue</small>
                        @else
                            <small class="text-success fw-bold"><i class="fas fa-thumbs-up me-1"></i> All on track</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       PENDING SUBMISSIONS REVIEW BOX (IF ANY)
    ══════════════════════════════════════ --}}
    @if($pendingSubmissions->count() > 0)
        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-warning bg-white" style="border-radius: 14px;">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-bell text-warning"></i> Employee Project Submissions Awaiting Your Review ({{ $pendingSubmissions->count() }})
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lead / Project</th>
                                <th>Assigned Employee</th>
                                <th>Submission Date</th>
                                <th>Deliverable Notes</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingSubmissions as $sub)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $sub->lead->lead_code }} &mdash; {{ $sub->lead->name }}</div>
                                    <small class="text-muted">{{ $sub->lead->domain ?? 'General' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $sub->worker->name }}</div>
                                    <small class="text-muted">{{ $sub->worker->designation }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ optional($sub->submitted_at)->format('d M Y, h:i A') ?? 'Submitted' }}</div>
                                    @if($sub->submitted_at && $sub->end_date && $sub->submitted_at->lte($sub->end_date->endOfDay()))
                                        <span class="badge bg-success text-white" style="font-size: 0.65rem;">On-Time Submission</span>
                                    @else
                                        <span class="badge bg-danger text-white" style="font-size: 0.65rem;">Delayed Submission</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark small" style="max-width: 350px;">
                                        {{ Str::limit($sub->submission_notes, 120) ?: 'No notes provided.' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('tl.workers.approve', $sub) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-bold" onclick="return confirm('Confirm approval of this project deliverable?')">
                                            <i class="fas fa-check-circle me-1"></i> Approve & Complete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════
       RECENT ASSIGNED LEADS & MULTI-WORKER ASSIGNMENT
    ══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-tasks text-success"></i> Leads Assigned to You & Execution Status
            </h6>
            <div class="d-flex gap-2">
                <a href="{{ route('tl.leads.index') }}" class="btn btn-sm btn-outline-success fw-bold">
                    <i class="fas fa-list me-1"></i> View All TL Leads
                </a>
                <a href="{{ route('tl.reports') }}" class="btn btn-sm btn-outline-secondary fw-bold">
                    <i class="fas fa-chart-line me-1"></i> TL Reports
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Lead Code</th>
                        <th>Client / Requirement</th>
                        <th>From Sales</th>
                        <th>Lead Status</th>
                        <th>Assigned Multi-Workers</th>
                        <th>Timeline (Start &rarr; End Date)</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLeads as $lead)
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
                                <div class="text-muted" style="font-size: 0.72rem;">Sales Rep</div>
                            @else
                                <span class="text-muted small">Inbound / Direct</span>
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
                                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                            <i class="fas fa-user text-muted"></i> {{ $lw->worker ? $lw->worker->name : 'Worker' }}
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
                                    <i class="far fa-calendar-alt text-success me-1"></i>
                                    {{ optional($firstWorker->start_date)->format('d M') }} &rarr; {{ optional($firstWorker->end_date)->format('d M Y') }}
                                </div>
                                @if($firstWorker->is_overdue)
                                    <span class="badge bg-danger text-white" style="font-size: 0.65rem;">Overdue Deadline</span>
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
                                            title="Assign Multiple Employees">
                                        <i class="fas fa-users-cog me-1"></i> Assign Workers
                                    </button>
                                @endif
                                <a href="{{ route('tl.leads.show', $lead) }}" class="btn btn-outline-primary" title="Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>

                            {{-- MULTI-WORKER ASSIGNMENT MODAL --}}
                            <div class="modal fade" id="assignWorkersModal{{ $lead->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content text-start border-0 shadow">
                                        <form action="{{ route('tl.leads.assign-workers', $lead) }}" method="POST">
                                            @csrf
                                            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                                                <div>
                                                    <h5 class="modal-title fw-bold text-white mb-0" style="color: #ffffff !important;">
                                                        @if($lead->leadWorkers->count() > 0)
                                                            <i class="fas fa-user-edit me-2 text-white" style="color: #ffffff !important;"></i> Edit & Update Assigned Workers
                                                        @else
                                                            <i class="fas fa-users-cog me-2 text-white" style="color: #ffffff !important;"></i> Assign Multiple Employees to Lead
                                                        @endif
                                                    </h5>
                                                    <small class="text-white-50">Select or deselect employees to sync assignments, configure deadlines and instructions.</small>
                                                </div>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

                                                {{-- Project Timeline: Start Date & End Date --}}
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small text-muted mb-1.5">
                                                            Project Start Date <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="date" name="start_date" class="form-control" style="border-radius: 10px; padding: 10px 14px;"
                                                               value="{{ optional($lead->leadWorkers->first())->start_date ? $lead->leadWorkers->first()->start_date->format('Y-m-d') : date('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small text-muted mb-1.5">
                                                            Project End Date (Submission Deadline) <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="date" name="end_date" class="form-control" style="border-radius: 10px; padding: 10px 14px;"
                                                               value="{{ optional($lead->leadWorkers->first())->end_date ? $lead->leadWorkers->first()->end_date->format('Y-m-d') : date('Y-m-d', strtotime('+14 days')) }}" required>
                                                        <small class="text-muted d-block mt-1">Assigned workers must submit finished project by this date.</small>
                                                    </div>
                                                </div>

                                                {{-- Task Instructions --}}
                                                <div class="mb-2">
                                                    <label class="form-label fw-bold small text-muted mb-1.5">Project Instructions & Milestones</label>
                                                    <textarea name="instructions" class="form-control" rows="3" style="border-radius: 10px; padding: 10px 14px;"
                                                              placeholder="Provide milestones, deliverables, tech stack guidelines, or GitHub/Drive links...">{{ optional($lead->leadWorkers->first())->instructions }}</textarea>
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
                                                            Assign Workers & Set Deadlines
                                                        @endif
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50"></i>
                            <div class="fw-bold">No leads assigned to you yet</div>
                            <small>Leads assigned to you by the Sales team or Admin will appear here.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
