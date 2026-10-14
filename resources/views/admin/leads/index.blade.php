@extends('layouts.admin')

@section('title', 'All Website Leads')
@section('page-title', 'Website Lead Pipeline')
@section('page-subtitle', 'Manage, assign, follow up and convert website visitor enquiries')

@section('content')
<div class="container-fluid px-3 px-md-4 py-2">

    <style>
        /* ── Status Pills (Card 1) ── */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 500;
            color: #475569;
            text-decoration: none !important;
            transition: all 0.15s ease;
            white-space: nowrap;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .status-pill:hover {
            color: #0f172a;
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .status-pill.active {
            background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            box-shadow: 0 2px 6px rgba(13, 59, 26, 0.25);
            font-weight: 600;
        }

        .status-pill.active span {
            color: #ffffff !important;
        }

        .pill-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.06);
            color: inherit;
            line-height: 1.1;
        }

        .status-pill.active .pill-count {
            background: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }

        .pill-today {
            color: #2563eb;
            background: #eff6ff;
            border-color: #dbeafe;
        }
        .active-today {
            background: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
        }
        .active-today * {
            color: #ffffff !important;
        }

        .pill-overdue {
            color: #dc2626;
            background: #fef2f2;
            border-color: #fecaca;
        }
        .active-overdue {
            background: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626 !important;
        }
        .active-overdue * {
            color: #ffffff !important;
        }

        .pill-unassigned {
            color: #475569;
            background: #f1f5f9;
            border-color: #e2e8f0;
        }
        .active-unassigned {
            background: #475569 !important;
            color: #ffffff !important;
            border-color: #475569 !important;
        }
        .active-unassigned * {
            color: #ffffff !important;
        }

        /* ── Modern Search Input ── */
        .search-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .search-input-wrap .search-icon {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            font-size: 0.82rem;
            pointer-events: none;
            z-index: 2;
        }

        .search-control {
            height: 38px;
            padding-left: 34px;
            padding-right: 30px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.835rem;
            color: #0f172a;
            width: 100%;
            transition: border-color 0.2s;
        }

        .search-control:focus {
            border-color: #1e7c42;
            box-shadow: 0 0 0 3px rgba(30, 124, 66, 0.12);
        }

        .search-clear-btn {
            position: absolute;
            right: 10px;
            color: #94a3b8;
            font-size: 0.75rem;
            text-decoration: none;
            z-index: 2;
        }
        .search-clear-btn:hover {
            color: #dc2626;
        }

        /* ── Modern Select Filters ── */
        .filter-select {
            height: 38px;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.835rem;
            font-weight: 500;
            color: #1e293b;
            transition: border-color 0.2s;
            cursor: pointer;
            width: 100%;
        }

        .filter-select:focus {
            border-color: #1e7c42;
            box-shadow: 0 0 0 3px rgba(30, 124, 66, 0.12);
        }

        /* ── Green CTA Buttons (Explicit White Text) ── */
        .btn-brand-cta,
        .btn-brand-cta:hover,
        .btn-brand-cta:focus,
        .btn-brand-cta:active {
            background: linear-gradient(135deg, #1e7c42, #0d3b1a) !important;
            color: #ffffff !important;
            border: none !important;
        }
        .btn-brand-cta *,
        .btn-brand-cta i,
        .btn-brand-cta span {
            color: #ffffff !important;
        }

        .btn-filter-submit {
            height: 38px;
            background: linear-gradient(135deg, #1e7c42, #0d3b1a) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.83rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            white-space: nowrap;
            flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(13, 59, 26, 0.2);
        }

        .btn-filter-submit *,
        .btn-filter-submit i,
        .btn-filter-submit span {
            color: #ffffff !important;
        }

        .btn-filter-submit:hover {
            background: linear-gradient(135deg, #0d3b1a, #05200d) !important;
            color: #ffffff !important;
        }

        .btn-filter-reset {
            height: 38px;
            width: 38px;
            min-width: 38px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #64748b;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            text-decoration: none !important;
            flex-shrink: 0;
        }

        .btn-filter-reset:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .btn-reset-active {
            background: #fef2f2 !important;
            border-color: #fca5a5 !important;
            color: #dc2626 !important;
        }
    </style>

    {{-- Header Actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-1" style="gap: 16px;">
        <div>
            <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.3px;">Website Leads Directory</h3>
            <span class="text-muted small">Showing {{ $leads->total() }} total enquiries captured across all channels</span>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 10px !important;">
            <a href="{{ route('admin.leads.export', request()->all()) }}" class="btn btn-outline-secondary btn-sm shadow-sm" style="border-radius: 8px; font-weight: 500; height: 36px; display: inline-flex; align-items: center; gap: 8px; padding: 0 14px;">
                <i class="fas fa-file-export text-muted"></i>
                <span>Export CSV</span>
            </a>
            <form action="{{ route('admin.leads.auto-assign') }}" method="POST" class="d-inline-flex m-0 p-0">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm shadow-sm" style="border-radius: 8px; font-weight: 500; height: 36px; display: inline-flex; align-items: center; gap: 8px; padding: 0 14px;" onclick="return confirm('Auto-assign all unassigned leads?')">
                    <i class="fas fa-robot"></i>
                    <span>Auto Assign</span>
                </button>
            </form>
            <a href="{{ route('admin.leads.create') }}" class="btn btn-sm shadow-sm btn-brand-cta" style="border-radius: 8px; font-weight: 600; height: 36px; display: inline-flex; align-items: center; gap: 8px; padding: 0 16px;">
                <i class="fas fa-plus"></i>
                <span>New Lead</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════
       CARD 1: STATUS PIPELINE NAVIGATION PILLS
    ══════════════════════════════════════ --}}
    <div class="card mb-4 border-0 shadow-sm bg-white" style="border: 1px solid #e2e8f0 !important; border-radius: 12px;">
        <div class="card-body d-flex flex-wrap align-items-center overflow-auto" style="padding: 12px 16px; gap: 10px !important;">
            @php
                $currStatus = request('status');
                $isAllActive = $currentTab === 'all' && !$currStatus;
            @endphp
            
            <a href="{{ route('admin.leads.index', array_merge(request()->except('tab', 'status', 'page'), ['tab' => 'all'])) }}"
               class="status-pill {{ $isAllActive ? 'active' : '' }}">
                <span>All</span>
                <span class="pill-count">{{ $counts['all'] }}</span>
            </a>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('tab', 'page'), ['status' => 'new', 'tab' => 'new'])) }}"
               class="status-pill {{ $currStatus === 'new' ? 'active' : '' }}">
                <span>New Leads</span>
                <span class="pill-count">{{ $counts['new'] }}</span>
            </a>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('tab', 'page'), ['status' => 'contacted', 'tab' => 'contacted'])) }}"
               class="status-pill {{ $currStatus === 'contacted' ? 'active' : '' }}">
                <span>Contacted</span>
                <span class="pill-count">{{ $counts['contacted'] ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('tab', 'page'), ['status' => 'follow_up', 'tab' => 'follow_up'])) }}"
               class="status-pill {{ $currStatus === 'follow_up' ? 'active' : '' }}">
                <span>Follow-up</span>
                <span class="pill-count">{{ $counts['follow_up'] }}</span>
            </a>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('tab', 'page'), ['status' => 'interested', 'tab' => 'interested'])) }}"
               class="status-pill {{ $currStatus === 'interested' ? 'active' : '' }}">
                <span>Interested</span>
                <span class="pill-count">{{ $counts['interested'] }}</span>
            </a>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('tab', 'page'), ['status' => 'converted', 'tab' => 'converted'])) }}"
               class="status-pill {{ $currStatus === 'converted' ? 'active' : '' }}">
                <span>Converted</span>
                <span class="pill-count">{{ $counts['converted'] }}</span>
            </a>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('tab', 'page'), ['status' => 'lost', 'tab' => 'lost'])) }}"
               class="status-pill {{ $currStatus === 'lost' ? 'active' : '' }}">
                <span>Lost</span>
                <span class="pill-count">{{ $counts['lost'] }}</span>
            </a>

            <div class="vr my-auto text-muted opacity-25" style="height: 22px; margin: 0 4px;"></div>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('status', 'page'), ['tab' => 'today'])) }}"
               class="status-pill {{ $currentTab === 'today' ? 'active-today' : 'pill-today' }}"
               title="View leads created or due today">
                <i class="far fa-calendar-check me-1"></i> Today's
                <span class="pill-count ms-1">{{ $counts['today'] }}</span>
            </a>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('status', 'page'), ['tab' => 'overdue'])) }}"
               class="status-pill {{ $currentTab === 'overdue' ? 'active-overdue' : 'pill-overdue' }}"
               title="View overdue follow-ups">
                <i class="fas fa-exclamation-circle me-1"></i> Overdue
                <span class="pill-count ms-1">{{ $counts['overdue'] }}</span>
            </a>

            <a href="{{ route('admin.leads.index', array_merge(request()->except('status', 'page'), ['tab' => 'unassigned'])) }}"
               class="status-pill {{ $currentTab === 'unassigned' ? 'active-unassigned' : 'pill-unassigned' }}"
               title="View unassigned leads">
                <i class="far fa-user me-1"></i> Unassigned
                <span class="pill-count ms-1">{{ $counts['unassigned'] }}</span>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       CARD 2: SEARCH & FILTER CONTROLS BAR (No Overflow)
    ══════════════════════════════════════ --}}
    <div class="card mb-4 border-0 shadow-sm bg-white" style="border: 1px solid #e2e8f0 !important; border-radius: 12px;">
        <div class="card-body" style="padding: 14px 18px;">
            <form action="{{ route('admin.leads.index') }}" method="GET" class="d-flex flex-wrap align-items-center" style="gap: 10px !important;">
                <input type="hidden" name="tab" value="{{ $currentTab }}">

                {{-- Search Box --}}
                <div class="search-input-wrap flex-grow-1" style="min-width: 200px; max-width: 320px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control form-control-sm search-control" 
                           placeholder="Search name, phone, email, code..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('admin.leads.index', request()->except('search', 'page')) }}" class="search-clear-btn" title="Clear search">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>

                {{-- Priority Filter --}}
                <div style="min-width: 130px; flex: 1 1 130px; max-width: 170px;">
                    <select name="priority" class="form-select form-select-sm filter-select">
                        <option value="all">Priority (All)</option>
                        <option value="hot" {{ request('priority') === 'hot' ? 'selected' : '' }}>🔥 Hot</option>
                        <option value="warm" {{ request('priority') === 'warm' ? 'selected' : '' }}>☀️ Warm</option>
                        <option value="cold" {{ request('priority') === 'cold' ? 'selected' : '' }}>❄️ Cold</option>
                    </select>
                </div>

                {{-- Enquiry Type Filter --}}
                <div style="min-width: 150px; flex: 1 1 150px; max-width: 200px;">
                    <select name="enquiry_type" class="form-select form-select-sm filter-select">
                        <option value="all">Enquiry Type (All)</option>
                        <option value="course_enquiry" {{ request('enquiry_type') === 'course_enquiry' ? 'selected' : '' }}>Course Enquiry</option>
                        <option value="internship_enquiry" {{ request('enquiry_type') === 'internship_enquiry' ? 'selected' : '' }}>Internship Enquiry</option>
                        <option value="project_enquiry" {{ request('enquiry_type') === 'project_enquiry' ? 'selected' : '' }}>Project Enquiry</option>
                        <option value="service_enquiry" {{ request('enquiry_type') === 'service_enquiry' ? 'selected' : '' }}>Software Service</option>
                        <option value="callback_request" {{ request('enquiry_type') === 'callback_request' ? 'selected' : '' }}>Callback Request</option>
                        <option value="whatsapp_enquiry" {{ request('enquiry_type') === 'whatsapp_enquiry' ? 'selected' : '' }}>WhatsApp Enquiry</option>
                    </select>
                </div>

                {{-- Source Filter --}}
                <div style="min-width: 130px; flex: 1 1 130px; max-width: 170px;">
                    <select name="source" class="form-select form-select-sm filter-select">
                        <option value="all">Source (All)</option>
                        @foreach($sources as $srcKey => $srcLabel)
                            <option value="{{ $srcKey }}" {{ request('source') === $srcKey ? 'selected' : '' }}>{{ $srcLabel }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Staff Filter --}}
                <div style="min-width: 130px; flex: 1 1 130px; max-width: 170px;">
                    <select name="assigned_to" class="form-select form-select-sm filter-select">
                        <option value="all">Staff (All)</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('assigned_to') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center flex-shrink-0 ms-auto" style="gap: 10px !important;">
                    <button type="submit" class="btn btn-sm btn-filter-submit" style="gap: 8px;" title="Apply Filters">
                        <i class="fas fa-filter"></i> <span>Filter</span>
                    </button>
                    @php
                        $hasActiveFilters = request()->hasAny(['search', 'priority', 'enquiry_type', 'source', 'assigned_to']) && 
                            (request('search') || (request('priority') && request('priority') !== 'all') || (request('enquiry_type') && request('enquiry_type') !== 'all') || (request('source') && request('source') !== 'all') || (request('assigned_to') && request('assigned_to') !== 'all'));
                    @endphp
                    <a href="{{ route('admin.leads.index') }}" 
                       class="btn btn-sm btn-filter-reset {{ $hasActiveFilters ? 'btn-reset-active' : '' }}" 
                       title="Reset all filters">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       DATA TABLE
    ══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm" style="border-radius: var(--r-md); overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="min-width: 140px;">Lead Code</th>
                            <th style="min-width: 200px;">Candidate Details</th>
                            <th>Enquiry / Domain</th>
                            <th>Source & Page</th>
                            <th>Status & Priority</th>
                            <th>Assigned Staff</th>
                            <th>Next Follow-up</th>
                            <th class="text-end pe-4" style="min-width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                {{-- Lead Code & Created Time --}}
                                <td>
                                    <span class="badge badge-soft-neutral font-monospace fw-bold">{{ $lead->lead_code }}</span>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                        <i class="far fa-calendar-alt me-1 opacity-75"></i>{{ $lead->created_at->format('d M, h:i A') }}
                                    </div>
                                    @if($lead->is_duplicate)
                                        <span class="badge badge-soft-danger rounded-pill mt-1" style="font-size: 0.62rem;" title="Duplicate lead detected">
                                            <i class="fas fa-copy me-1"></i>Duplicate
                                        </span>
                                    @endif
                                </td>

                                {{-- Candidate Contact --}}
                                <td>
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="fw-bold text-dark text-decoration-none d-block">
                                        {{ $lead->name }}
                                    </a>
                                    @if($lead->college_company)
                                        <div class="text-muted text-truncate" style="font-size: 0.75rem; max-width: 210px;" title="{{ $lead->college_company }}">
                                            <i class="fas fa-building me-1 opacity-75"></i>{{ $lead->college_company }}
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center gap-1.5 mt-1.5 flex-wrap">
                                        <a href="tel:{{ $lead->mobile }}" class="badge badge-soft-primary text-decoration-none" title="Call {{ $lead->mobile }}">
                                            <i class="fas fa-phone-alt me-1"></i>{{ $lead->mobile }}
                                        </a>
                                        @php $cleanPhone = preg_replace('/[^0-9]/','', $lead->whatsapp ?: $lead->mobile); @endphp
                                        <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="badge badge-soft-success text-decoration-none" title="WhatsApp Message">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        @if($lead->email)
                                            <a href="mailto:{{ $lead->email }}" class="badge badge-soft-neutral text-decoration-none" title="Email: {{ $lead->email }}">
                                                <i class="far fa-envelope"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                {{-- Enquiry & Domain --}}
                                <td>
                                    <span class="badge badge-soft-teal rounded-pill">{{ $lead->enquiry_type_label }}</span>
                                    <div class="fw-bold text-dark small mt-1">
                                        {{ $lead->domain ?: ($lead->service_needed ?: 'General') }}
                                    </div>
                                    @if($lead->course_department)
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $lead->course_department }}</div>
                                    @endif
                                </td>

                                {{-- Source & Landing Page --}}
                                <td>
                                    <span class="badge badge-soft-purple rounded-pill">
                                        <i class="fas fa-globe me-1"></i>{{ $lead->source_label }}
                                    </span>
                                    @if($lead->source_page)
                                        <div class="text-muted text-truncate mt-1" style="font-size: 0.72rem; max-width: 140px;" title="{{ $lead->source_page }}">
                                            <i class="fas fa-link me-1 opacity-75"></i>{{ $lead->source_page }}
                                        </div>
                                    @endif
                                    @if($lead->campaign_name)
                                        <div class="badge badge-soft-neutral mt-1" style="font-size: 0.65rem;">
                                            <i class="fas fa-bullhorn me-1"></i>{{ $lead->campaign_name }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Status & Priority --}}
                                <td>
                                    @php
                                        $badgeStyles = [
                                            'new' => 'badge-soft-info',
                                            'contacted' => 'badge-soft-primary',
                                            'follow_up' => 'badge-soft-warning',
                                            'interested' => 'badge-soft-success',
                                            'converted' => 'badge-soft-success',
                                            'lost' => 'badge-soft-danger',
                                        ];
                                        $badgeClass = $badgeStyles[$lead->status] ?? 'badge-soft-neutral';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill">{{ $lead->status_label }}</span>

                                    <div class="mt-1">
                                        @if($lead->priority === 'hot')
                                            <span class="badge badge-soft-danger rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-fire me-1"></i>Hot</span>
                                        @elseif($lead->priority === 'warm')
                                            <span class="badge badge-soft-warning rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-sun me-1"></i>Warm</span>
                                        @else
                                            <span class="badge badge-soft-info rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-snowflake me-1"></i>Cold</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Staff Assigned --}}
                                <td>
                                    @if($lead->assignedStaff)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="user-avatar" style="width: 28px; height: 28px; font-size: 0.7rem; background: linear-gradient(135deg, #1e7c42, #0d3b1a);">
                                                {{ strtoupper(substr($lead->assignedStaff->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $lead->assignedStaff->name }}</div>
                                                <div class="text-muted" style="font-size: 0.68rem;">{{ $lead->assignedStaff->department }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge badge-soft-neutral">
                                            <i class="fas fa-user-clock me-1 text-muted"></i>Unassigned
                                        </span>
                                    @endif
                                </td>

                                {{-- Next Follow-up --}}
                                <td>
                                    @if($lead->next_followup_date)
                                        @if($lead->is_overdue)
                                            <span class="badge badge-soft-danger rounded-pill fw-bold">
                                                <i class="fas fa-circle-exclamation me-1"></i>Overdue: {{ $lead->next_followup_date->diffForHumans() }}
                                            </span>
                                        @elseif($lead->is_today_followup)
                                            <span class="badge badge-soft-warning rounded-pill fw-bold">
                                                <i class="far fa-clock me-1"></i>Today {{ $lead->next_followup_date->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-neutral">
                                                <i class="far fa-calendar me-1 text-muted"></i>{{ $lead->next_followup_date->format('d M, h:i A') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-end pe-4">
                                    <div class="btn-action-group justify-content-end">
                                        <a href="{{ route('admin.leads.show', $lead) }}" class="btn-action btn-action-view" title="View 360° Lead Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.leads.edit', $lead) }}" class="btn-action btn-action-edit" title="Edit Lead">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this lead record permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-delete" title="Delete Lead">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                        <i class="fas fa-inbox fs-2 text-muted"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">No Website Leads Found</h5>
                                    <p class="small text-muted mb-3">Try clearing your filters or create a new lead enquiry manually.</p>
                                    <a href="{{ route('admin.leads.create') }}" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-plus me-1"></i> Add First Lead
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($leads->hasPages())
            <div class="card-footer bg-white py-3 border-top border-light">
                {{ $leads->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
