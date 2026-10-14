@extends('layouts.admin')

@section('title', 'Website Lead Analytics & Reports')
@section('page-title', 'Lead Analytics & Reports')
@section('page-subtitle', 'Source tracking, campaign ROI, conversion rates and team performance')

@section('content')
<div class="container-fluid px-0">

    {{-- Header & Date Filter --}}
    <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: var(--r-md);">
        <div class="card-body p-3">
            <form action="{{ route('admin.leads.analytics') }}" method="GET" class="row g-2 align-items-center justify-content-between">
                <div class="col-md-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line text-success me-2"></i>Performance Insights</h5>
                    <span class="text-muted small">Period: {{ Carbon\Carbon::parse($dateFrom)->format('d M Y') }} &mdash; {{ Carbon\Carbon::parse($dateTo)->format('d M Y') }}</span>
                </div>
                <div class="col-md-6 d-flex gap-2 justify-content-md-end align-items-center">
                    <div class="d-flex align-items-center gap-1">
                        <label class="small text-muted mb-0">From:</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <label class="small text-muted mb-0">To:</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-filter me-1"></i> Apply</button>
                    <a href="{{ route('admin.leads.export', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-outline-success btn-sm" title="Export Filtered CSV">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       KPI SUMMARY METRICS
    ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <div class="stats-icon text-white"><i class="fas fa-filter"></i></div>
                <div class="stats-info">
                    <h3>{{ number_format($totalLeads) }}</h3>
                    <p>Total Leads</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stats-card lime-card">
                <div class="stats-icon"><i class="fas fa-trophy"></i></div>
                <div class="stats-info">
                    <h3>{{ number_format($convertedLeads) }}</h3>
                    <p>Converted Deals</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stats-card teal-card">
                <div class="stats-icon"><i class="fas fa-percentage"></i></div>
                <div class="stats-info">
                    <h3>{{ $conversionRate }}%</h3>
                    <p>Conversion Rate</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stats-card gold-card">
                <div class="stats-icon"><i class="fas fa-rupee-sign"></i></div>
                <div class="stats-info">
                    <h3>₹{{ number_format($totalRevenue / 1000, 1) }}k</h3>
                    <p>Revenue Realized</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- 1. Source-wise Leads & Conversion --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-globe me-2 text-primary"></i>Source Channel Effectiveness</span>
                    <span class="badge bg-light text-muted">{{ $leadsBySource->count() }} channels</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Traffic Source</th>
                                    <th class="text-center">Leads</th>
                                    <th class="text-center">Converted</th>
                                    <th class="text-end pe-3">Conversion Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leadsBySource as $src)
                                    @php 
                                        $rate = $src->total > 0 ? round(($src->converted / $src->total) * 100, 1) : 0; 
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge badge-green">{{ ucwords(str_replace('_',' ',$src->source)) }}</span>
                                        </td>
                                        <td class="text-center fw-bold">{{ $src->total }}</td>
                                        <td class="text-center text-success fw-bold">{{ $src->converted }}</td>
                                        <td class="text-end pe-3">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <span class="small fw-bold">{{ $rate }}%</span>
                                                <div class="progress" style="width: 50px; height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: {{ min(100, $rate) }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No source records found for this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Page-wise Lead Performance --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-file-invoice me-2 text-success"></i>Website Page Performance</span>
                    <span class="badge bg-light text-muted">Top Submitted URLs</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Page URL</th>
                                    <th class="text-center">Leads</th>
                                    <th class="text-center">Converted</th>
                                    <th class="text-end pe-3">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leadsByPage as $pg)
                                    @php $share = $totalLeads > 0 ? round(($pg->total / $totalLeads) * 100, 1) : 0; @endphp
                                    <tr>
                                        <td class="font-monospace small text-truncate" style="max-width: 220px;" title="{{ $pg->page }}">
                                            {{ $pg->page }}
                                        </td>
                                        <td class="text-center fw-bold">{{ $pg->total }}</td>
                                        <td class="text-center text-success fw-bold">{{ $pg->converted }}</td>
                                        <td class="text-end pe-3">
                                            <span class="badge bg-light text-dark">{{ $share }}%</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No page tracking captured.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- 3. Most Enquired Courses & Domains --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent">
                    <span class="fw-bold"><i class="fas fa-book-reader me-2 text-warning"></i>Most Enquired Courses</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topCourses as $tc)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span class="small fw-bold text-dark">{{ $tc->domain }}</span>
                                <div>
                                    <span class="badge badge-lime me-1">{{ $tc->total }} leads</span>
                                    <span class="badge bg-success">{{ $tc->converted }} closed</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-3">No course enquiries logged.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- 4. Most Enquired Services --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent">
                    <span class="fw-bold"><i class="fas fa-laptop-code me-2 text-info"></i>Top Services Demanded</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topServices as $ts)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span class="small fw-bold text-dark">{{ $ts->service_needed }}</span>
                                <span class="badge badge-green">{{ $ts->total }} enquiries</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-3">No service requests logged.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- 5. Lost Reasons Analysis --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md);">
                <div class="card-header bg-transparent">
                    <span class="fw-bold text-danger"><i class="fas fa-user-times me-2"></i>Lost Leads Analysis</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($lostReasons as $lr)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span class="small text-secondary">{{ Str::limit($lr->lost_reason, 30) }}</span>
                                <span class="badge bg-danger text-white rounded-pill">{{ $lr->total }} leads</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-3">Zero leads marked as lost in this period!</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
       STAFF PERFORMANCE TABLE
    ══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: var(--r-md);">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="fas fa-users-cog me-2 text-success"></i>Staff Lead Conversion & Productivity</span>
            <span class="text-muted small">Department-wise Breakdown</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Staff Member</th>
                            <th>Department</th>
                            <th class="text-center">Assigned Leads</th>
                            <th class="text-center">Converted</th>
                            <th class="text-center">Lost</th>
                            <th class="text-end pe-3">Conversion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffReport as $st)
                            @php 
                                $staffRate = $st->total_assigned > 0 ? round(($st->total_converted / $st->total_assigned) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $st->name }}</div>
                                </td>
                                <td><span class="badge bg-light text-muted">{{ $st->department }}</span></td>
                                <td class="text-center fw-bold">{{ $st->total_assigned }}</td>
                                <td class="text-center text-success fw-bold">{{ $st->total_converted }}</td>
                                <td class="text-center text-danger fw-bold">{{ $st->total_lost }}</td>
                                <td class="text-end pe-3">
                                    <span class="badge bg-success">{{ $staffRate }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No staff lead metrics available for selected range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
