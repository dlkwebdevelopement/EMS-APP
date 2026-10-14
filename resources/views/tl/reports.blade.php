@extends('layouts.admin')

@section('title', 'Team Leader Project Reports')
@section('page-title', 'Team Leader Project Execution Reports')
@section('page-subtitle', 'Supervise timeline compliance, worker milestones, and deliverable submissions')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Stats Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Total Assigned Tasks</div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_tasks']) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Active In-Progress</div>
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
                <div class="text-muted small text-uppercase fw-bold">Completed & Verified</div>
                <h3 class="fw-bold text-success mb-0">{{ number_format($stats['completed']) }}</h3>
                @if($stats['overdue'] > 0)
                    <small class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> {{ $stats['overdue'] }} overdue</small>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail Table --}}
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-calendar-check text-success me-2"></i> Employee Project Worker Assignment Log
            </h6>
            <span class="text-muted small">{{ $workerTasks->total() }} Assignments</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Lead Code</th>
                        <th>Project / Lead Name</th>
                        <th>Assigned Worker</th>
                        <th>Start Date</th>
                        <th>End Date (Deadline)</th>
                        <th>Timeline Status</th>
                        <th>Execution Status</th>
                        <th>Submission Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workerTasks as $task)
                    <tr>
                        <td>
                            <a href="{{ route('tl.leads.show', $task->lead_id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ optional($task->lead)->lead_code }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ optional($task->lead)->name }}</div>
                            <small class="text-muted">{{ optional($task->lead)->domain ?? 'General' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ optional($task->worker)->name }}</div>
                            <small class="text-muted">{{ optional($task->worker)->designation }}</small>
                        </td>
                        <td>
                            <span class="small fw-semibold text-muted">
                                {{ optional($task->start_date)->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="small fw-bold text-dark">
                                {{ optional($task->end_date)->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            @if($task->status === 'completed')
                                <span class="badge bg-success text-white" style="font-size: 0.65rem;">Completed</span>
                            @elseif($task->is_overdue)
                                <span class="badge bg-danger text-white" style="font-size: 0.65rem;">Overdue</span>
                            @elseif($task->days_left !== null)
                                <span class="badge bg-info text-white" style="font-size: 0.65rem;">{{ $task->days_left }} days left</span>
                            @endif
                        </td>
                        <td>
                            @php $badge = $task->status_badge; @endphp
                            <span class="badge {{ $badge['class'] }}" style="font-size: 0.72rem;">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td>
                            @if($task->submitted_at)
                                <div class="small fw-bold text-dark">{{ $task->submitted_at->format('d M Y, h:i A') }}</div>
                                @if($task->end_date && $task->submitted_at->lte($task->end_date->endOfDay()))
                                    <span class="badge bg-success text-white" style="font-size: 0.6rem;">On-Time</span>
                                @else
                                    <span class="badge bg-danger text-white" style="font-size: 0.6rem;">Delayed</span>
                                @endif
                            @else
                                <span class="text-muted small">&mdash; Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No worker assignments recorded yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($workerTasks->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $workerTasks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
