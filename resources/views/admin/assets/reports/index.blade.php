@extends('layouts.admin')

@section('title', 'Asset Reports')
@section('page-title', 'Inventory Analysis')
@section('page-subtitle', 'Generate hardware statements and filters')

@section('content')
<div class="stagger-children">
    <!-- Filter Section -->
    <div class="card glass-panel mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.assets.reports.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted uppercase">Category</label>
                    <select name="category_id" class="form-select border-soft">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted uppercase">Status</label>
                    <select name="status" class="form-select border-soft">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted uppercase">From Date</label>
                    <input type="date" name="date_from" class="form-control border-soft" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted uppercase">To Date</label>
                    <input type="date" name="date_to" class="form-control border-soft" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                    <a href="{{ route('admin.assets.reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary & Actions -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center">
                    <i class="fas fa-calculator me-2"></i>
                    <span class="text-xs me-2">Report Valuation:</span>
                    <span class="fw-bold fs-5">₹{{ number_format($totalCost, 2) }}</span>
                </div>
                <div class="ms-3 text-muted text-xs fw-bold italic">{{ count($assets) }} Items in this report</div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group shadow-sm text-nowrap">
                <a href="{{ route('admin.assets.reports.invoice', request()->all()) }}" target="_blank" class="btn btn-dark">
                    <i class="fas fa-file-invoice me-1 text-success"></i> Inventory Invoice
                </a>
                <a href="{{ route('admin.assets.reports.maintenance.invoice', request()->all()) }}" target="_blank" class="btn btn-outline-dark">
                    <i class="fas fa-tools me-1 text-primary"></i> Maintenance Statement
                </a>
                <a href="{{ route('admin.assets.reports.components.invoice', request()->all()) }}" target="_blank" class="btn btn-outline-dark">
                    <i class="fas fa-microchip me-1 text-warning"></i> Hardware Components
                </a>
                <button type="button" onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print me-1"></i> Print View
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-soft">
                        <tr class="text-xs text-muted uppercase tracking-wider">
                            <th class="ps-4 py-3">Asset Info</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Assigned To</th>
                            <th class="py-3">Purchase Date</th>
                            <th class="text-end pe-4 py-3">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr class="border-bottom hover-light transition">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="asset-icon-sm me-3 bg-light-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fas fa-laptop text-success text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-sm mb-0">{{ $asset->brand }} {{ $asset->model }}</div>
                                        <div class="text-xs text-muted">{{ $asset->asset_tag }} • {{ $asset->serial_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border-soft">{{ $asset->category->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'available' => 'success',
                                        'allocated' => 'info',
                                        'faulty' => 'danger',
                                        'repairing' => 'warning',
                                        'retired' => 'secondary'
                                    ][$asset->status] ?? 'primary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }} rounded-pill px-3">{{ ucfirst($asset->status) }}</span>
                            </td>
                            <td>
                                @if($asset->currentAllocation)
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-xs me-2" style="background: rgba(39,163,85,0.1); color: #27a355; width: 24px; height: 24px; font-size: 10px; display:flex; align-items:center; justify-content:center; border-radius: 50%;">
                                            {{ strtoupper(substr($asset->currentAllocation->employee->name, 0, 1)) }}
                                        </div>
                                        <div class="text-xs fw-bold">{{ $asset->currentAllocation->employee->name }}</div>
                                    </div>
                                @else
                                    <span class="text-xs text-muted italic">Available</span>
                                @endif
                            </td>
                            <td class="text-sm">
                                {{ $asset->purchase_date ? $asset->purchase_date->format('d M, Y') : 'N/A' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="fw-bold">₹{{ number_format($asset->purchase_cost, 2) }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-5 text-center text-muted">
                                <i class="fas fa-file-search fa-3x mb-3 opacity-10"></i>
                                <p class="mb-0 fw-bold">No assets found matching your filters.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
