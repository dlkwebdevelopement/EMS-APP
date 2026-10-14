@extends('layouts.admin')

@section('title', 'Sales Performance & Lead Reports')
@section('page-title', 'Sales & Conversion Analytics')
@section('page-subtitle', 'Track leads generated, Team Leader assignments, and completed customer deals')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Date Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('sales.reports') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold mb-1">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold mb-1">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-success fw-bold flex-grow-1" style="margin-top: 24px;">
                        <i class="fas fa-filter me-1"></i> Generate Report
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()" style="margin-top: 24px;" title="Print">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Report KPI Highlights --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Period Inbound Leads</div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($totalLeads) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Handed Over to TL</div>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($assignedToTl) }}</h3>
                <small class="text-muted">{{ $totalLeads > 0 ? round(($assignedToTl / $totalLeads) * 100, 1) : 0 }}% of pipeline</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Closed Converted Deals</div>
                <h3 class="fw-bold text-success mb-0">{{ number_format($converted) }}</h3>
                <small class="text-success fw-bold">{{ $totalLeads > 0 ? round(($converted / $totalLeads) * 100, 1) : 0 }}% Conversion</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <div class="text-muted small text-uppercase fw-bold">Revenue Realized</div>
                <h3 class="fw-bold text-success mb-0">₹{{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>

    {{-- Report Detail Table --}}
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-table text-success me-2"></i> Sales Pipeline Performance Log
            </h6>
            <span class="text-muted small">{{ $leads->total() }} Records found</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Lead Code</th>
                        <th>Candidate / Client</th>
                        <th>Domain / Service</th>
                        <th>Status</th>
                        <th>Team Leader (TL)</th>
                        <th>Assigned Workers</th>
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
                            @php $badge = $lead->status_badge; @endphp
                            <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        </td>
                        <td>
                            @if($lead->teamLeader)
                                <span class="badge bg-success text-white">
                                    <i class="fas fa-crown me-1"></i> {{ $lead->teamLeader->name }}
                                </span>
                            @else
                                <span class="text-muted small">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->leadWorkers->count() > 0)
                                <span class="badge bg-info text-white">{{ $lead->leadWorkers->count() }} Workers</span>
                            @else
                                <span class="text-muted small">0</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            ₹{{ number_format($lead->deal_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No records found for selected period</td>
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
