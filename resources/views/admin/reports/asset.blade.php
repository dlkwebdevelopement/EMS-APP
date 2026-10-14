{{-- resources/views/admin/reports/asset.blade.php --}}
@extends('layouts.admin')

@section('title', 'Asset Report')

@section('content')
<div class="fade-in">
    {{-- Summary Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 me-3"><i class="fas fa-boxes"></i></div>
                    <div>
                        <h6 class="mb-0 text-white-50">Total Assets</h6>
                        <h4 class="mb-0 text-white fw-bold">{{ $summary['total_assets'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 me-3"><i class="fas fa-user-check"></i></div>
                    <div>
                        <h6 class="mb-0 text-white-50">Allocated</h6>
                        <h4 class="mb-0 text-white fw-bold">{{ $summary['allocated'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 me-3"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <h6 class="mb-0 text-white-50">Available</h6>
                        <h4 class="mb-0 text-white fw-bold">{{ $summary['available'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 me-3"><i class="fas fa-rupee-sign"></i></div>
                    <div>
                        <h6 class="mb-0 text-white-50">Total Valuation</h6>
                        <h4 class="mb-0 text-white fw-bold">₹{{ number_format($summary['total_cost'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-laptop me-2"></i>
                Asset Allocation & Valuation Report
            </h5>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.reports.asset') }}">
                    @csrf
                    <input type="hidden" name="category_id" value="{{ $category_id }}">
                    <input type="hidden" name="status"      value="{{ $status }}">
                    <input type="hidden" name="export"      value="csv">
                    <button type="submit" class="btn btn-success btn-sm text-white">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Asset Info</th>
                            <th>Category</th>
                            <th>Serial Number</th>
                            <th>Assigned Holder</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Purchase Cost</th>
                            <th>Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($asset->profile_photo)
                                        <img src="{{ asset('storage/' . $asset->profile_photo) }}" alt="Asset Photo" class="rounded me-2" style="width: 44px; height: 44px; object-fit: cover;">
                                    @else
                                        <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center text-secondary" style="width: 44px; height: 44px;">
                                            <i class="fas fa-laptop fs-5"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="d-block text-dark">{{ $asset->brand }} {{ $asset->model }}</strong>
                                        <span class="badge bg-secondary">Tag: {{ $asset->asset_tag }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $asset->category->name ?? 'Hardware' }}</td>
                            <td><code>{{ $asset->serial_number ?? 'N/A' }}</code></td>
                            <td>
                                @if($asset->currentAllocation && $asset->currentAllocation->employee)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle text-primary me-2 fs-5"></i>
                                        <div>
                                            <strong class="text-dark">{{ $asset->currentAllocation->employee->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $asset->currentAllocation->employee->employee_code }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-light text-secondary border">Unallocated / In Store</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($asset->status === 'allocated')
                                    <span class="badge bg-success"><i class="fas fa-user-check me-1"></i>Allocated</span>
                                @elseif($asset->status === 'available')
                                    <span class="badge bg-primary"><i class="fas fa-check-circle me-1"></i>Available</span>
                                @elseif($asset->status === 'repairing')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-tools me-1"></i>Repairing</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Faulty</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">
                                ₹{{ number_format($asset->purchase_cost ?? 0, 2) }}
                            </td>
                            <td>
                                {{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-laptop-code fs-1 d-block mb-2 text-muted"></i>
                                No assets matched the specified criteria.
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
