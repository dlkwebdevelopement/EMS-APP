@extends('layouts.admin')

@section('title', 'Master Lead & Execution Report')
@section('page-title', 'Enterprise Lead & Project Reports')
@section('page-subtitle', 'Consolidated tracking across Sales conversion, Team Leader delegation, and Multi-Worker completion')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Filter bar --}}
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.leads.reports') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold mb-1">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold mb-1">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold mb-1">Team Leader (TL)</label>
                    <select name="tl_id" class="form-select form-select-sm">
                        <option value="">All TLs</option>
                        @foreach($teamLeaders as $tl)
                            <option value="{{ $tl->id }}" {{ request('tl_id') == $tl->id ? 'selected' : '' }}>{{ $tl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold mb-1">Sales Rep</label>
                    <select name="sales_id" class="form-select form-select-sm">
                        <option value="">All Sales</option>
                        @foreach($salesAgents as $sa)
                            <option value="{{ $sa->id }}" {{ request('sales_id') == $sa->id ? 'selected' : '' }}>{{ $sa->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-sm btn-success fw-bold flex-grow-1" style="margin-top: 24px;">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()" style="margin-top: 24px;" title="Print">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Highlight Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Total Inbound Leads</div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($totalLeads) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Assigned to Team Leaders</div>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($assignedToTl) }}</h3>
                <small class="text-muted">{{ $totalLeads > 0 ? round(($assignedToTl / $totalLeads) * 100, 1) : 0 }}% delegated to TLs</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Converted Closed Deals</div>
                <h3 class="fw-bold text-success mb-0">{{ number_format($converted) }}</h3>
                <small class="text-success fw-bold">{{ $totalLeads > 0 ? round(($converted / $totalLeads) * 100, 1) : 0 }}% Conversion Rate</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Total Deal Revenue</div>
                <h3 class="fw-bold text-success mb-0">₹{{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>

    {{-- Master Table --}}
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-table text-success me-2"></i> Comprehensive Lead Pipeline & Execution Matrix
            </h6>
            <span class="text-muted small">{{ $leads->total() }} Leads Logged</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Lead Code</th>
                        <th>Client / Candidate</th>
                        <th>Domain / Service</th>
                        <th>Sales Rep</th>
                        <th>Team Leader (TL)</th>
                        <th>Assigned Project Workers</th>
                        <th>Status</th>
                        <th class="text-end">Deal Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td class="small text-muted">{{ $lead->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.leads.show', $lead) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $lead->lead_code }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $lead->name }}</div>
                            <small class="text-muted">{{ $lead->mobile }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $lead->domain ?? 'General' }}</span>
                        </td>
                        <td>
                            @if($lead->salesAgent)
                                <div class="small fw-bold text-dark">{{ $lead->salesAgent->name }}</div>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->teamLeader)
                                <span class="badge bg-success text-white">
                                    <i class="fas fa-crown me-1"></i> {{ $lead->teamLeader->name }}
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">Pending TL</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->leadWorkers->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($lead->leadWorkers as $lw)
                                        <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                            {{ $lw->worker ? $lw->worker->name : 'Worker' }} ({{ $lw->status }})
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted small">None</span>
                            @endif
                        </td>
                        <td>
                            @php $b = $lead->status_badge; @endphp
                            <span class="badge {{ $b['class'] }}">{{ $b['label'] }}</span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            ₹{{ number_format($lead->deal_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">No records found for selected period</td>
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

</div>
@endsection
