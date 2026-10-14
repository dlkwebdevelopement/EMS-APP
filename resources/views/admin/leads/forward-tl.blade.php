@extends('layouts.admin')

@section('title', 'Admin Forward Lead to Team Leader - ' . $lead->lead_code)
@section('page-title', 'Assign to Team Leader')
@section('page-subtitle', 'System Administrator oversight — assign lead to a Team Leader for project execution')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Breadcrumb & Back --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 10px;">
                <i class="fas fa-arrow-left me-1"></i> Back to Lead 360
            </a>
            <span class="badge p-2 fs-6 rounded-3 text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                <i class="fas fa-crown me-1 text-warning"></i> Lead: {{ $lead->lead_code }}
            </span>
            @php $badge = $lead->status_badge; @endphp
            <span class="badge {{ $badge['class'] }} fs-6 py-2 px-3">{{ $badge['label'] }}</span>
        </div>
        <div>
            <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 10px;">
                <i class="fas fa-list me-1"></i> All Leads
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- LEFT COLUMN: Candidate Dossier --}}
        <div class="col-lg-5">
            {{-- Candidate Contact Card --}}
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-user-graduate text-success me-2"></i> Candidate & Client Details
                    </h6>
                    <span class="badge bg-light text-dark border">{{ $lead->enquiry_type_label }}</span>
                </div>
                <div class="card-body p-4">
                    <h4 class="fw-bold text-dark mb-1">{{ $lead->name }}</h4>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <a href="tel:{{ $lead->mobile }}" class="btn btn-sm btn-outline-success" style="border-radius: 20px;">
                            <i class="fas fa-phone-alt me-1"></i> {{ $lead->mobile }}
                        </a>
                        @if($lead->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->whatsapp) }}" target="_blank" class="btn btn-sm btn-success text-white" style="border-radius: 20px;">
                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                            </a>
                        @endif
                    </div>

                    <div class="list-group list-group-flush small">
                        @if($lead->email)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-envelope me-2 text-primary"></i> Email</span>
                                <span class="fw-semibold text-dark">{{ $lead->email }}</span>
                            </div>
                        @endif
                        @if($lead->college_company)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-university me-2 text-warning"></i> Institution</span>
                                <span class="fw-semibold text-dark text-end">{{ $lead->college_company }}</span>
                            </div>
                        @endif
                        @if($lead->course_department)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-graduation-cap me-2 text-info"></i> Department</span>
                                <span class="fw-semibold text-dark">{{ $lead->course_department }}</span>
                            </div>
                        @endif
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-laptop-code me-2 text-success"></i> Domain</span>
                            <span class="badge bg-light text-dark border">{{ $lead->domain ?? 'General' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-concierge-bell me-2 text-secondary"></i> Service Needed</span>
                            <span class="fw-semibold text-dark">{{ $lead->service_needed ?? 'Standard Project Execution' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-rupee-sign me-2 text-success"></i> Deal Amount</span>
                            <span class="fw-bold text-success fs-6">₹{{ number_format($lead->deal_amount ?? 0, 2) }}</span>
                        </div>
                    </div>

                    @if($lead->requirement)
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <div class="fw-bold small text-muted mb-1"><i class="fas fa-clipboard-list me-1 text-primary"></i> Requirement Notes</div>
                            <div class="small text-dark" style="white-space: pre-line;">{{ $lead->requirement }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Current TL Status Card --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                             style="width: 48px; height: 48px; background: {{ $lead->teamLeader ? '#1e7c42' : '#f59e0b' }};">
                            <i class="fas {{ $lead->teamLeader ? 'fa-crown' : 'fa-hourglass-half' }} fs-5"></i>
                        </div>
                        <div>
                            <div class="small text-muted fw-bold text-uppercase">Current Assignment Status</div>
                            @if($lead->teamLeader)
                                <div class="fw-bold text-dark fs-6 d-flex align-items-center flex-wrap gap-1">
                                    <span>{{ $lead->teamLeader->name }}</span>
                                    @if($lead->teamLeader->employee_code)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace" style="font-size: 0.72rem;">
                                            <i class="fas fa-id-badge me-1"></i>{{ $lead->teamLeader->employee_code }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted small">Assigned on {{ optional($lead->assigned_to_tl_at)->format('d M Y, h:i A') }}</div>
                            @else
                                <div class="fw-bold text-warning fs-6">Unassigned &bull; Ready for TL Handover</div>
                                <div class="text-muted small">Select a Team Leader on the right to assign</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Forward to Team Leader Form --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <span class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                            <i class="fas fa-crown"></i>
                        </span>
                        Admin &mdash; Assign Lead to Team Leader (TL)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.leads.assign-tl', $lead) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        {{-- TEAM LEADER SELECTOR --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">
                                1. Select Team Leader <span class="text-danger">*</span>
                            </label>
                            <div class="small text-muted mb-3">
                                Choose the Team Leader to oversee project milestones, workers, and deliverables:
                            </div>

                            <div class="row g-3">
                                @forelse($teamLeaders as $tl)
                                    <div class="col-md-6">
                                        <label class="tl-choice-card w-100 h-100 p-3 border rounded-3 d-flex align-items-start gap-3 position-relative cursor-pointer transition-all"
                                               style="cursor: pointer; border-width: 2px !important; {{ $lead->tl_id == $tl->id ? 'border-color: #1e7c42 !important; background: #eaf6ed;' : 'border-color: #e2e8f0;' }}">
                                            <input type="radio" name="tl_id" value="{{ $tl->id }}" class="form-check-input mt-1 flex-shrink-0" 
                                                   {{ (old('tl_id', $lead->tl_id) == $tl->id || ($loop->first && !$lead->tl_id)) ? 'checked' : '' }} required>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-dark d-flex align-items-center justify-content-between flex-wrap gap-1">
                                                    <div>
                                                        <span>{{ $tl->name }}</span>
                                                        @if($tl->employee_code)
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace ms-1" style="font-size: 0.7rem;">
                                                                <i class="fas fa-id-badge me-1"></i>{{ $tl->employee_code }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="badge bg-white text-dark border" style="font-size: 0.7rem;">
                                                        <i class="fas fa-briefcase text-success me-1"></i> {{ $tl->assigned_leads_as_tl_count ?? 0 }} Active
                                                    </span>
                                                </div>
                                                <div class="small text-muted">{{ $tl->designation ?? 'Team Leader' }}</div>
                                                <div class="small text-muted" style="font-size: 0.75rem;">
                                                    <i class="fas fa-building me-1 text-muted"></i> {{ $tl->department ?? 'Engineering' }}
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning border-0">
                                            <i class="fas fa-exclamation-circle me-2"></i> No active Team Leaders found. Please promote an employee to Team Leader in Employee Directory.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- PRIORITY & DEAL VALUE --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">2. Priority Level <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="hot" {{ old('priority', $lead->priority) === 'hot' ? 'selected' : '' }}>🔥 Hot (High Priority / Urgent)</option>
                                    <option value="warm" {{ old('priority', $lead->priority) === 'warm' ? 'selected' : '' }}>☀️ Warm (Normal Schedule)</option>
                                    <option value="cold" {{ old('priority', $lead->priority) === 'cold' ? 'selected' : '' }}>❄️ Cold (Flexible Timeline)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">3. Deal Value (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">₹</span>
                                    <input type="number" step="0.01" name="deal_amount" class="form-control" value="{{ old('deal_amount', $lead->deal_amount) }}" placeholder="e.g. 15000">
                                </div>
                            </div>
                        </div>

                        {{-- HANDOVER INSTRUCTIONS --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                                <span>4. Project Scope & Execution Instructions</span>
                                <span class="text-muted small fw-normal">Optional</span>
                            </label>

                            {{-- Fast Chips --}}
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fast-chip" style="font-size: 0.75rem; border-radius: 14px;" data-insert="[Urgent Delivery]: Candidate has college review submission next week. ">
                                    + Urgent Review
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fast-chip" style="font-size: 0.75rem; border-radius: 14px;" data-insert="[IEEE Paper]: Requires complete implementation with documentation and PPT. ">
                                    + IEEE Standard
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fast-chip" style="font-size: 0.75rem; border-radius: 14px;" data-insert="[Multi-Worker]: Needs both frontend UI and backend Python/ML engineers. ">
                                    + Multi-Worker Needed
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fast-chip" style="font-size: 0.75rem; border-radius: 14px;" data-insert="[Hardware Kit]: Requires sensor components setup. ">
                                    + Hardware Setup
                                </button>
                            </div>

                            <textarea id="adminAssignmentNotes" name="assignment_notes" class="form-control" rows="4" 
                                      placeholder="Specify technical guidelines, milestones, deliverable expectations, or target deadlines..."></textarea>
                        </div>

                        {{-- SUBMIT BUTTON --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg text-white fw-bold shadow-sm" 
                                    style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); border-radius: 12px; padding: 14px;">
                                <i class="fas fa-check-circle me-2"></i> Confirm & Forward Lead to Team Leader
                            </button>
                            <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-light text-muted fw-semibold">
                                Cancel & Return to Lead 360
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Radio card visual selection
        const radioCards = document.querySelectorAll('.tl-choice-card');
        radioCards.forEach(card => {
            card.addEventListener('click', function() {
                radioCards.forEach(c => {
                    c.style.borderColor = '#e2e8f0';
                    c.style.background = '#ffffff';
                });
                this.style.borderColor = '#1e7c42';
                this.style.background = '#eaf6ed';
            });
        });

        // Fast insertion chips
        const textarea = document.getElementById('adminAssignmentNotes');
        document.querySelectorAll('.fast-chip').forEach(btn => {
            btn.addEventListener('click', function() {
                const text = this.getAttribute('data-insert');
                if (textarea.value) {
                    textarea.value += '\n' + text;
                } else {
                    textarea.value = text;
                }
                textarea.focus();
            });
        });
    });
</script>
@endpush
@endsection
