@extends('layouts.admin')

@section('title', 'Sales Leads Pipeline')
@section('page-title', 'Sales Leads Management')
@section('page-subtitle', 'Manage candidate leads, review status, and forward to Team Leaders')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════
       FILTER PILLS & ACTIONS
    ══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 14px;">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('sales.leads.index') }}" 
                   class="btn btn-sm {{ !request('tl_assignment') && !request('status') ? 'btn-success text-white' : 'btn-outline-secondary' }}" 
                   style="border-radius: 20px;">
                    All Leads <span class="badge bg-white text-dark ms-1">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('sales.leads.index', ['tl_assignment' => 'assigned']) }}" 
                   class="btn btn-sm {{ request('tl_assignment') === 'assigned' ? 'btn-primary text-white' : 'btn-outline-secondary' }}" 
                   style="border-radius: 20px;">
                    <i class="fas fa-crown me-1 text-warning"></i> Assigned to TL <span class="badge bg-white text-dark ms-1">{{ $counts['assigned_tl'] }}</span>
                </a>
                <a href="{{ route('sales.leads.index', ['tl_assignment' => 'unassigned']) }}" 
                   class="btn btn-sm {{ request('tl_assignment') === 'unassigned' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}" 
                   style="border-radius: 20px;">
                    <i class="fas fa-clock me-1"></i> Pending TL Assign <span class="badge bg-white text-dark ms-1">{{ $counts['unassigned_tl'] }}</span>
                </a>
                <a href="{{ route('sales.leads.index', ['status' => 'converted']) }}" 
                   class="btn btn-sm {{ request('status') === 'converted' ? 'btn-success text-white' : 'btn-outline-secondary' }}" 
                   style="border-radius: 20px;">
                    <i class="fas fa-check-circle me-1"></i> Converted <span class="badge bg-white text-dark ms-1">{{ $counts['converted'] }}</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('sales.leads.create') }}" class="btn btn-sm text-white fw-bold shadow-sm" 
                   style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); border-radius: 10px; padding: 8px 16px;">
                    <i class="fas fa-plus-circle me-1"></i> + New Lead
                </a>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('sales.leads.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by candidate name, phone, code..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="tl_id" class="form-select form-select-sm">
                        <option value="">Filter by Team Leader (All)</option>
                        @foreach($teamLeaders as $tl)
                            <option value="{{ $tl->id }}" {{ request('tl_id') == $tl->id ? 'selected' : '' }}>{{ $tl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Filter by Status (All)</option>
                        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="interested" {{ request('status') === 'interested' ? 'selected' : '' }}>Interested</option>
                        <option value="follow_up" {{ request('status') === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-success w-100 fw-bold">Filter</button>
                    @if(request()->hasAny(['search', 'tl_id', 'status', 'tl_assignment']))
                        <a href="{{ route('sales.leads.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="fas fa-undo"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- LEADS TABLE CARD --}}
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="py-3 px-3 text-uppercase fw-bold text-muted" style="font-size: 0.68rem; letter-spacing: 0.5px; width: 28%;">Candidate & Lead</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-muted" style="font-size: 0.68rem; letter-spacing: 0.5px; width: 18%;">Domain & Enquiry</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-muted" style="font-size: 0.68rem; letter-spacing: 0.5px; width: 14%;">Status</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-muted" style="font-size: 0.68rem; letter-spacing: 0.5px; width: 22%;">Team Leader & Execution</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-muted text-end" style="font-size: 0.68rem; letter-spacing: 0.5px; width: 18%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        {{-- Column 1: Candidate & Lead ID --}}
                        <td class="py-3 px-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <a href="{{ route('sales.leads.show', $lead) }}" class="badge font-monospace text-decoration-none" 
                                   style="background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; font-size: 0.72rem; padding: 4px 8px; border-radius: 6px;">
                                    {{ $lead->lead_code }}
                                </a>
                                <span class="text-muted" style="font-size: 0.72rem;">
                                    <i class="far fa-calendar-alt me-1"></i>{{ $lead->created_at->format('d M Y') }}
                                </span>
                            </div>
                            <div class="fw-bold text-dark fs-6">{{ $lead->name }}</div>
                            <div class="small d-flex align-items-center gap-2 mt-0.5">
                                <a href="tel:{{ $lead->mobile }}" class="text-success fw-semibold text-decoration-none" style="font-size: 0.8rem;">
                                    <i class="fas fa-phone-alt me-1"></i>{{ $lead->mobile }}
                                </a>
                                @if($lead->college_company)
                                    <span class="text-muted opacity-50">&bull;</span>
                                    <span class="text-muted text-truncate" style="max-width: 170px; font-size: 0.78rem;" title="{{ $lead->college_company }}">
                                        <i class="fas fa-building me-1 opacity-75"></i>{{ $lead->college_company }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Column 2: Domain & Enquiry Type --}}
                        <td class="py-3 px-3">
                            <div>
                                <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.72rem; padding: 4px 8px; border-radius: 6px;">
                                    {{ $lead->domain ?? 'General' }}
                                </span>
                            </div>
                            <div class="small text-muted mt-1 fw-medium" style="font-size: 0.76rem;">
                                {{ $lead->enquiry_type_label }}
                            </div>
                        </td>

                        {{-- Column 3: Status --}}
                        <td class="py-3 px-3">
                            @php
                                $status = strtolower($lead->status);
                                $statusStyles = [
                                    'new' => ['bg' => '#f0f9ff', 'text' => '#0369a1', 'border' => '#bae6fd', 'icon' => 'fas fa-sparkles', 'label' => 'New Lead'],
                                    'contacted' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'icon' => 'fas fa-phone', 'label' => 'Contacted'],
                                    'interested' => ['bg' => '#fdf4ff', 'text' => '#86198f', 'border' => '#f5d0fe', 'icon' => 'fas fa-thumbs-up', 'label' => 'Interested'],
                                    'follow_up' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#ffedd5', 'icon' => 'fas fa-clock', 'label' => 'Follow Up'],
                                    'converted' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'icon' => 'fas fa-check-circle', 'label' => 'Converted'],
                                    'lost' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca', 'icon' => 'fas fa-times-circle', 'label' => 'Lost'],
                                ];
                                $curStatus = $statusStyles[$status] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0', 'icon' => 'fas fa-circle', 'label' => ucfirst($lead->status)];
                            @endphp
                            <span class="badge fw-semibold" 
                                  style="background: {{ $curStatus['bg'] }}; color: {{ $curStatus['text'] }}; border: 1px solid {{ $curStatus['border'] }}; font-size: 0.74rem; padding: 5px 10px; border-radius: 20px;">
                                <i class="{{ $curStatus['icon'] }} me-1" style="font-size: 0.68rem;"></i> {{ $curStatus['label'] }}
                            </span>
                        </td>

                        {{-- Column 4: Team Leader & Execution Workers --}}
                        <td class="py-3 px-3">
                            @if($lead->teamLeader)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" 
                                         style="width: 32px; height: 32px; background: linear-gradient(135deg, #0d3b1a, #1e7c42); font-size: 0.75rem; flex-shrink: 0;">
                                        <i class="fas fa-crown text-warning" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                            <span>{{ $lead->teamLeader->name }}</span>
                                            @if($lead->teamLeader->employee_code)
                                                <span class="badge font-monospace" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.65rem; padding: 1px 4px;">
                                                    {{ $lead->teamLeader->employee_code }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            @if($lead->leadWorkers->count() > 0)
                                                <span class="text-primary fw-semibold"><i class="fas fa-users me-1"></i>{{ $lead->leadWorkers->count() }} Workers assigned</span>
                                            @else
                                                <span class="text-muted">Awaiting worker assignment</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="badge" style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a; font-size: 0.74rem; padding: 5px 10px; border-radius: 20px;">
                                    <i class="fas fa-user-clock me-1 text-warning"></i> Pending TL Assign
                                </span>
                            @endif
                        </td>

                        {{-- Column 5: Actions Toolbar --}}
                        <td class="py-3 px-3 text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @if(!$lead->teamLeader)
                                    <a href="{{ route('sales.leads.forward-tl', $lead) }}" 
                                       class="btn btn-sm text-white fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" 
                                       style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); border-radius: 8px; font-size: 0.75rem; padding: 6px 12px;"
                                       title="Forward Lead to Team Leader">
                                        <i class="fas fa-crown text-warning"></i>
                                        <span>Forward TL</span>
                                    </a>
                                @else
                                    <a href="{{ route('sales.leads.forward-tl', $lead) }}" 
                                       class="btn btn-sm btn-outline-success fw-semibold d-inline-flex align-items-center gap-1" 
                                       style="border-radius: 8px; font-size: 0.75rem; padding: 5px 10px;"
                                       title="Reassign Team Leader">
                                        <i class="fas fa-exchange-alt"></i>
                                        <span>Re-assign</span>
                                    </a>
                                @endif

                                <button type="button" class="btn btn-sm btn-light border" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#assignTlModal{{ $lead->id }}" 
                                        title="Quick Modal Assign"
                                        style="border-radius: 8px; width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: #ffffff;">
                                    <i class="fas fa-bolt text-success" style="font-size: 0.75rem;"></i>
                                </button>

                                <a href="{{ route('sales.leads.edit', $lead) }}" 
                                   class="btn btn-sm btn-light border" 
                                   title="Edit Lead" 
                                   style="border-radius: 8px; width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: #ffffff;">
                                    <i class="fas fa-pen text-secondary" style="font-size: 0.72rem;"></i>
                                </a>

                                <a href="{{ route('sales.leads.show', $lead) }}" 
                                   class="btn btn-sm btn-light border" 
                                   title="View Details" 
                                   style="border-radius: 8px; width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: #ffffff;">
                                    <i class="fas fa-eye text-primary" style="font-size: 0.72rem;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50"></i>
                            <div class="fw-bold">No matching leads found</div>
                        </td>
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

    {{-- MODALS PLACED OUTSIDE THE TABLE CONTAINER TO PREVENT OVERFLOW / CLIPPING BUGS --}}
    @foreach($leads as $lead)
        <div class="modal fade" id="assignTlModal{{ $lead->id }}" tabindex="-1" aria-labelledby="assignTlModalLabel{{ $lead->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-start border-0 shadow" style="border-radius: 16px; overflow: hidden;">
                    <form action="{{ route('sales.leads.assign-tl', $lead) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%);">
                            <h5 class="modal-title fw-bold" id="assignTlModalLabel{{ $lead->id }}">
                                <i class="fas fa-crown me-2 text-warning"></i> Forward Lead to Team Leader
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-light border mb-3" style="border-radius: 10px;">
                                <div class="fw-bold">{{ $lead->lead_code }} &mdash; {{ $lead->name }}</div>
                                <div class="small text-muted">Domain: {{ $lead->domain ?? 'General' }} | Service: {{ $lead->service_needed ?? 'Standard' }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Select Team Leader (TL) <span class="text-danger">*</span></label>
                                <select name="tl_id" class="form-select" required>
                                    <option value="">-- Choose Team Leader --</option>
                                    @foreach($teamLeaders as $tl)
                                        <option value="{{ $tl->id }}" {{ $lead->tl_id == $tl->id ? 'selected' : '' }}>
                                            ⭐ {{ $tl->name }} ({{ $tl->department }} &bull; {{ $tl->designation }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Execution Priority</label>
                                <select name="priority" class="form-select form-select-sm">
                                    <option value="hot" {{ $lead->priority === 'hot' ? 'selected' : '' }}>🔥 Hot (High Priority / Urgent)</option>
                                    <option value="warm" {{ $lead->priority === 'warm' ? 'selected' : '' }}>☀️ Warm (Normal Schedule)</option>
                                    <option value="cold" {{ $lead->priority === 'cold' ? 'selected' : '' }}>❄️ Cold (Flexible Timeline)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Project & Handover Notes</label>
                                <textarea name="assignment_notes" class="form-control" rows="3" placeholder="Enter instructions, client budget, delivery expectations..."></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('sales.leads.forward-tl', $lead) }}" class="small text-success text-decoration-none fw-bold">
                                    <i class="fas fa-external-link-alt me-1"></i> Open Full Dedicated Forward Screen
                                </a>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3 px-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px; padding: 8px 16px;" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn text-white fw-bold px-4 shadow-sm" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); color: #ffffff !important; border-radius: 8px; padding: 9px 22px; border: none;">
                                <i class="fas fa-check-circle me-1 text-white" style="color: #ffffff !important;"></i>
                                <span class="text-white" style="color: #ffffff !important;">Confirm & Forward to TL</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection
