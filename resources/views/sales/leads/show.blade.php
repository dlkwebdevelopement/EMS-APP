@extends('layouts.admin')

@section('title', 'Lead Details — ' . $lead->lead_code)
@section('page-title', 'Lead Profile')
@section('page-subtitle', 'Detailed candidate information, Team Leader assignment & project workers')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #eaf6ed; color: #0d3b1a;">
            <i class="fas fa-check-circle me-2 text-success"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Top Action Header --}}
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 16px;">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                     style="width: 56px; height: 56px; font-size: 1.4rem; background: linear-gradient(135deg, #0d3b1a, #1e7c42);">
                    {{ strtoupper(substr($lead->name, 0, 2)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="fw-bold text-dark mb-0">{{ $lead->name }}</h4>
                        <span class="badge bg-light text-dark font-monospace border">{{ $lead->lead_code }}</span>
                        @php $badge = $lead->status_badge; @endphp
                        <span class="badge {{ $badge['class'] }}">
                            <i class="{{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
                        </span>
                    </div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-calendar-alt me-1 text-success"></i> Registered: {{ $lead->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('sales.leads.forward-tl', $lead) }}" class="btn btn-sm text-white fw-bold shadow-sm"
                   style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); border-radius: 10px; padding: 8px 16px;">
                    <i class="fas fa-crown me-1 text-warning"></i> Forward to TL
                </a>
                <a href="{{ route('sales.leads.edit', $lead) }}" class="btn btn-sm btn-outline-secondary fw-semibold" style="border-radius: 10px; padding: 8px 14px;">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <a href="{{ route('sales.leads.index') }}" class="btn btn-sm btn-light border text-muted" style="border-radius: 10px; padding: 8px 14px;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Pipeline
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT: Candidate Details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-id-card text-success"></i> Contact & Candidate Information
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted small ps-0" style="width: 140px;">Candidate Name</td>
                                    <td class="fw-bold text-dark">{{ $lead->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small ps-0">Mobile Number</td>
                                    <td>
                                        <a href="tel:{{ $lead->mobile }}" class="fw-bold text-success text-decoration-none">
                                            <i class="fas fa-phone-alt me-1"></i> {{ $lead->mobile }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small ps-0">Email Address</td>
                                    <td>
                                        <a href="mailto:{{ $lead->email }}" class="text-dark text-decoration-none">
                                            {{ $lead->email ?? 'Not provided' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small ps-0">College / Company</td>
                                    <td class="text-dark">{{ $lead->college_company ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small ps-0">Enquiry Domain</td>
                                    <td><span class="badge bg-light text-dark border">{{ $lead->domain ?? 'General' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted small ps-0">Lead Source</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success">{{ ucfirst($lead->source ?? 'Website') }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted small ps-0">Requirement</td>
                                    <td class="text-dark">{{ $lead->requirement ?? 'No specific details provided' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Team Leader & Worker Execution --}}
        <div class="col-lg-6">
            {{-- Team Leader Assignment Card --}}
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-crown text-warning"></i> Assigned Team Leader (TL)
                    </h6>
                    <a href="{{ route('sales.leads.forward-tl', $lead) }}" class="btn btn-sm btn-outline-warning text-dark fw-bold" style="border-radius: 8px; font-size: 0.75rem;">
                        <i class="fas fa-exchange-alt me-1"></i> Re-assign TL
                    </a>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($lead->teamLeader)
                        <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: #fffbeb; border-color: #fde68a !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-1.5 flex-wrap">
                                        <span>{{ $lead->teamLeader->name }}</span>
                                        @if($lead->teamLeader->employee_code)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace" style="font-size: 0.72rem;">
                                                {{ $lead->teamLeader->employee_code }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">{{ $lead->teamLeader->designation ?? 'Team Leader' }} &bull; {{ $lead->teamLeader->email }}</div>
                                </div>
                            </div>
                            <span class="badge bg-success text-white">Assigned</span>
                        </div>
                    @else
                        <div class="p-4 rounded-3 border text-center" style="background: #fef2f2; border-color: #fecaca !important;">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                            <div class="fw-bold text-dark">No Team Leader Assigned Yet</div>
                            <small class="text-muted d-block mb-3">Forward this lead to an active TL so they can assign employees and oversee project deliverables.</small>
                            <a href="{{ route('sales.leads.forward-tl', $lead) }}" class="btn btn-sm btn-danger fw-bold" style="border-radius: 8px;">
                                <i class="fas fa-crown me-1"></i> Forward to Team Leader Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assigned Project Workers --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-users-cog text-primary"></i> Team Leader Assigned Project Workers
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($lead->leadWorkers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Worker</th>
                                        <th>Role / Task</th>
                                        <th>Timeline</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lead->leadWorkers as $lw)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ optional($lw->worker)->name ?? 'Worker' }}</div>
                                            @if(optional($lw->worker)->employee_code)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace" style="font-size: 0.65rem;">
                                                    {{ $lw->worker->employee_code }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $lw->role_in_project ?? 'Developer' }}</td>
                                        <td class="small text-muted">
                                            <div>Start: {{ optional($lw->start_date)->format('d M') ?? 'N/A' }}</div>
                                            <div>End: {{ optional($lw->end_date)->format('d M') ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ ucfirst($lw->status ?? 'pending') }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-user-clock fa-2x mb-2 text-muted opacity-50"></i>
                            <div>Team Leader has not assigned multi-worker employees yet.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
