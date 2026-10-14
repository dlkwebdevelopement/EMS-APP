@extends('layouts.admin')

@section('title', 'Asset Dashboard')
@section('page-title', 'Asset Dashboard')
@section('page-subtitle', 'Overview of device inventory and allocations')

@section('content')
<div class="stagger-children">
    <!-- Quick Actions Bar -->
    <div class="glass-panel p-3 mb-4 d-flex align-items-center justify-content-between">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.assets.index') }}" class="btn btn-sm btn-outline-primary shadow-sm hover-up px-3 rounded-pill">
                <i class="fas fa-th-list me-1 text-xs"></i> Inventory
            </a>
            <a href="{{ route('admin.assets.allocations.create') }}" class="btn btn-sm btn-outline-success shadow-sm hover-up px-3 rounded-pill">
                <i class="fas fa-hand-holding me-1 text-xs"></i> Quick Assign
            </a>
            <a href="{{ route('admin.assets.maintenance.index') }}" class="btn btn-sm btn-outline-info shadow-sm hover-up px-3 rounded-pill">
                <i class="fas fa-tools me-1 text-xs"></i> Service Logs
            </a>
        </div>
        <a href="{{ route('admin.assets.create') }}" class="btn btn-sm btn-success shadow-sm hover-up px-3 rounded-pill">
            <i class="fas fa-plus me-1 text-xs"></i> New Device
        </a>
    </div>

    <!-- Quick Stats Row 1 -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card lime-card h-100">
                <div class="stats-icon"><i class="fas fa-boxes"></i></div>
                <div class="stats-info">
                    <h3>{{ $totalAssets }}</h3>
                    <p>Total Hardware</p>
                    <small class="text-xs opacity-50 italic">Across all categories</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card h-100 border-start border-4 border-success">
                <div class="stats-icon text-success"><i class="fas fa-check-circle"></i></div>
                <div class="stats-info">
                    <h3>{{ $availableAssets }}</h3>
                    <p>Ready to Assign</p>
                    <small class="text-xs text-success fw-bold">{{ round(($availableAssets / ($totalAssets ?: 1)) * 100) }}% Utilization Gap</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card teal-card h-100">
                <div class="stats-icon"><i class="fas fa-user-tag"></i></div>
                <div class="stats-info">
                    <h3>{{ $allocatedAssets }}</h3>
                    <p>Live Allocations</p>
                    <small class="text-xs opacity-50 italic">In use by employees</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card gold-card h-100">
                <div class="stats-icon text-warning"><i class="fas fa-tools"></i></div>
                <div class="stats-info">
                    <h3>{{ $faultyAssets + $repairingAssets }}</h3>
                    <p>In Service</p>
                    <small class="text-xs opacity-75 fw-bold italic">{{ $repairingAssets }} currently at service center</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row 2 (Detailed Metrics) -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card glass-panel border-0 shadow-sm p-4 h-100 d-flex flex-row align-items-center">
                <div class="rounded-circle bg-light-soft d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-wallet text-success"></i>
                </div>
                <div>
                    <p class="text-xs text-muted fw-bold mb-0 uppercase tracking-widest">Total Valuation</p>
                    <h4 class="mb-0 fw-bold">₹{{ number_format($totalInvestment, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card glass-panel border-0 shadow-sm p-4 h-100 d-flex flex-row align-items-center border-start border-4 border-secondary">
                <div class="rounded-circle bg-light-soft d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-archive text-secondary"></i>
                </div>
                <div>
                    <p class="text-xs text-muted fw-bold mb-0 uppercase tracking-widest">Retired/Retired</p>
                    <h4 class="mb-0 fw-bold">{{ $retiredAssets }} Assets</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="card glass-panel border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-soft-blue d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-shield-alt text-primary text-xs"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-xs text-muted fw-bold mb-0 uppercase tracking-wide">Critical Renewals</p>
                        <h6 class="mb-0">{{ count($expiringWarranty) }} Warranties Expiring Soon</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Asset Distribution Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="header-icon"><i class="fas fa-chart-pie"></i></div>
                        Category Analytics
                    </div>
                    <span class="text-xs text-muted italic">Breakdown of Inventory by Type</span>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expiring Warranty -->
        <div class="col-xl-4 col-lg-5">
            <div class="card h-100 shadow-lg border-0 overflow-hidden">
                <div class="card-header bg-soft-blue border-0 py-3">
                    <div class="header-icon text-primary"><i class="fas fa-shield-alt"></i></div>
                    Warranty Alerts <small class="text-xs opacity-50 ms-1">(Next 3 Months)</small>
                </div>
                <div class="card-body p-0">
                    @forelse($expiringWarranty as $asset)
                        <div class="d-flex align-items-center p-3 border-bottom hover-light transition">
                            <div class="bg-light-soft rounded p-2 me-3">
                                <i class="fas fa-laptop text-xs opacity-50"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-sm fw-bold">{{ $asset->brand }} {{ $asset->model }}</h6>
                                <p class="mb-0 text-xs text-muted">{{ $asset->asset_tag }}</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-soft-gold text-dark border-0 rounded-pill px-3">{{ $asset->warranty_expiry->diffForHumans() }}</span>
                                <small class="text-xs d-block text-muted mt-1">{{ $asset->warranty_expiry->format('d M') }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-shield-virus fa-3x mb-3 opacity-10"></i>
                            <p class="text-sm mb-0 fw-bold">All device warranties are up to date.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Allocations Bottom Bar -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4 pb-0">
            <div>
                <div class="header-icon text-success"><i class="fas fa-history"></i></div>
                Recent Distribution History
            </div>
            <a href="{{ route('admin.assets.allocations.index') }}" class="text-xs fw-bold text-success text-decoration-none uppercase tracking-wider">Historical Logs <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="card-body p-4 pt-1">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-soft">
                        <tr class="text-xs text-muted">
                            <th class="ps-3 py-2 border-0">Asset</th>
                            <th class="py-2 border-0">Employee</th>
                            <th class="py-2 border-0">Allocated By</th>
                            <th class="text-end pe-3 py-2 border-0">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAllocations as $allocation)
                        <tr class="text-sm border-bottom">
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light-soft rounded p-1 me-2"><i class="fas fa-desktop text-success"></i></div>
                                    <span class="fw-bold">{{ $allocation->asset->asset_tag }}</span>
                                </div>
                            </td>
                            <td>{{ $allocation->employee->name }}</td>
                            <td><span class="text-xs opacity-75">{{ $allocation->allocatedBy->name ?? 'System Admin' }}</span></td>
                            <td class="text-end pe-3">
                                <span class="badge badge-lime shadow-none border-0">{{ $allocation->allocated_at->format('d-m-Y') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($categoryDistribution->pluck('name')) !!},
            datasets: [{
                label: 'Assets',
                data: {!! json_encode($categoryDistribution->pluck('assets_count')) !!},
                backgroundColor: 'rgba(39, 163, 85, 0.6)',
                borderColor: '#27a355',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
@endsection
