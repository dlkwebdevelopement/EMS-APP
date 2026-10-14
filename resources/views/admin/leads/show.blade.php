@extends('layouts.admin')

@section('title', 'Lead Profile - ' . $lead->lead_code)
@section('page-title', 'Lead 360° Profile')
@section('page-subtitle', 'Complete interaction history, source attribution & communication hub')

@section('content')
<div class="container-fluid px-0">

    {{-- ══════════════════════════════════════
       DUPLICATE LEAD ALERT (Positioned at top so it never overlaps or collides with actions)
    ══════════════════════════════════════ --}}
    @if($lead->is_duplicate)
        <div class="alert alert-warning d-flex align-items-center mb-3 py-3 px-4 border-0 shadow-sm"
             style="border-radius: var(--r-md); background: #fff8e6; border-left: 5px solid #f59e0b !important;">
            <i class="fas fa-exclamation-triangle fs-4 me-3 text-warning"></i>
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-2">
                <div>
                    <strong class="text-dark">Duplicate Lead Flagged:</strong>
                    <span class="text-muted ms-1">An enquiry with matching mobile number or email already exists in the system.</span>
                </div>
                @if($lead->duplicateOf)
                    <a href="{{ route('admin.leads.show', $lead->duplicateOf) }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm text-decoration-none">
                        <i class="fas fa-external-link-alt me-1"></i> View Original ({{ $lead->duplicateOf->lead_code }})
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════
       LEAD HEADER BANNER
    ══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md); overflow: visible; position: relative; z-index: 30;">
        <div class="card-body p-4" style="overflow: visible;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                         style="width: 58px; height: 58px; font-size: 1.4rem; background: linear-gradient(135deg, var(--gm), var(--gk)); flex-shrink: 0;">
                        {{ strtoupper(substr($lead->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="mb-0 fw-bold text-dark">{{ $lead->name }}</h3>
                            <span class="badge bg-light text-dark font-monospace">{{ $lead->lead_code }}</span>
                            @if($lead->priority === 'hot')
                                <span class="badge bg-danger text-white rounded-pill"><i class="fas fa-fire me-1"></i>Hot Lead</span>
                            @elseif($lead->priority === 'warm')
                                <span class="badge bg-warning text-dark rounded-pill"><i class="fas fa-sun me-1"></i>Warm</span>
                            @else
                                <span class="badge bg-info text-white rounded-pill"><i class="fas fa-snowflake me-1"></i>Cold</span>
                            @endif
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="fas fa-calendar-alt me-1"></i> Received on {{ $lead->created_at->format('d M Y, h:i A') }} ({{ $lead->created_at->diffForHumans() }})
                            @if($lead->assignedStaff)
                                &bull; <i class="fas fa-user-check me-1 text-success"></i> Handled by <strong>{{ $lead->assignedStaff->name }}</strong>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status Changer & Actions --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="btn-group position-relative">
                        <button type="button" class="btn btn-sm {{ $lead->status_badge['class'] }} dropdown-toggle shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="{{ $lead->status_badge['icon'] }} me-1"></i> Status: {{ $lead->status_badge['label'] }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 220px; z-index: 1060; border-radius: var(--r-sm);">
                            <li><h6 class="dropdown-header text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">Update Pipeline Status</h6></li>
                            @foreach(['new' => 'New Lead', 'contacted' => 'Contacted', 'interested' => 'Interested', 'follow_up' => 'Follow-up', 'not_interested' => 'Not Interested', 'lost' => 'Lost'] as $stKey => $stLabel)
                                <li>
                                    <form action="{{ route('admin.leads.status', $lead) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $stKey }}">
                                        <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ $lead->status === $stKey ? 'bg-light fw-bold text-success' : '' }}">
                                            <span>{{ $stLabel }}</span>
                                            @if($lead->status === $stKey) <i class="fas fa-check text-success ms-2"></i> @endif
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <button type="button" class="dropdown-item text-success fw-bold py-2" data-bs-toggle="modal" data-bs-target="#conversionModal">
                                    <i class="fas fa-trophy me-2"></i> Convert to Customer
                                </button>
                            </li>
                        </ul>
                    </div>

                    @if($lead->status !== 'converted')
                        <button type="button" class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#conversionModal">
                            <i class="fas fa-trophy me-1"></i> Convert to Customer
                        </button>
                    @endif

                    <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════
       COMMUNICATION BAR (QUICK WHATSAPP / CALL)
    ══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md); overflow: visible; position: relative; z-index: 20;">
        <div class="card-body p-3" style="overflow: visible;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small fw-bold text-uppercase me-2"><i class="fas fa-bolt text-warning me-1"></i> Quick Connect:</span>

                    {{-- Click to Call --}}
                    <a href="tel:{{ $lead->mobile }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                        <i class="fas fa-phone-alt me-1"></i> Call {{ $lead->mobile }}
                    </a>

                    {{-- WhatsApp Direct --}}
                    @php $cleanPhone = preg_replace('/[^0-9]/','', $lead->whatsapp ?: $lead->mobile); @endphp
                    <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                        <i class="fab fa-whatsapp me-1"></i> Open WhatsApp
                    </a>

                    {{-- Email --}}
                    @if($lead->email)
                        <a href="mailto:{{ $lead->email }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
                            <i class="fas fa-envelope me-1"></i> Send Email
                        </a>
                    @endif
                </div>

                {{-- WhatsApp Templates Dropdown --}}
                <div class="dropdown position-relative">
                    <button class="btn btn-sm btn-outline-success dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fab fa-whatsapp me-1"></i> Send WhatsApp Template
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 280px; z-index: 1060; border-radius: var(--r-sm);">
                        <li><h6 class="dropdown-header text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">Choose Pre-filled Message</h6></li>
                        @forelse($templates as $tmpl)
                            <li>
                                <a class="dropdown-item py-2" href="{{ $tmpl->whatsapp_url }}" target="_blank">
                                    <div class="fw-bold text-dark small"><i class="fas fa-comment-dots text-success me-1"></i> {{ $tmpl->title }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem; max-width: 260px;">{{ Str::limit($tmpl->rendered_body, 50) }}</div>
                                </a>
                            </li>
                        @empty
                            <li><span class="dropdown-item text-muted small">No active templates</span></li>
                        @endforelse
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a href="{{ route('admin.leads.settings') }}" class="dropdown-item small text-primary fw-bold">
                                <i class="fas fa-cog me-1"></i> Manage Templates
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" style="position: relative; z-index: 10;">

        {{-- ══════════════════════════════════════
           LEFT COLUMN: LEAD DETAILS & ATTRIBUTION
        ══════════════════════════════════════ --}}
        <div class="col-lg-5">

            {{-- Contact & Academic Profile --}}
            <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-user-circle text-white"></i></span>
                        <span class="fw-bold">Contact & Candidate Profile</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="text-muted ps-3 py-2" style="width: 140px;">Full Name</td>
                                <td class="fw-bold text-dark pe-3">{{ $lead->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3 py-2">Mobile Phone</td>
                                <td class="pe-3">
                                    <a href="tel:{{ $lead->mobile }}" class="text-dark fw-bold text-decoration-none">
                                        {{ $lead->mobile }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3 py-2">WhatsApp</td>
                                <td class="pe-3">
                                    <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="text-success fw-bold text-decoration-none">
                                        <i class="fab fa-whatsapp me-1"></i>{{ $lead->whatsapp ?: $lead->mobile }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3 py-2">Email Address</td>
                                <td class="pe-3">
                                    {{ $lead->email ?: 'Not provided' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3 py-2">College / Org</td>
                                <td class="fw-bold text-dark pe-3">{{ $lead->college_company ?: 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3 py-2">Course / Dept</td>
                                <td class="pe-3">{{ $lead->course_department ?: 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3 py-2">Enquiry Category</td>
                                <td class="pe-3"><span class="badge badge-lime">{{ $lead->enquiry_type_label }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3 py-2">Domain / Tech</td>
                                <td class="fw-bold text-success pe-3">{{ $lead->domain ?: 'General' }}</td>
                            </tr>
                            @if($lead->service_needed)
                                <tr>
                                    <td class="text-muted ps-3 py-2">Service Needed</td>
                                    <td class="pe-3">{{ $lead->service_needed }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Message & Requirement --}}
            <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex align-items-center gap-2">
                    <span class="header-icon"><i class="fas fa-comment-alt text-white"></i></span>
                    <span class="fw-bold">Visitor Requirement & Message</span>
                </div>
                <div class="card-body">
                    @if($lead->requirement)
                        <div class="mb-3">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Specific Requirement</span>
                            <div class="p-3 bg-light rounded" style="font-size: 0.88rem;">{{ $lead->requirement }}</div>
                        </div>
                    @endif
                    @if($lead->message)
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Enquiry Message</span>
                            <div class="p-3 bg-light rounded text-dark" style="font-size: 0.88rem;">{{ $lead->message }}</div>
                        </div>
                    @endif
                    @if(!$lead->requirement && !$lead->message)
                        <div class="text-muted small text-center py-2">No specific message attached with this enquiry.</div>
                    @endif
                </div>
            </div>

            {{-- Website Source Tracking & Attribution --}}
            <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex align-items-center gap-2">
                    <span class="header-icon"><i class="fas fa-satellite-dish text-white"></i></span>
                    <span class="fw-bold">Website Attribution & UTM Tracking</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="text-muted ps-3 py-2" style="width: 140px;">Traffic Source</td>
                                <td class="pe-3"><span class="badge badge-green">{{ $lead->source_label }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3 py-2">Submitted Page</td>
                                <td class="pe-3 font-monospace small text-truncate" style="max-width: 180px;">{{ $lead->source_page ?: '/' }}</td>
                            </tr>
                            @if($lead->landing_page)
                                <tr>
                                    <td class="text-muted ps-3 py-2">Landing Page</td>
                                    <td class="pe-3 font-monospace small text-truncate">{{ $lead->landing_page }}</td>
                                </tr>
                            @endif
                            @if($lead->campaign_name)
                                <tr>
                                    <td class="text-muted ps-3 py-2">Campaign</td>
                                    <td class="pe-3 fw-bold text-dark">{{ $lead->campaign_name }}</td>
                                </tr>
                            @endif
                            @if($lead->utm_source)
                                <tr>
                                    <td class="text-muted ps-3 py-2">UTM Source / Medium</td>
                                    <td class="pe-3 font-monospace small">{{ $lead->utm_source }} / {{ $lead->utm_medium ?: '-' }}</td>
                                </tr>
                            @endif
                            @if($lead->utm_campaign)
                                <tr>
                                    <td class="text-muted ps-3 py-2">UTM Campaign</td>
                                    <td class="pe-3 font-monospace small">{{ $lead->utm_campaign }}</td>
                                </tr>
                            @endif
                            @if($lead->ip_address)
                                <tr>
                                    <td class="text-muted ps-3 py-2">Visitor IP</td>
                                    <td class="pe-3 font-monospace small text-muted">{{ $lead->ip_address }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Staff Assignment Card --}}
            {{-- Team Leader (TL) Assignment Card --}}
            <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon bg-success"><i class="fas fa-crown text-white"></i></span>
                        <span class="fw-bold">Team Leader (TL) Assignment</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($lead->teamLeader)
                        <div class="p-2 mb-3 bg-light rounded border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-success text-white rounded-circle"><i class="fas fa-crown"></i></div>
                                <div>
                                    <div class="fw-bold text-dark small d-flex align-items-center flex-wrap gap-1">
                                        <span>{{ $lead->teamLeader->name }}</span>
                                        @if($lead->teamLeader->employee_code)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace" style="font-size: 0.68rem;">
                                                {{ $lead->teamLeader->employee_code }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Assigned on {{ optional($lead->assigned_to_tl_at)->format('d M Y') ?? 'Recently' }}</div>
                                </div>
                            </div>
                            <span class="badge bg-success text-white">Active TL</span>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="fas fa-exclamation-circle me-1"></i> No Team Leader assigned yet.
                        </div>
                    @endif

                    <form action="{{ route('admin.leads.assign-tl', $lead) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Select Team Leader</label>
                            <select name="tl_id" class="form-select form-select-sm" required>
                                <option value="">-- Choose Team Leader --</option>
                                @foreach($teamLeaders as $tl)
                                    <option value="{{ $tl->id }}" {{ $lead->tl_id == $tl->id ? 'selected' : '' }}>
                                        ⭐ [{{ $tl->employee_code }}] {{ $tl->name }} ({{ $tl->department }} &bull; {{ $tl->designation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm text-white w-100 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                            <i class="fas fa-crown me-1 text-warning"></i> {{ $lead->teamLeader ? 'Change Team Leader' : 'Assign to Team Leader' }}
                        </button>
                        <a href="{{ route('admin.leads.forward-tl', $lead) }}" class="btn btn-sm btn-outline-secondary w-100 fw-semibold mt-2">
                            <i class="fas fa-external-link-alt me-1"></i> Open Full Forward Screen
                        </a>
                    </form>
                </div>
            </div>

            {{-- Multi-Worker Project Execution Timeline Card --}}
            <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon bg-primary"><i class="fas fa-users-cog text-white"></i></span>
                        <span class="fw-bold">Project Execution Workers</span>
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adminAssignWorkersModal">
                        @if($lead->leadWorkers->count() > 0)
                            <i class="fas fa-user-edit me-1"></i> Edit Workers ({{ $lead->leadWorkers->count() }})
                        @else
                            <i class="fas fa-user-plus me-1"></i> Assign Workers
                        @endif
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($lead->leadWorkers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($lead->leadWorkers as $lw)
                                @php $lwBadge = $lw->status_badge; @endphp
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="fw-bold text-dark small d-flex align-items-center flex-wrap gap-1">
                                            <span>{{ $lw->worker ? $lw->worker->name : 'Worker' }}</span>
                                            @if(optional($lw->worker)->employee_code)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace" style="font-size: 0.68rem;">
                                                    {{ $lw->worker->employee_code }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="badge {{ $lwBadge['class'] }}" style="font-size: 0.65rem;">{{ $lwBadge['label'] }}</span>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <i class="far fa-calendar-alt text-success me-1"></i> {{ optional($lw->start_date)->format('d M') }} &rarr; {{ optional($lw->end_date)->format('d M Y') }}
                                        @if($lw->days_left !== null && !in_array($lw->status, ['completed', 'submitted']))
                                            <span class="text-danger fw-bold ms-1">({{ $lw->days_left }}d left)</span>
                                        @endif
                                    </div>
                                    @if($lw->submission_notes)
                                        <div class="small text-dark p-2 bg-light rounded mt-1">
                                            <strong>Submission:</strong> {{ Str::limit($lw->submission_notes, 80) }}
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-end gap-1 mt-2 pt-2 border-top">
                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.72rem; border-radius: 4px;" data-bs-toggle="modal" data-bs-target="#adminEditWorkerModal{{ $lw->id }}" title="Edit Worker Assignment">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.workers.destroy', $lw) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to unassign this worker from this lead?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size: 0.72rem; border-radius: 4px;" title="Unassign Worker">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-3 text-center text-muted small">
                            No project workers assigned yet.
                            <div class="mt-2">
                                <button type="button" class="btn btn-xs btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#adminAssignWorkersModal">
                                    <i class="fas fa-plus me-1"></i> Assign Workers Now
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Financial Deal & Conversion Info --}}
            @if($lead->status === 'converted' || $lead->deal_amount > 0)
                <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md);">
                    <div class="card-header bg-transparent d-flex align-items-center gap-2">
                        <span class="header-icon bg-success"><i class="fas fa-receipt text-white"></i></span>
                        <span class="fw-bold">Customer Conversion & Deal</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Registration Type</span>
                            <span class="fw-bold">{{ ucwords(str_replace('_', ' ', $lead->conversion_type ?: 'Training')) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Deal Amount</span>
                            <span class="fw-bold text-dark">₹{{ number_format($lead->deal_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Paid Amount</span>
                            <span class="fw-bold text-success">₹{{ number_format($lead->paid_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Balance Payment Due</span>
                            <span class="fw-bold text-danger">₹{{ number_format($lead->balance_amount, 2) }}</span>
                        </div>
                        <div class="p-2 bg-light rounded text-center">
                            <span class="text-muted small">Payment Status:</span>
                            <strong class="text-uppercase ms-1 text-dark">{{ str_replace('_', ' ', $lead->payment_status) }}</strong>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ══════════════════════════════════════
           RIGHT COLUMN: FOLLOW-UP TIMELINE & SCHEDULER
        ══════════════════════════════════════ --}}
        <div class="col-lg-7">

            {{-- Log Follow-up / Add Note Form --}}
            <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-phone-volume text-white"></i></span>
                        <span class="fw-bold">Record Interaction & Schedule Follow-up</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('admin.leads.followups.add', $lead) }}" method="POST">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold">Channel</label>
                                <select name="followup_type" class="form-select form-select-sm" required>
                                    <option value="call">📞 Phone Call</option>
                                    <option value="whatsapp">💬 WhatsApp Message</option>
                                    <option value="email">✉️ Email</option>
                                    <option value="meeting">🤝 In-Person / Demo</option>
                                    <option value="note">📝 Internal Note</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold">Call Outcome</label>
                                <select name="outcome" class="form-select form-select-sm">
                                    <option value="Interested - Course Details Sent">Interested - Course Details Sent</option>
                                    <option value="Call Back Later">Call Back Later</option>
                                    <option value="Not Reachable / Busy">Not Reachable / Busy</option>
                                    <option value="Switched Off">Switched Off</option>
                                    <option value="Demo Scheduled">Demo Scheduled</option>
                                    <option value="Fees Discussed">Fees Discussed</option>
                                    <option value="Wrong Number">Wrong Number</option>
                                    <option value="Not Interested">Not Interested</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold">Update Lead Status</label>
                                <select name="update_status" class="form-select form-select-sm">
                                    <option value="">(Keep current status)</option>
                                    <option value="contacted">Mark as Contacted</option>
                                    <option value="interested">Mark as Interested</option>
                                    <option value="follow_up">Mark as Follow-up</option>
                                    <option value="not_interested">Not Interested</option>
                                    <option value="lost">Mark as Lost</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small text-muted fw-bold">Interaction Summary / Notes</label>
                            <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Summarize the discussion, questions asked, fee discussion, candidate response..." required></textarea>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="form-label small text-muted fw-bold mb-0 text-nowrap"><i class="fas fa-clock text-warning me-1"></i> Next Follow-up:</label>
                                    <input type="datetime-local" name="next_followup_date" class="form-control form-control-sm" value="{{ $lead->next_followup_date ? $lead->next_followup_date->format('Y-m-d\TH:i') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-5 text-end">
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm w-100">
                                    <i class="fas fa-save me-1"></i> Save Follow-up
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Follow-up & Activity Timeline --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-history text-white"></i></span>
                        <span class="fw-bold">Interaction & Follow-up History</span>
                    </div>
                    <span class="badge bg-light text-muted">{{ $lead->followups->count() }} activities</span>
                </div>
                <div class="card-body p-4">
                    @if($lead->followups->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-clipboard-list fs-2 text-secondary mb-2"></i>
                            <p class="mb-0">No follow-ups recorded yet. Record the first interaction above!</p>
                        </div>
                    @else
                        <div class="timeline position-relative ps-4" style="border-left: 2px solid rgba(13, 59, 26, 0.1);">
                            @foreach($lead->followups as $act)
                                <div class="timeline-item position-relative mb-4">
                                    {{-- Timeline bullet icon --}}
                                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white {{ $act->type_badge['class'] }}"
                                         style="width: 28px; height: 28px; left: -30px; top: 0; font-size: 0.75rem;">
                                        <i class="{{ $act->type_badge['icon'] }}"></i>
                                    </div>

                                    <div class="card border-0 shadow-none bg-light p-3 rounded" style="border-left: 3px solid var(--gm) !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div>
                                                <span class="badge {{ $act->type_badge['class'] }} rounded-pill me-1">{{ $act->type_badge['label'] }}</span>
                                                <strong class="text-dark small">{{ $act->outcome }}</strong>
                                            </div>
                                            <span class="text-muted" style="font-size: 0.72rem;">
                                                <i class="fas fa-clock me-1"></i>{{ $act->created_at->format('d M Y, h:i A') }}
                                            </span>
                                        </div>

                                        <p class="mb-1 text-secondary" style="font-size: 0.85rem; line-height: 1.4;">{{ $act->notes }}</p>

                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top" style="font-size: 0.72rem;">
                                            <span class="text-muted">
                                                Logged by: <strong>{{ $act->staff ? $act->staff->name : 'System Admin' }}</strong>
                                            </span>
                                            @if($act->scheduled_at)
                                                <span class="text-muted">
                                                    Scheduled: {{ $act->scheduled_at->format('d M, h:i A') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

{{-- ══════════════════════════════════════
   CUSTOMER CONVERSION MODAL
══════════════════════════════════════ --}}
<div class="modal fade" id="conversionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-trophy me-2"></i> Convert Lead to Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.leads.convert', $lead) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Congratulations on closing this enquiry! Record the registration details and payment tracking below.</p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Registration / Service Type</label>
                        <select name="conversion_type" class="form-select form-select-sm" required>
                            <option value="training_registration">🎓 Training Registration</option>
                            <option value="internship_registration">💼 Internship Registration</option>
                            <option value="project_registration">🔬 Academic Project Registration</option>
                            <option value="service_customer">💻 Software / Web Service Client</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Total Deal Amount (₹)</label>
                            <input type="number" step="0.01" name="deal_amount" id="modal_deal_amount" 
                                   class="form-control form-control-sm" value="{{ $lead->deal_amount ?: '12000' }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Paid Amount (₹)</label>
                            <input type="number" step="0.01" name="paid_amount" id="modal_paid_amount" 
                                   class="form-control form-control-sm" value="{{ $lead->paid_amount ?: '2000' }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Payment Status</label>
                        <select name="payment_status" id="modal_payment_status" class="form-select form-select-sm" required>
                            <option value="advance_paid">Advance Paid (Part Payment)</option>
                            <option value="full_paid">Full Paid (100% Cleared)</option>
                            <option value="balance_payment">Balance Payment Due</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Batch / Project ID / Notes</label>
                        <textarea name="conversion_notes" rows="2" class="form-control form-control-sm" placeholder="e.g. Batch starts 15th March, Project Title approved, Student Portal activated..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Confirm Conversion
                    </button>
                </div>
            </form>
        </div>
</div>

{{-- ══════════════════════════════════════
   ADMIN ASSIGN MULTI-WORKERS MODAL
══════════════════════════════════════ --}}
<div class="modal fade" id="adminAssignWorkersModal" tabindex="-1" aria-labelledby="adminAssignWorkersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--r-md); overflow: hidden;">
            <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                <div>
                    <h5 class="modal-title fw-bold fs-6 text-white mb-0" id="adminAssignWorkersModalLabel" style="color: #ffffff !important;">
                        @if($lead->leadWorkers->count() > 0)
                            <i class="fas fa-user-edit me-2 text-white" style="color: #ffffff !important;"></i> Edit & Update Assigned Workers
                        @else
                            <i class="fas fa-users-cog me-2 text-white" style="color: #ffffff !important;"></i> Assign Multiple Project Workers & Timeline
                        @endif
                    </h5>
                    <small class="text-white-50">Select or deselect employees to sync assignments, configure deadlines and instructions.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.leads.assign-workers', $lead) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-light border mb-4" style="border-radius: 8px;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-briefcase text-success fs-5"></i>
                            <div>
                                <strong class="text-dark">{{ $lead->name }}</strong>
                                <span class="text-muted ms-2">&bull; {{ $lead->domain ?? 'General' }} &bull; {{ $lead->service_needed ?? $lead->enquiry_type_label }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark d-flex justify-content-between">
                            <span><i class="fas fa-user-friends text-success me-1"></i> Select Employees / Workers (Multi-select)</span>
                            <span class="text-muted fw-normal" style="font-size: 0.75rem;">Check/uncheck employees to sync this project</span>
                        </label>
                        <div class="row g-2 p-3 bg-light rounded border" style="max-height: 220px; overflow-y: auto;">
                            @foreach($employees as $emp)
                                <div class="col-md-6">
                                    <div class="form-check p-2 bg-white rounded border-sm shadow-none">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="employee_ids[]" 
                                               value="{{ $emp->id }}" id="admin_emp_{{ $emp->id }}"
                                               {{ $lead->leadWorkers->pluck('employee_id')->contains($emp->id) ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold text-dark w-100 cursor-pointer" for="admin_emp_{{ $emp->id }}">
                                            {{ $emp->name }}
                                            @if($emp->employee_code)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace ms-1" style="font-size: 0.68rem;">
                                                    {{ $emp->employee_code }}
                                                </span>
                                            @endif
                                            <span class="d-block text-muted fw-normal" style="font-size: 0.72rem;">
                                                {{ $emp->department ?? 'General' }} &bull; {{ $emp->designation ?? 'Staff' }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">
                                <i class="fas fa-play-circle text-primary me-1"></i> Project Start Date
                            </label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="{{ optional($lead->leadWorkers->first())->start_date ? $lead->leadWorkers->first()->start_date->format('Y-m-d') : date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">
                                <i class="fas fa-flag-checkered text-danger me-1"></i> Project Deadline / End Date
                            </label>
                            <input type="date" name="end_date" class="form-control" 
                                   value="{{ optional($lead->leadWorkers->first())->end_date ? $lead->leadWorkers->first()->end_date->format('Y-m-d') : date('Y-m-d', strtotime('+14 days')) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="fas fa-tasks text-muted me-1"></i> Assignment Instructions & Scope of Work
                        </label>
                        <textarea name="instructions" rows="3" class="form-control" 
                                  placeholder="Specify milestones, requirements, submission checklist, and deliverables for the assigned workers...">{{ optional($lead->leadWorkers->first())->instructions }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm text-white px-4 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); color: #ffffff !important; border-radius: 8px; border: none; padding: 8px 20px;">
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

{{-- ══════════════════════════════════════
   ADMIN EDIT INDIVIDUAL WORKER MODALS
══════════════════════════════════════ --}}
@foreach($lead->leadWorkers as $lw)
<div class="modal fade" id="adminEditWorkerModal{{ $lw->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
            <form action="{{ route('admin.workers.update', $lw) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" style="color: #ffffff !important;">
                            <i class="fas fa-user-edit me-2 text-white" style="color: #ffffff !important;"></i> Edit Worker Task (Admin)
                        </h5>
                        <small class="text-white-50">{{ $lw->worker ? $lw->worker->name : 'Worker' }} @if(optional($lw->worker)->employee_code) &bull; {{ $lw->worker->employee_code }} @endif</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted mb-1">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" style="border-radius: 8px; padding: 9px 12px;"
                                   value="{{ optional($lw->start_date)->format('Y-m-d') ?: date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted mb-1">Submission Deadline <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" style="border-radius: 8px; padding: 9px 12px;"
                                   value="{{ optional($lw->end_date)->format('Y-m-d') ?: date('Y-m-d', strtotime('+14 days')) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted mb-1">Task Execution Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" style="border-radius: 8px; padding: 9px 12px;" required>
                            <option value="assigned" {{ $lw->status === 'assigned' ? 'selected' : '' }}>Assigned (Pending Start)</option>
                            <option value="in_progress" {{ $lw->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="submitted" {{ $lw->status === 'submitted' ? 'selected' : '' }}>Submitted (In Review)</option>
                            <option value="completed" {{ $lw->status === 'completed' ? 'selected' : '' }}>Completed (Approved)</option>
                            <option value="overdue" {{ $lw->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted mb-1">Specific Instructions & Milestones</label>
                        <textarea name="instructions" class="form-control" rows="3" style="border-radius: 8px; padding: 9px 12px;" placeholder="Specific instructions for this worker...">{{ $lw->instructions }}</textarea>
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
@endsection
