{{-- resources/views/admin/time-sheets/show.blade.php --}}
<div class="modal-content" style="background: var(--glass); backdrop-filter: var(--blur); border-radius: var(--r-lg); border: 1.5px solid var(--glass-b);">
    <div class="modal-header" style="background: linear-gradient(135deg, #1a4a2a, #2d6a3f); color: white; border-radius: 28px 28px 0 0; border-bottom: none;">
        <h5 class="modal-title">
            <i class="fas fa-file-alt me-2"></i>Time Sheet Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="glass-panel p-3">
                    <label class="text-muted small text-uppercase mb-1">Employee</label>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <div class="user-avatar" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr($timeSheet->employee->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $timeSheet->employee->name ?? 'Unknown' }}</div>
                            <small class="text-muted">{{ $timeSheet->employee->employee_code ?? '' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-panel p-3">
                    <label class="text-muted small text-uppercase mb-1">Date</label>
                    <div class="fw-bold mt-1">{{ \Carbon\Carbon::parse($timeSheet->date)->format('l, d F Y') }}</div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($timeSheet->date)->diffForHumans() }}</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-panel p-3 text-center">
                    <i class="fas fa-play-circle" style="color: var(--gl); font-size: 1.5rem;"></i>
                    <label class="text-muted small text-uppercase d-block mt-1">Start Time</label>
                    <div class="fw-bold">{{ \Carbon\Carbon::parse($timeSheet->start_time)->format('h:i A') }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-panel p-3 text-center">
                    <i class="fas fa-stop-circle" style="color: #dc2626; font-size: 1.5rem;"></i>
                    <label class="text-muted small text-uppercase d-block mt-1">End Time</label>
                    <div class="fw-bold">{{ \Carbon\Carbon::parse($timeSheet->end_time)->format('h:i A') }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-panel p-3 text-center">
                    <i class="fas fa-coffee" style="color: #c9a84c; font-size: 1.5rem;"></i>
                    <label class="text-muted small text-uppercase d-block mt-1">Break Hours</label>
                    <div class="fw-bold">{{ $timeSheet->break_hours }} hours</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-panel p-3">
                    <label class="text-muted small text-uppercase mb-1">Total Hours</label>
                    <div class="fw-bold" style="color: var(--gl); font-size: 1.8rem;">{{ $timeSheet->total_hours }} hrs</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-panel p-3">
                    <label class="text-muted small text-uppercase mb-1">Status</label>
                    <div>
                        @if($timeSheet->status == 'pending')
                            <span class="badge badge-gold"><i class="fas fa-clock me-1"></i>Pending</span>
                        @elseif($timeSheet->status == 'approved')
                            <span class="badge badge-lime"><i class="fas fa-check me-1"></i>Approved</span>
                        @else
                            <span class="badge badge-red"><i class="fas fa-times me-1"></i>Rejected</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($timeSheet->project)
            <div class="col-12">
                <div class="glass-panel p-3">
                    <label class="text-muted small text-uppercase mb-1">Project</label>
                    <div><span class="badge badge-green">{{ $timeSheet->project }}</span></div>
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="glass-panel p-3">
                    <label class="text-muted small text-uppercase mb-1">Task Description</label>
                    <div class="mt-1">{{ nl2br(e($timeSheet->task_description)) }}</div>
                </div>
            </div>
            @if($timeSheet->remarks)
            <div class="col-12">
                <div class="glass-panel p-3" style="border-left: 3px solid #dc2626;">
                    <label class="text-muted small text-uppercase mb-1">Remarks / Rejection Reason</label>
                    <div class="mt-1 text-danger">{{ $timeSheet->remarks }}</div>
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="glass-panel p-3">
                    <label class="text-muted small text-uppercase mb-1">Submitted</label>
                    <div class="small">{{ \Carbon\Carbon::parse($timeSheet->created_at)->format('d M Y, h:i A') }}</div>
                    @if($timeSheet->updated_at != $timeSheet->created_at)
                        <small class="text-muted">Last updated: {{ \Carbon\Carbon::parse($timeSheet->updated_at)->diffForHumans() }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer" style="border-top: 1px solid rgba(143,214,122,.15);">
        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Close
        </button>
        @if($timeSheet->status == 'pending')
            <button type="button" class="btn btn-success" onclick="approveFromModal({{ $timeSheet->id }})">
                <i class="fas fa-check me-1"></i> Approve
            </button>
            <button type="button" class="btn btn-danger" onclick="rejectFromModal({{ $timeSheet->id }})">
                <i class="fas fa-times me-1"></i> Reject
            </button>
        @endif
    </div>
</div>

<script>
    function approveFromModal(id) {
        $('#viewModal').modal('hide');
        approveTimeSheet(id);
    }
    
    function rejectFromModal(id) {
        $('#viewModal').modal('hide');
        rejectTimeSheet(id);
    }
</script>