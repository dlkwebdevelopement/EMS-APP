@extends('layouts.admin')

@section('title', 'Device Inventory')
@section('page-title', 'Device Inventory')
@section('page-subtitle', 'Manage all IT assets and device distributions')

@section('content')
<div class="stagger-children">
    <!-- Action Bar -->
    <div class="glass-panel p-4 mb-4 d-flex align-items-center justify-content-between">
        <div class="row w-100 align-items-center">
            <div class="col-md-5">
                <form action="{{ route('admin.assets.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search Tag, S/N, Model..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="col-md-5">
                <div class="d-flex gap-2">
                    <select name="category_id" class="form-select" onchange="window.location.href='{{ route('admin.assets.index') }}?category_id='+this.value">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-select" onchange="window.location.href='{{ route('admin.assets.index') }}?status='+this.value">
                        <option value="">All Statuses</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="allocated" {{ request('status') == 'allocated' ? 'selected' : '' }}>Allocated</option>
                        <option value="faulty" {{ request('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                        <option value="repairing" {{ request('status') == 'repairing' ? 'selected' : '' }}>Repairing</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('admin.assets.create') }}" class="btn btn-success w-100"><i class="fas fa-plus me-2"></i>Add Asset</a>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Asset Tag</th>
                        <th>Device Info</th>
                        <th>Category</th>
                        <th>Cost (₹)</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr onclick="window.location.href='{{ route('admin.assets.show', $asset) }}'" style="cursor: pointer">
                        <td><span class="fw-bold text-success">{{ $asset->asset_tag }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="asset-thumb me-3">
                                    @if($asset->profile_photo)
                                        <img src="{{ asset('storage/' . $asset->profile_photo) }}" alt="Asset" class="w-100 h-100 rounded-lg shadow-sm" style="object-fit: cover;">
                                    @else
                                        <i class="fas fa-{{ str_contains(strtolower($asset->category->name), 'laptop') ? 'laptop' : 'tablet-alt' }} fa-lg opacity-40"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $asset->brand }} {{ $asset->model }}</h6>
                                    <small class="text-muted">SN: {{ $asset->serial_number }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $asset->category->name }}</td>
                        <td class="fw-bold">₹{{ number_format($asset->purchase_cost, 2) }}</td>
                        <td>
                                                    @php
                                $statusBadge = [
                                    'available' => 'badge-lime',
                                    'allocated' => 'badge-green',
                                    'faulty' => 'badge-gold',
                                    'repairing' => 'badge-gold',
                                    'retired' => 'badge-red'
                                ][$asset->status] ?? 'badge-red';
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ ucfirst($asset->status) }}</span>
                        </td>
                        <td>
                            @if($asset->currentAllocation)
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar user-avatar-xs me-2">
                                        {{ strtoupper(substr($asset->currentAllocation->employee->name, 0, 1)) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $asset->currentAllocation->employee->name }}</span>
                                        <small class="text-xs text-muted">{{ $asset->currentAllocation->employee->department }} | {{ $asset->currentAllocation->employee->designation }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-muted italic">Not Assigned</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('admin.assets.destroy', $asset) }}', '{{ $asset->asset_tag }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 opacity-20"></i>
                            <p>No assets found matching the criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $assets->links() }}
        </div>
    </div>
</div>

<style>
    .asset-thumb {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: var(--gs);
        display: flex; align-items: center; justify-content: center;
        border: 1px solid rgba(126, 213, 58, 0.2);
    }
    .user-avatar-xs { width: 26px; height: 26px; font-size: 0.65rem; }
</style>
@endsection
