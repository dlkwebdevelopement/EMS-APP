@extends('layouts.admin')

@section('title', 'Website Lead Dashboard')
@section('page-title', 'Website Lead Management')
@section('page-subtitle', 'Real-time pipeline, source tracking & conversion analytics')

@section('content')
<div class="container-fluid px-0">

    {{-- ══════════════════════════════════════
       HERO STRIP
    ══════════════════════════════════════ --}}
    <div class="page-hero-strip d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <div class="strip-rule"></div>
            <h2><i class="fas fa-funnel-dollar me-2 text-warning"></i>Website Lead Command Center</h2>
            <p>Track visitor enquiries, auto-route to staff, drive follow-ups, and accelerate customer conversion.</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.leads.create') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-plus me-1"></i> Add Lead
            </a>
            <a href="{{ route('admin.leads.index') }}" class="btn btn-light shadow-sm text-dark">
                <i class="fas fa-list me-1"></i> All Leads
            </a>
            <a href="{{ route('admin.leads.settings') }}" class="btn btn-outline-light shadow-sm">
                <i class="fas fa-code me-1"></i> Web Form Integration
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
       KPI STATS CARDS
    ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- Total Leads --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stats-card">
                <div class="stats-icon text-white"><i class="fas fa-users"></i></div>
                <div class="stats-info">
                    <h3>{{ number_format($totalLeads) }}</h3>
                    <p>Total Leads</p>
                </div>
            </div>
        </div>

        {{-- Today's Leads --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stats-card lime-card">
                <div class="stats-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stats-info">
                    <h3>{{ number_format($todayLeads) }}</h3>
                    <p>Today's Leads</p>
                </div>
            </div>
        </div>

        {{-- New Fresh Leads --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stats-card teal-card">
                <div class="stats-icon"><i class="fas fa-sparkles"></i></div>
                <div class="stats-info">
                    <h3>{{ number_format($newLeads) }}</h3>
                    <p>Fresh / Unworked</p>
                </div>
            </div>
        </div>

        {{-- In Follow-up --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stats-card gold-card">
                <div class="stats-icon"><i class="fas fa-phone-volume"></i></div>
                <div class="stats-info">
                    <h3>{{ number_format($followupLeads) }}</h3>
                    <p>In Follow-up</p>
                </div>
            </div>
        </div>

        {{-- Converted --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stats-card" style="background: linear-gradient(135deg, #16a34a, #15803d);">
                <div class="stats-icon text-white"><i class="fas fa-trophy"></i></div>
                <div class="stats-info">
                    <h3>{{ number_format($convertedLeads) }}</h3>
                    <p>Converted</p>
                </div>
            </div>
        </div>

        {{-- Conversion Rate --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stats-card" style="background: linear-gradient(135deg, #4f46e5, #3730a3);">
                <div class="stats-icon text-white"><i class="fas fa-chart-line"></i></div>
                <div class="stats-info">
                    <h3>{{ $conversionRate }}%</h3>
                    <p>Win Rate</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary Strip --}}
    <div class="card mb-4 bg-white shadow-sm border-0" style="border-radius: var(--r-md);">
        <div class="card-body py-3 px-4 d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-rupee-sign text-success fs-5"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Converted Deal Value</span>
                    <h4 class="mb-0 fw-bold text-dark">₹{{ number_format($totalDealValue, 2) }}</h4>
                </div>
            </div>
            <div class="vr d-none d-md-block"></div>
            <div>
                <span class="text-muted small text-uppercase fw-bold">Revenue Collected</span>
                <h4 class="mb-0 fw-bold text-success">₹{{ number_format($totalRevenueCollected, 2) }}</h4>
            </div>
            <div class="vr d-none d-md-block"></div>
            <div>
                <span class="text-muted small text-uppercase fw-bold">Pending Balance</span>
                <h4 class="mb-0 fw-bold text-warning">₹{{ number_format($totalPendingBalance, 2) }}</h4>
            </div>
            <div class="vr d-none d-md-block"></div>
            <form action="{{ route('admin.leads.auto-assign') }}" method="POST" class="mt-2 mt-md-0">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="return confirm('Auto-route all unassigned leads based on department rules?')">
                    <i class="fas fa-robot me-1"></i> Run Auto-Assignment
                </button>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       ACTIONABLE ALERTS: TODAY & OVERDUE
    ══════════════════════════════════════ --}}
    <div class="row g-4 mb-4">
        {{-- Today's Scheduled Calls --}}
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0" style="border-radius: var(--r-md); overflow: hidden;">
                <div class="card-header bg-white border-bottom border-light d-flex justify-content-between align-items-center py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                            <i class="fas fa-calendar-check fs-6"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">Today's Callback & Follow-up Queue</h6>
                            <span class="text-muted" style="font-size: 0.73rem;">Scheduled calls requiring immediate action</span>
                        </div>
                    </div>
                    <span class="badge badge-soft-warning rounded-pill px-3 py-1.5 fw-bold">
                        <i class="far fa-clock me-1 text-warning"></i> {{ $todayFollowups->count() }} Due
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($todayFollowups->isEmpty())
                        <div class="p-5 text-center text-muted">
                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                <i class="fas fa-check-double fs-3 text-success"></i>
                            </div>
                            <h6 class="fw-bold text-dark">All Caught Up!</h6>
                            <p class="mb-0 small text-muted">Great job! All of today's follow-ups are attended to.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width: 170px;">Lead Details</th>
                                        <th>Course / Domain</th>
                                        <th>Assigned Staff</th>
                                        <th>Time</th>
                                        <th class="text-end pe-3" style="min-width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayFollowups as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.leads.show', $lead) }}" class="fw-bold text-dark text-decoration-none d-block">
                                                    {{ $lead->name }}
                                                </a>
                                                <a href="tel:{{ $lead->mobile }}" class="text-muted small text-decoration-none">
                                                    <i class="fas fa-phone-alt text-primary me-1" style="font-size: 0.7rem;"></i>{{ $lead->mobile }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-teal rounded-pill">{{ $lead->domain ?: $lead->enquiry_type_label }}</span>
                                            </td>
                                            <td>
                                                @if($lead->assignedStaff)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="user-avatar" style="width: 26px; height: 26px; font-size: 0.68rem;">
                                                            {{ strtoupper(substr($lead->assignedStaff->name, 0, 2)) }}
                                                        </div>
                                                        <span class="fw-semibold text-dark small">{{ $lead->assignedStaff->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="badge badge-soft-neutral">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-neutral fw-semibold">
                                                    <i class="far fa-clock me-1 text-muted"></i>{{ $lead->next_followup_date ? $lead->next_followup_date->format('h:i A') : 'Today' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-action-group justify-content-end">
                                                    <a href="tel:{{ $lead->mobile }}" class="btn-action btn-action-call" title="Call Now">
                                                        <i class="fas fa-phone-alt"></i>
                                                    </a>
                                                    @php $cleanPhone = preg_replace('/[^0-9]/','', $lead->whatsapp ?: $lead->mobile); @endphp
                                                    <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="btn-action btn-action-whatsapp" title="WhatsApp Chat">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                    <a href="{{ route('admin.leads.show', $lead) }}" class="btn-action btn-action-view" title="Attend Lead Profile">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Overdue Follow-ups --}}
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0" style="border-radius: var(--r-md); overflow: hidden;">
                <div class="card-header bg-white border-bottom border-light d-flex justify-content-between align-items-center py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);">
                            <i class="fas fa-exclamation-triangle fs-6"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">Missed & Overdue Follow-ups</h6>
                            <span class="text-muted" style="font-size: 0.73rem;">Urgent attention required to prevent lead leakage</span>
                        </div>
                    </div>
                    <span class="badge badge-soft-danger rounded-pill px-3 py-1.5 fw-bold">
                        <i class="fas fa-fire me-1 text-danger"></i> {{ $overdueFollowups->count() }} Overdue
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($overdueFollowups->isEmpty())
                        <div class="p-5 text-center text-muted">
                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                <i class="fas fa-shield-alt fs-3 text-success"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Zero Overdue Follow-ups!</h6>
                            <p class="mb-0 small text-muted">Response times are healthy across all teams.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width: 170px;">Lead Details</th>
                                        <th>Enquiry Type</th>
                                        <th>Staff</th>
                                        <th>Due Since</th>
                                        <th class="text-end pe-3" style="min-width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueFollowups as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.leads.show', $lead) }}" class="fw-bold text-dark text-decoration-none d-block">
                                                    {{ $lead->name }}
                                                </a>
                                                <a href="tel:{{ $lead->mobile }}" class="text-muted small text-decoration-none">
                                                    <i class="fas fa-phone-alt text-primary me-1" style="font-size: 0.7rem;"></i>{{ $lead->mobile }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-purple rounded-pill">{{ $lead->enquiry_type_label }}</span>
                                            </td>
                                            <td>
                                                @if($lead->assignedStaff)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="user-avatar" style="width: 26px; height: 26px; font-size: 0.68rem;">
                                                            {{ strtoupper(substr($lead->assignedStaff->name, 0, 2)) }}
                                                        </div>
                                                        <span class="fw-semibold text-dark small">{{ $lead->assignedStaff->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="badge badge-soft-neutral">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-danger rounded-pill fw-bold">
                                                    <i class="fas fa-circle-exclamation me-1"></i>{{ $lead->next_followup_date ? $lead->next_followup_date->diffForHumans() : 'Overdue' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-action-group justify-content-end">
                                                    <a href="tel:{{ $lead->mobile }}" class="btn-action btn-action-call" title="Call Now">
                                                        <i class="fas fa-phone-alt"></i>
                                                    </a>
                                                    @php $cleanPhone = preg_replace('/[^0-9]/','', $lead->whatsapp ?: $lead->mobile); @endphp
                                                    <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="btn-action btn-action-whatsapp" title="WhatsApp Chat">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                    <a href="{{ route('admin.leads.show', $lead) }}" class="btn-action btn-action-view" title="Attend Lead Profile">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       CHARTS & TRENDS
    ══════════════════════════════════════ --}}
    <div class="row g-4 mb-4">
        {{-- Daily Trends --}}
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-chart-bar text-white"></i></span>
                        <span>Daily Leads & Conversions (Last 7 Days)</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="dailyTrendChart" height="110"></canvas>
                </div>
            </div>
        </div>

        {{-- Source Breakdown Doughnut --}}
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-pie-chart text-white"></i></span>
                        <span>Traffic Sources</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="sourceChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       PAGE-WISE LEADS & RECENT ENQUIRIES
    ══════════════════════════════════════ --}}
    <div class="row g-4 mb-4">
        {{-- Top Landing & Referrer Pages --}}
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-file-alt text-white"></i></span>
                        <span>Lead Volume by Website Page</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($leadsByPage as $pg)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <i class="fas fa-globe text-muted me-2"></i>
                                    <span class="fw-bold text-dark">{{ $pg->page }}</span>
                                </div>
                                <span class="badge badge-green">{{ $pg->count }} leads</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-3">No page tracking data yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Recent Incoming Leads Feed --}}
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-stream text-white"></i></span>
                        <span>Recent Incoming Enquiries</span>
                    </div>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-link text-decoration-none">View All &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Lead Code</th>
                                    <th>Candidate</th>
                                    <th>Requirement</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLeads as $lead)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark font-monospace">{{ $lead->lead_code }}</span>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $lead->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $lead->name }}</div>
                                            <div class="text-muted small">{{ $lead->mobile }}</div>
                                        </td>
                                        <td>
                                            <span class="badge badge-lime">{{ $lead->domain ?: $lead->enquiry_type_label }}</span>
                                            @if($lead->priority === 'hot')
                                                <i class="fas fa-fire text-danger ms-1" title="Hot Lead"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @php $badge = $lead->status_badge; @endphp
                                            <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-xs btn-outline-primary" title="View Lead">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .stats-card {
        border-radius: var(--r-md);
        padding: 1.25rem 1rem;
    }
    .stats-info h3 {
        font-size: 1.7rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Daily Leads Trend Chart
        const dailyLabels = {!! json_encode(array_column($dailyTrend, 'day')) !!};
        const dailyLeadsData = {!! json_encode(array_column($dailyTrend, 'leads')) !!};
        const dailyConvertedData = {!! json_encode(array_column($dailyTrend, 'converted')) !!};

        const ctxDaily = document.getElementById('dailyTrendChart').getContext('2d');
        new Chart(ctxDaily, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [
                    {
                        label: 'Total Inquiries',
                        data: dailyLeadsData,
                        backgroundColor: 'rgba(39, 163, 85, 0.75)',
                        borderColor: '#27a355',
                        borderRadius: 6,
                    },
                    {
                        label: 'Converted Customers',
                        data: dailyConvertedData,
                        backgroundColor: 'rgba(126, 213, 58, 0.85)',
                        borderColor: '#7ed53a',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // Source Doughnut Chart
        const sourceLabels = {!! json_encode($leadsBySource->pluck('source')->map(fn($s) => ucwords(str_replace('_',' ',$s)))) !!};
        const sourceData = {!! json_encode($leadsBySource->pluck('count')) !!};

        const ctxSource = document.getElementById('sourceChart').getContext('2d');
        new Chart(ctxSource, {
            type: 'doughnut',
            data: {
                labels: sourceLabels,
                datasets: [{
                    data: sourceData,
                    backgroundColor: [
                        '#27a355', '#7ed53a', '#0ea5e9', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endpush
