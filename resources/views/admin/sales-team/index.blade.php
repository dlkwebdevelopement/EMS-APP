@extends('layouts.admin')

@section('title', 'Sales Team Management')
@section('page-title', 'Sales Team Management')
@section('page-subtitle', 'Manage sales representatives, track pipeline performance, and control system access')

@section('content')
<div class="container-fluid px-0">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-triangle fs-5"></i>
                <div>{{ session('warning') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Top Metrics Row --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md); border-left: 4px solid #1e7c42 !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Total Sales Reps</span>
                        <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalSalesReps }}</h3>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(30, 124, 66, 0.1); color: #1e7c42;">
                        <i class="fas fa-users-cog fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md); border-left: 4px solid #10b981 !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Active Representatives</span>
                        <h3 class="fw-bold mb-0 text-success mt-1">{{ $activeSalesReps }}</h3>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-user-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md); border-left: 4px solid #3b82f6 !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Total Leads Captured</span>
                        <h3 class="fw-bold mb-0 text-primary mt-1">{{ $totalSalesLeads }}</h3>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <i class="fas fa-funnel-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: var(--r-md); border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Revenue Collected</span>
                        <h3 class="fw-bold mb-0 text-dark mt-1">₹{{ number_format($totalSalesRevenue, 0) }}</h3>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-coins fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Sales Team Card --}}
    <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md);">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="p-2 rounded-circle bg-success text-white" style="background-color: #1e7c42 !important;">
                    <i class="fas fa-briefcase"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Sales Representatives Directory</h5>
                    <span class="text-muted small">Authorized sales team accounts configured by administrator</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('admin.sales-team.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, code, phone..." value="{{ request('search') }}" style="width: 220px;">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 120px;">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.sales-team.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </form>

                <a href="{{ route('admin.sales-team.create') }}" class="btn btn-sm btn-success px-3 fw-bold text-white shadow-sm" style="background-color: #1e7c42; border-color: #1e7c42;">
                    <i class="fas fa-user-plus me-1 text-white"></i> Add Sales Member
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Representative</th>
                        <th>Contact Details</th>
                        <th>Designation</th>
                        <th class="text-center">Leads Handled</th>
                        <th class="text-center">Conversions</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesTeam as $rep)
                        @php
                            $conversionPct = $rep->total_leads_count > 0 ? round(($rep->converted_leads_count / $rep->total_leads_count) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" 
                                         style="width: 40px; height: 40px; background: linear-gradient(135deg, #1e7c42, #0d3b1a); font-size: 0.85rem;">
                                        {{ strtoupper(substr($rep->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $rep->name }}</div>
                                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.7rem;">{{ $rep->employee_code ?? 'NO-CODE' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small text-dark"><i class="fas fa-envelope text-muted me-1"></i> {{ $rep->email }}</div>
                                <div class="small text-muted"><i class="fas fa-phone-alt text-muted me-1"></i> {{ $rep->phone ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small">{{ $rep->designation ?? 'Sales Executive' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $rep->department }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill px-3">{{ $rep->total_leads_count }} leads</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success rounded-pill px-3">{{ $rep->converted_leads_count }} closed</span>
                                <div class="text-muted mt-1" style="font-size: 0.7rem;">{{ $conversionPct }}% rate</div>
                            </td>
                            <td>
                                @if($rep->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fas fa-check-circle me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fas fa-ban me-1"></i> Deactivated
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-action-group">
                                    <a href="{{ route('admin.sales-team.leads', $rep) }}" class="btn-action btn-action-view" title="View Leads Handled">
                                        <i class="fas fa-funnel-dollar"></i>
                                    </a>
                                    <a href="{{ route('admin.sales-team.edit', $rep) }}" class="btn-action btn-action-edit" title="Edit Representative">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.sales-team.toggle-status', $rep) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-action {{ $rep->is_active ? 'btn-action-call' : 'btn-action-view' }}" 
                                                title="{{ $rep->is_active ? 'Deactivate Account' : 'Activate Account' }}"
                                                onclick="return confirm('Toggle status for {{ $rep->name }}?')">
                                            <i class="fas {{ $rep->is_active ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.sales-team.destroy', $rep) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete or deactivate {{ $rep->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-action-delete" title="Delete Representative">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-user-slash fs-1 text-secondary mb-3 d-block"></i>
                                <p class="mb-2 fw-semibold">No sales representatives found.</p>
                                <a href="{{ route('admin.sales-team.create') }}" class="btn btn-sm btn-success text-white fw-bold shadow-sm" style="background-color: #1e7c42; border-color: #1e7c42;">
                                    <i class="fas fa-plus me-1 text-white"></i> Add First Sales Executive
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salesTeam->hasPages())
            <div class="card-footer bg-transparent py-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted">Showing {{ $salesTeam->firstItem() }} to {{ $salesTeam->lastItem() }} of {{ $salesTeam->total() }} sales reps</span>
                {{ $salesTeam->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
