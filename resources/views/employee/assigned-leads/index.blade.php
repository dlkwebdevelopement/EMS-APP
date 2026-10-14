@extends('layouts.admin')

@section('title', 'My Assigned Projects & Tasks')
@section('page-title', 'Assigned Projects & Leads')
@section('page-subtitle', 'Projects assigned to you by your Team Leader — Track deadlines and submit deliverables')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Assigned Projects</div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">In Progress</div>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($stats['in_progress']) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Submitted / Under Review</div>
                <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['submitted']) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Completed & Approved</div>
                <h3 class="fw-bold text-success mb-0">{{ number_format($stats['completed']) }}</h3>
                @if($stats['overdue'] > 0)
                    <small class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> {{ $stats['overdue'] }} deadline passed</small>
                @endif
            </div>
        </div>
    </div>

    {{-- Projects Grid --}}
    <div class="row g-4">
        @forelse($assignments as $assignment)
            @php 
                $lead = $assignment->lead;
                $taskBadge = $assignment->status_badge;
            @endphp
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm bg-white h-100 position-relative" style="border-radius: 14px; overflow: hidden;">
                    {{-- Top colored accent strip based on status --}}
                    <div style="height: 4px; background: {{ $assignment->is_overdue ? '#dc2626' : ($assignment->status === 'completed' ? '#10b981' : '#2563eb') }};"></div>
                    
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-light text-dark border fw-bold">{{ $lead->lead_code }}</span>
                                <span class="badge {{ $taskBadge['class'] }}">
                                    <i class="{{ $taskBadge['icon'] }} me-1"></i> {{ $taskBadge['label'] }}
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-1">{{ $lead->name }}</h5>
                            <div class="text-muted small mb-3">
                                <span class="badge bg-light text-dark border me-1">{{ $lead->domain ?? 'General' }}</span>
                                <span>{{ $lead->service_needed ?? 'Project Execution' }}</span>
                            </div>

                            @if($lead->requirement)
                                <div class="p-3 bg-light rounded-3 small text-dark mb-3 border">
                                    <div class="text-muted fw-bold mb-1" style="font-size: 0.72rem;">CLIENT SPECIFICATIONS:</div>
                                    {{ Str::limit($lead->requirement, 160) }}
                                </div>
                            @endif

                            @if($assignment->instructions)
                                <div class="p-3 bg-light rounded-3 small text-dark mb-3 border border-primary border-opacity-25">
                                    <div class="text-primary fw-bold mb-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-crown me-1"></i> TEAM LEADER INSTRUCTIONS:
                                    </div>
                                    {{ $assignment->instructions }}
                                </div>
                            @endif

                            {{-- Project Timeline (Start Date & End Date) --}}
                            <div class="p-3 bg-light rounded-3 mb-3 border">
                                <div class="row g-2 text-center">
                                    <div class="col-6 border-end">
                                        <div class="text-muted" style="font-size: 0.72rem;">START DATE</div>
                                        <div class="fw-bold text-dark small">
                                            <i class="far fa-calendar-alt text-success me-1"></i>
                                            {{ optional($assignment->start_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted" style="font-size: 0.72rem;">SUBMIT DEADLINE (END DATE)</div>
                                        <div class="fw-bold text-danger small">
                                            <i class="far fa-clock me-1"></i>
                                            {{ optional($assignment->end_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-2 pt-2 border-top">
                                    @if($assignment->status === 'completed')
                                        <span class="badge bg-success text-white">
                                            <i class="fas fa-check-circle me-1"></i> Completed & Verified
                                        </span>
                                    @elseif($assignment->status === 'submitted')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-file-export me-1"></i> Submitted on {{ optional($assignment->submitted_at)->format('d M Y') }} &bull; Awaiting TL Approval
                                        </span>
                                    @elseif($assignment->is_overdue)
                                        <span class="badge bg-danger text-white">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Overdue Submission Deadline!
                                        </span>
                                    @elseif($assignment->days_left !== null)
                                        <span class="badge bg-info text-white">
                                            <i class="fas fa-hourglass-half me-1"></i> {{ $assignment->days_left }} Days Remaining to Submit
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($assignment->submission_notes)
                                <div class="alert alert-secondary py-2 px-3 small mb-3">
                                    <div class="fw-bold mb-1">Your Submission Deliverable Notes:</div>
                                    {{ $assignment->submission_notes }}
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex gap-2 pt-3 border-top mt-2">
                            @if($assignment->status === 'assigned')
                                <form action="{{ route('employee.assigned-leads.start', $assignment) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">
                                        <i class="fas fa-play me-1"></i> Start Working
                                    </button>
                                </form>
                            @endif

                            @if(in_array($assignment->status, ['assigned', 'in_progress', 'overdue']))
                                <button type="button" class="btn btn-sm btn-success fw-bold flex-grow-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#submitProjectModal{{ $assignment->id }}">
                                    <i class="fas fa-file-upload me-1"></i> Submit Finished Project
                                </button>
                            @elseif($assignment->status === 'submitted')
                                <button type="button" class="btn btn-sm btn-outline-warning w-100" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#submitProjectModal{{ $assignment->id }}">
                                    <i class="fas fa-edit me-1"></i> Update Deliverable Notes
                                </button>
                            @else
                                <div class="w-100 text-center text-success fw-bold small py-1">
                                    <i class="fas fa-check-double me-1"></i> Project Finalized & Complete
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SUBMIT PROJECT MODAL --}}
                <div class="modal fade" id="submitProjectModal{{ $assignment->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-start border-0 shadow">
                            <form action="{{ route('employee.assigned-leads.submit', $assignment) }}" method="POST">
                                @csrf
                                <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                                    <h5 class="modal-title fw-bold">
                                        <i class="fas fa-file-upload me-2"></i> Submit Completed Project
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="alert alert-light border mb-3">
                                        <div class="fw-bold text-dark">{{ $lead->lead_code }} &mdash; {{ $lead->name }}</div>
                                        <div class="small text-muted">Submission Deadline: {{ optional($assignment->end_date)->format('d M Y') }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">
                                            Completion Deliverables & Work Summary <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="submission_notes" class="form-control" rows="4" required 
                                                  placeholder="Provide GitHub repo links, drive download URLs, deliverables summary, or notes for your Team Leader...">{{ old('submission_notes', $assignment->submission_notes) }}</textarea>
                                        <small class="text-muted">Your Team Leader will review this submission before final sign-off.</small>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success btn-sm fw-bold">
                                        <i class="fas fa-paper-plane me-1"></i> Submit to Team Leader
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fas fa-laptop-code fa-3x mb-3 text-muted opacity-50"></i>
                <h5 class="fw-bold">No projects currently assigned to you</h5>
                <p class="small">When your Team Leader assigns leads or projects to you with start & end dates, they will appear here.</p>
            </div>
        @endforelse
    </div>

    @if($assignments->hasPages())
        <div class="mt-4">
            {{ $assignments->links() }}
        </div>
    @endif

</div>
@endsection
