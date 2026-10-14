@extends('layouts.admin')

@section('title', 'Lead Execution - ' . $lead->lead_code)
@section('page-title', 'Lead Project Execution: ' . $lead->lead_code)
@section('page-subtitle', 'Supervise multi-employee workers, track start & end dates, and approve deliverables')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Top Action Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success p-2 fs-6 rounded-3">
                <i class="fas fa-crown text-white me-1"></i> TL Overseeing: {{ $lead->lead_code }}
            </span>
            @php $badge = $lead->status_badge; @endphp
            <span class="badge {{ $badge['class'] }} fs-6 py-2 px-3">{{ $badge['label'] }}</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#assignWorkersModal"
                    style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); color: #ffffff !important; border-radius: 8px; padding: 7px 16px; border: none;">
                @if($lead->leadWorkers->count() > 0)
                    <i class="fas fa-user-edit me-1 text-white" style="color: #ffffff !important;"></i>
                    <span class="text-white" style="color: #ffffff !important;">Edit / Manage Assigned Workers ({{ $lead->leadWorkers->count() }})</span>
                @else
                    <i class="fas fa-users-cog me-1 text-white" style="color: #ffffff !important;"></i>
                    <span class="text-white" style="color: #ffffff !important;">Assign Project Workers</span>
                @endif
            </button>
            <a href="{{ route('tl.leads.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                <i class="fas fa-arrow-left me-1"></i> Back to Leads
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT COLUMN: Client & Requirement Info --}}
        <div class="col-lg-4">
            {{-- Client Card --}}
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-circle text-success me-2"></i> Client Information</h6>
                </div>
                <div class="card-body p-3">
                    <h5 class="fw-bold text-dark mb-1">{{ $lead->name }}</h5>
                    <div class="text-muted small mb-2"><i class="fas fa-phone text-success me-1"></i> {{ $lead->mobile }}</div>
                    @if($lead->whatsapp)
                        <div class="text-muted small mb-2"><i class="fab fa-whatsapp text-success me-1"></i> {{ $lead->whatsapp }}</div>
                    @endif
                    @if($lead->email)
                        <div class="text-muted small mb-2"><i class="fas fa-envelope text-primary me-1"></i> {{ $lead->email }}</div>
                    @endif
                    @if($lead->college_company)
                        <div class="text-muted small mb-2"><i class="fas fa-university me-1 text-muted"></i> {{ $lead->college_company }}</div>
                    @endif
                    @if($lead->course_department)
                        <div class="text-muted small mb-0"><i class="fas fa-graduation-cap me-1 text-muted"></i> {{ $lead->course_department }}</div>
                    @endif
                </div>
            </div>

            {{-- Handover from Sales --}}
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-handshake text-primary me-2"></i> Sales Handover</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Sales Executive</span>
                        <span class="fw-bold small">{{ optional($lead->salesAgent)->name ?? 'Inbound / Website' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Assigned on</span>
                        <span class="small">{{ optional($lead->assigned_to_tl_at)->format('d M Y, h:i A') ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-0">
                        <span class="text-muted small">Deal Value</span>
                        <span class="fw-bold text-success">₹{{ number_format($lead->deal_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Requirement Details --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-clipboard-list text-info me-2"></i> Project Specifications</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-2">
                        <span class="text-muted small d-block">Technology Domain</span>
                        <span class="badge bg-light text-dark border">{{ $lead->domain ?? 'General' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Service Required</span>
                        <span class="fw-bold small">{{ $lead->service_needed ?? 'Standard Project Execution' }}</span>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Client Requirement Notes:</span>
                        <div class="p-2 bg-light rounded small text-dark mt-1">
                            {{ $lead->requirement ?: 'No detailed requirement specified.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Multi-Worker Project Timeline & Deliverables --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-users text-success me-2"></i> Assigned Project Workers & Submission Timelines
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignWorkersModal">
                        <i class="fas fa-plus-circle me-1"></i> Add / Modify Workers
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Assigned Worker</th>
                                    <th>Start Date</th>
                                    <th>End Date (Deadline)</th>
                                    <th>Status</th>
                                    <th>Submission Notes</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lead->leadWorkers as $workerTask)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark d-flex align-items-center flex-wrap gap-1">
                                            <span>{{ $workerTask->worker ? $workerTask->worker->name : 'Worker' }}</span>
                                            @if(optional($workerTask->worker)->employee_code)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace" style="font-size: 0.68rem;">
                                                    {{ $workerTask->worker->employee_code }}
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ optional($workerTask->worker)->designation }}</small>
                                    </td>
                                    <td>
                                        <span class="small fw-semibold text-muted">
                                            <i class="far fa-calendar-alt me-1 text-success"></i>
                                            {{ optional($workerTask->start_date)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">
                                            <i class="far fa-clock me-1 text-danger"></i>
                                            {{ optional($workerTask->end_date)->format('d M Y') }}
                                        </div>
                                        @if($workerTask->is_overdue)
                                            <span class="badge bg-danger text-white" style="font-size: 0.65rem;">Overdue</span>
                                        @elseif($workerTask->days_left !== null && !in_array($workerTask->status, ['completed', 'submitted']))
                                            <span class="badge bg-info text-white" style="font-size: 0.65rem;">{{ $workerTask->days_left }} days remaining</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $taskBadge = $workerTask->status_badge; @endphp
                                        <span class="badge {{ $taskBadge['class'] }}" style="font-size: 0.72rem;">
                                            <i class="{{ $taskBadge['icon'] }} me-1"></i> {{ $taskBadge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($workerTask->submission_notes)
                                            <div class="small text-dark" style="max-width: 200px;">
                                                {{ Str::limit($workerTask->submission_notes, 80) }}
                                            </div>
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                Submitted: {{ optional($workerTask->submitted_at)->format('d M, h:i A') }}
                                            </small>
                                        @else
                                            <span class="text-muted small">&mdash; Pending submission</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            {{-- Edit Worker Modal Trigger --}}
                                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editWorkerModal{{ $workerTask->id }}" 
                                                    title="Edit Worker Timeline & Status"
                                                    style="border-radius: 6px; font-size: 0.75rem;">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>

                                            @if($workerTask->status === 'submitted')
                                                <form action="{{ route('tl.workers.approve', $workerTask) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold py-1 px-2" style="font-size: 0.75rem; border-radius: 6px;" title="Approve Submission">
                                                        <i class="fas fa-check-circle me-1"></i> Approve
                                                    </button>
                                                </form>
                                            @elseif($workerTask->status === 'completed')
                                                <span class="badge bg-success text-white" style="font-size: 0.7rem; border-radius: 6px; padding: 5px 8px;">
                                                    <i class="fas fa-check-double me-1"></i> Done
                                                </span>
                                            @endif

                                            {{-- Unassign / Remove Worker Form --}}
                                            <form action="{{ route('tl.workers.destroy', $workerTask) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to unassign {{ addslashes($workerTask->worker ? $workerTask->worker->name : 'this worker') }} from this project?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" 
                                                        title="Unassign Worker"
                                                        style="border-radius: 6px; font-size: 0.75rem;">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-users-slash fa-2x mb-2 text-muted opacity-50"></i>
                                        <div>No employees assigned to work on this lead yet.</div>
                                        <button type="button" class="btn btn-sm btn-success mt-2 fw-bold" data-bs-toggle="modal" data-bs-target="#assignWorkersModal" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); color: #ffffff !important; border: none; border-radius: 8px;">
                                            <i class="fas fa-users-cog me-1 text-white" style="color: #ffffff !important;"></i> Assign Employees Now
                                        </button>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Activity & Follow-up History --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                <div class="card-header bg-transparent py-3 d-flex align-items-center gap-2">
                    <span class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); font-size: 0.8rem;">
                        <i class="fas fa-history"></i>
                    </span>
                    <h6 class="fw-bold mb-0 text-dark">Lead Activity & Timeline Notes</h6>
                </div>
                <div class="card-body p-4">
                    @forelse($lead->followups as $followup)
                        <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; flex-shrink: 0;">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-dark">{{ $followup->outcome ?? 'Followup' }}</strong>
                                    <small class="text-muted">{{ $followup->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                                <div class="small text-muted mt-1">{{ $followup->notes }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">
                            No followup notes logged yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- MULTI-WORKER ASSIGNMENT MODAL (BULK ASSIGN / EDIT / SYNC) --}}
    <div class="modal fade" id="assignWorkersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content text-start border-0 shadow" style="border-radius: 16px; overflow: hidden;">
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
                                <small class="text-muted d-block mt-1">Employees must submit their finished work on or before this deadline.</small>
                            </div>
                        </div>

                        {{-- Task Instructions --}}
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-muted mb-1.5">Project Instructions & Deliverables</label>
                            <textarea name="instructions" class="form-control" rows="3" style="border-radius: 10px; padding: 10px 14px;"
                                      placeholder="Provide technical guidelines, repo/drive links, and specific milestones...">{{ optional($lead->leadWorkers->first())->instructions }}</textarea>
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
                                    Confirm & Assign Workers
                                @endif
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EDIT INDIVIDUAL WORKER MODALS --}}
    @foreach($lead->leadWorkers as $workerTask)
        <div class="modal fade" id="editWorkerModal{{ $workerTask->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-start border-0 shadow" style="border-radius: 16px; overflow: hidden;">
                    <form action="{{ route('tl.workers.update', $workerTask) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                            <div>
                                <h5 class="modal-title fw-bold mb-0 text-white" style="color: #ffffff !important;">
                                    <i class="fas fa-user-edit me-2 text-white" style="color: #ffffff !important;"></i> Edit Worker Task
                                </h5>
                                <small class="text-white-50">{{ $workerTask->worker ? $workerTask->worker->name : 'Worker' }} @if(optional($workerTask->worker)->employee_code) &bull; {{ $workerTask->worker->employee_code }} @endif</small>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted mb-1">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" style="border-radius: 8px; padding: 9px 12px;"
                                           value="{{ optional($workerTask->start_date)->format('Y-m-d') ?: date('Y-m-d') }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted mb-1">Submission Deadline <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" style="border-radius: 8px; padding: 9px 12px;"
                                           value="{{ optional($workerTask->end_date)->format('Y-m-d') ?: date('Y-m-d', strtotime('+14 days')) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted mb-1">Task Execution Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" style="border-radius: 8px; padding: 9px 12px;" required>
                                    <option value="assigned" {{ $workerTask->status === 'assigned' ? 'selected' : '' }}>Assigned (Pending Start)</option>
                                    <option value="in_progress" {{ $workerTask->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="submitted" {{ $workerTask->status === 'submitted' ? 'selected' : '' }}>Submitted (In Review)</option>
                                    <option value="completed" {{ $workerTask->status === 'completed' ? 'selected' : '' }}>Completed (Approved)</option>
                                    <option value="overdue" {{ $workerTask->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold small text-muted mb-1">Specific Instructions & Milestones</label>
                                <textarea name="instructions" class="form-control" rows="3" style="border-radius: 8px; padding: 9px 12px;" placeholder="Specific instructions for this worker...">{{ $workerTask->instructions }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3 px-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px; padding: 8px 16px;" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn text-white fw-bold px-4 shadow-sm" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); color: #ffffff !important; border-radius: 8px; padding: 8px 20px; border: none;">
                                <i class="fas fa-save me-1 text-white" style="color: #ffffff !important;"></i>
                                <span class="text-white" style="color: #ffffff !important;">Update Worker Task</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection
