@extends('layouts.admin')

@section('title', 'Asset Allocations')
@section('page-title', 'Allocation History')
@section('page-subtitle', 'Track assignments and returns of all devices')

@section('content')
<div class="stagger-children">
    <!-- Action Bar -->
    <div class="glass-panel p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-5">
                <form action="{{ route('admin.assets.allocations.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search Tag, S/N, Employee..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary px-3"><i class="fas fa-search"></i></button>
                    
                    @if(request('status') || request('search'))
                        <a href="{{ route('admin.assets.allocations.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="fas fa-times"></i></a>
                    @endif
                </form>
            </div>
            <div class="col-md-5 d-flex justify-content-center">
                <div class="btn-group shadow-sm p-1 bg-light rounded-pill" style="border: 1px solid rgba(0,0,0,0.05)">
                    <a href="{{ route('admin.assets.allocations.index') }}" class="btn btn-xs px-3 rounded-pill {{ !request('status') ? 'btn-success active' : 'btn-link text-muted text-decoration-none' }}">All</a>
                    <a href="{{ route('admin.assets.allocations.index', ['status' => 'active']) }}" class="btn btn-xs px-3 rounded-pill {{ request('status') == 'active' ? 'btn-success active' : 'btn-link text-muted text-decoration-none' }}">Active</a>
                    <a href="{{ route('admin.assets.allocations.index', ['status' => 'returned']) }}" class="btn btn-xs px-3 rounded-pill {{ request('status') == 'returned' ? 'btn-success active' : 'btn-link text-muted text-decoration-none' }}">Returned</a>
                </div>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('admin.assets.allocations.create') }}" class="btn btn-success px-4"><i class="fas fa-plus me-2"></i>New Allocation</a>
            </div>
        </div>
    </div>

    <!-- Allocations Table -->
    <div class="card p-0 overflow-hidden border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Asset & Device</th>
                        <th>Held By</th>
                        <th>Value (₹)</th>
                        <th>Lifecycle</th>
                        <th>Assigned By</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allocations as $allocation)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="asset-thumb me-3 bg-light-soft">
                                    <i class="fas fa-{{ str_contains(strtolower($allocation->asset->category->name), 'laptop') ? 'laptop' : 'tablet-alt' }} fa-lg text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-success">{{ $allocation->asset->asset_tag }}</h6>
                                    <small class="text-muted d-block text-xs uppercase tracking-wider">{{ $allocation->asset->brand }} {{ $allocation->asset->model }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="employee-avatar-sm me-2">
                                    @if($allocation->employee->profile_photo)
                                        <img src="{{ asset('storage/' . $allocation->employee->profile_photo) }}" alt="Avatar">
                                    @else
                                        {{ strtoupper(substr($allocation->employee->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <span class="fw-bold d-block">{{ $allocation->employee->name }}</span>
                                    <small class="text-muted text-xs">{{ $allocation->employee->employee_code }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-sm">₹{{ number_format($allocation->asset->purchase_cost, 2) }}</div>
                        </td>
                        <td>
                                                    <div class="text-sm fw-bold">{{ $allocation->allocated_at->format('d M, Y') }}</div>
                            @if($allocation->returned_at)
                                <div class="text-xs text-muted"><i class="fas fa-undo me-1"></i>Returned: {{ $allocation->returned_at->format('d M, Y') }}</div>
                            @else
                                <div class="text-xs text-info"><i class="fas fa-clock me-1"></i>Active since: {{ $allocation->allocated_at->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="text-xs fw-bold">{{ $allocation->allocatedBy->name ?? 'Super Admin' }}</div>
                            <small class="text-muted text-xs opacity-75 italic">Authorized</small>
                        </td>
                        <td>
                            @if(!$allocation->returned_at)
                                <span class="badge badge-lime">In Posession</span>
                            @else
                                <span class="badge badge-gray opacity-75">Returned</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                @if(!$allocation->returned_at)
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#returnModal{{ $allocation->id }}" title="Quick Return"><i class="fas fa-undo"></i></button>
                                @endif
                                <a href="{{ route('admin.assets.allocations.handover', $allocation) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print Handover Receipt"><i class="fas fa-file-invoice"></i></a>
                                <a href="{{ route('admin.assets.show', $allocation->asset) }}" class="btn btn-sm btn-outline-primary" title="View Details"><i class="fas fa-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted bg-light-soft">
                            <i class="fas fa-history fa-3x mb-3 opacity-20"></i>
                            <p class="mb-0 fw-bold">No allocation records match your search.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $allocations->links() }}
        </div>
    </div>

    <!-- Modal Section (Consolidated into single section) -->
    @foreach($allocations as $allocation)
        @if(!$allocation->returned_at)
            <div class="modal fade" id="returnModal{{ $allocation->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-glass">
                        <div class="modal-header border-0 pb-1">
                            <h5 class="modal-title fw-bold text-danger"><i class="fas fa-undo me-2"></i>Return Asset: {{ $allocation->asset->asset_tag }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.assets.allocations.return', $allocation) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4 pt-1 text-start">
                                <p class="text-xs text-muted mb-4 italic text-start">Logged allocation to <strong>{{ $allocation->employee->name }}</strong>. Please confirm device condition.</p>
                                
                                <div class="mb-3 text-start">
                                    <label class="form-label text-xs">Return Date <span class="text-danger">*</span></label>
                                    <input type="date" name="returned_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-xs">Condition on Return <span class="text-danger">*</span></label>
                                    <select name="condition_on_return" class="form-select" required>
                                        <option value="Good">Good - Minimal wear</option>
                                        <option value="Minor Damage">Minor Damage - Scratches/Dents</option>
                                        <option value="Faulty">Faulty - Needs Repair</option>
                                        <option value="Broken">Broken - Total Loss</option>
                                    </select>
                                </div>

                                <div class="mb-0 text-start">
                                    <label class="form-label text-xs">Final Remarks/Notes</label>
                                    <textarea name="remarks" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger px-4">Confirm Return</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<style>
    .asset-thumb { width: 44px; height: 44px; border-radius: var(--r-md); background: #f4fbf6; display: flex; align-items: center; justify-content: center; border: 1.5px solid rgba(126,213,58,.1); }
    .employee-avatar-sm { width: 34px; height: 34px; border-radius: 50%; background: var(--gs); color: var(--gb); display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; border: 2px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; }
    .employee-avatar-sm img { width: 100%; height: 100%; object-fit: cover; }
    .btn-xs { padding: 4px 12px; font-size: 0.75rem; font-weight: 700; transition: var(--tr); }
    .btn-group .btn-success.active { background: var(--gl) !important; border-color: var(--gl) !important; color: #fff !important; }
    .text-start { text-align: left !important; }
</style>
@endsection
