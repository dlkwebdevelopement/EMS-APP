@extends('layouts.admin')

@section('title', 'Follow-up Management')
@section('page-title', 'Follow-up & Callback Hub')
@section('page-subtitle', 'Track reminders, schedule calls and ensure zero lead leakage')

@section('content')
<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Follow-up Management</h3>
            <span class="text-muted small">Daily scheduled calls, callbacks and conversation logs</span>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-primary btn-sm rounded-pill shadow-sm">
                <i class="fas fa-list me-1"></i> All Leads
            </a>
            <a href="{{ route('admin.leads.dashboard') }}" class="btn btn-light btn-sm rounded-pill shadow-sm text-dark">
                <i class="fas fa-chart-pie me-1"></i> Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════
       KPI SUMMARY COUNTERS
    ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md); border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Due Today</span>
                        <h3 class="mb-0 fw-bold text-warning">{{ $todayLeads->count() }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-calendar-day fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md); border-left: 4px solid #ef4444 !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Overdue / Missed</span>
                        <h3 class="mb-0 fw-bold text-danger">{{ $overdueLeads->count() }}</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-exclamation-triangle fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md); border-left: 4px solid #3b82f6 !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Upcoming (7 Days)</span>
                        <h3 class="mb-0 fw-bold text-primary">{{ $upcomingLeads->count() }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-calendar-alt fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       TABBED QUEUE
    ══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md); overflow: hidden;">
        <div class="card-header bg-white border-bottom border-light p-3">
            <ul class="nav nav-pills card-header-pills gap-2" id="followupTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" role="tab" class="nav-link active rounded-pill btn-sm px-3.5 py-2 fw-bold" data-bs-toggle="pill" data-bs-target="#todayTab" aria-controls="todayTab" aria-selected="true">
                        <i class="fas fa-phone-volume me-1 text-warning"></i> Today's Queue
                        <span class="badge bg-warning bg-opacity-20 text-dark rounded-pill ms-1">{{ $todayLeads->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" role="tab" class="nav-link rounded-pill btn-sm px-3.5 py-2 fw-semibold" data-bs-toggle="pill" data-bs-target="#overdueTab" aria-controls="overdueTab" aria-selected="false">
                        <i class="fas fa-circle-exclamation me-1 text-danger"></i> Overdue
                        <span class="badge bg-danger text-white rounded-pill ms-1">{{ $overdueLeads->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" role="tab" class="nav-link rounded-pill btn-sm px-3.5 py-2 fw-semibold" data-bs-toggle="pill" data-bs-target="#upcomingTab" aria-controls="upcomingTab" aria-selected="false">
                        <i class="fas fa-calendar-week me-1 text-primary"></i> Upcoming (7 Days)
                        <span class="badge bg-primary bg-opacity-15 text-primary rounded-pill ms-1">{{ $upcomingLeads->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" role="tab" class="nav-link rounded-pill btn-sm px-3.5 py-2 fw-semibold" data-bs-toggle="pill" data-bs-target="#completedTab" aria-controls="completedTab" aria-selected="false">
                        <i class="fas fa-check-double me-1 text-success"></i> Recent Activity Log
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content">

                {{-- Tab 1: Today's Follow-ups --}}
                <div class="tab-pane fade show active" id="todayTab">
                    @include('admin.leads.partials.followup_table', ['leadsList' => $todayLeads, 'emptyMsg' => 'No follow-ups due today. You are all caught up!'])
                </div>

                {{-- Tab 2: Overdue Follow-ups --}}
                <div class="tab-pane fade" id="overdueTab">
                    @include('admin.leads.partials.followup_table', ['leadsList' => $overdueLeads, 'emptyMsg' => 'Zero overdue follow-ups! Excellent response time.'])
                </div>

                {{-- Tab 3: Upcoming Follow-ups --}}
                <div class="tab-pane fade" id="upcomingTab">
                    @include('admin.leads.partials.followup_table', ['leadsList' => $upcomingLeads, 'emptyMsg' => 'No upcoming follow-ups scheduled for the next 7 days.'])
                </div>

                {{-- Tab 4: Completed Follow-up Log --}}
                <div class="tab-pane fade" id="completedTab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 170px;">Lead Code & Name</th>
                                    <th>Channel</th>
                                    <th>Outcome</th>
                                    <th>Summary Notes</th>
                                    <th>Logged By</th>
                                    <th>Completed Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedFollowups as $act)
                                    <tr>
                                        <td>
                                            @if($act->lead)
                                                <a href="{{ route('admin.leads.show', $act->lead) }}" class="fw-bold text-dark text-decoration-none d-block">
                                                    {{ $act->lead->name }}
                                                </a>
                                                <span class="badge badge-soft-neutral font-monospace mt-1" style="font-size: 0.65rem;">{{ $act->lead->lead_code }}</span>
                                            @else
                                                <span class="text-muted">Deleted Lead</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $act->type_badge['class'] }} rounded-pill">
                                                <i class="{{ $act->type_badge['icon'] }} me-1"></i> {{ $act->type_badge['label'] }}
                                            </span>
                                        </td>
                                        <td><span class="badge badge-soft-neutral fw-bold">{{ $act->outcome }}</span></td>
                                        <td style="max-width: 300px;"><span class="text-secondary small">{{ $act->notes }}</span></td>
                                        <td>
                                            @if($act->staff)
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <div class="user-avatar" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                                        {{ strtoupper(substr($act->staff->name, 0, 2)) }}
                                                    </div>
                                                    <span class="small fw-semibold text-dark">{{ $act->staff->name }}</span>
                                                </div>
                                            @else
                                                <span class="small text-muted">Admin</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                <i class="far fa-clock me-1"></i>{{ $act->completed_at ? $act->completed_at->format('d M, h:i A') : $act->created_at->format('d M, h:i A') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                                <i class="fas fa-clipboard-list fs-4 text-muted"></i>
                                            </div>
                                            <p class="mb-0 fw-semibold text-dark">No completed interaction notes logged yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
