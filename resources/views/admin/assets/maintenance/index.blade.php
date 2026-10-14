@extends('layouts.admin')

@section('title', 'Maintenance Logs')
@section('page-title', 'Service Records')
@section('page-subtitle', 'Track repairs and maintenance for all assets')

@section('content')
<div class="stagger-children">
    <!-- Action Bar -->
    <div class="glass-panel p-4 mb-4 d-flex align-items-center justify-content-between">
        <div class="row w-100 align-items-center">
            <div class="col-md-5">
                <form action="{{ route('admin.assets.maintenance.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search Tag, S/N, Issue..." value="{{ request('search') }}">
                    <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary px-3"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="col-md-7 text-end">
                <a href="{{ route('admin.assets.maintenance.create') }}" class="btn btn-success px-4"><i class="fas fa-plus me-2"></i>New Repair Log</a>
            </div>
        </div>
    </div>

    <!-- Maintenance Table -->
    <div class="card p-0 overflow-hidden border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Asset Tag</th>
                        <th>Device Info</th>
                        <th>Issue</th>
                        <th>Service Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenances as $maintenance)
                    <tr>
                        <td class="ps-4"><span class="fw-bold text-success">{{ $maintenance->asset->asset_tag }}</span></td>
                        <td>{{ $maintenance->asset->brand }} {{ $maintenance->asset->model }}</td>
                        <td>
                            <div class="text-sm fw-bold">{{ \Illuminate\Support\Str::limit($maintenance->issue_description, 40) }}</div>
                            <small class="text-muted">{{ $maintenance->service_provider ?? 'Internal' }}</small>
                        </td>
                        <td>{{ $maintenance->maintenance_at->format('d M, Y') }}</td>
                        <td>
                            @php
                                $statusBadge = [
                                    'pending' => 'badge-gold',
                                    'in_progress' => 'badge-blue',
                                    'completed' => 'badge-lime',
                                    'cancelled' => 'badge-gray'
                                ][$maintenance->status] ?? 'badge-red';
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ ucfirst($maintenance->status) }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                @if($maintenance->status == 'pending')
                                    <form action="{{ route('admin.assets.maintenance.receive', $maintenance) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Mark as Received"><i class="fas fa-sign-in-alt"></i></button>
                                    </form>
                                @endif

                                @if($maintenance->status == 'pending' || $maintenance->status == 'in_progress')
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#completeModal{{ $maintenance->id }}" title="Complete Repair"><i class="fas fa-check-double"></i></button>
                                    
                                    <form action="{{ route('admin.assets.maintenance.cancel', $maintenance) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Cancel Repair" onclick="return confirm('Are you sure?')"><i class="fas fa-times"></i></button>
                                    </form>
                                @endif
                                @if($maintenance->status == 'completed' && $maintenance->bill_image)
                                    <a href="{{ $maintenance->public_bill_url }}" target="_blank" class="btn btn-sm btn-outline-info" title="View Bill"><i class="fas fa-file-invoice-dollar"></i></a>
                                @endif
                                
                                <a href="{{ route('admin.assets.show', $maintenance->asset) }}" class="btn btn-sm btn-outline-primary" title="View Device"><i class="fas fa-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted bg-light-soft">
                            <i class="fas fa-tools fa-3x mb-3 opacity-20"></i>
                            <p class="mb-0 fw-bold">No maintenance logs found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $maintenances->links() }}
        </div>
    </div>

    <!-- Modal Section (Placed inside content to avoid multi-section Blade issues) -->
    @foreach($maintenances as $maintenance)
        @if($maintenance->status == 'pending' || $maintenance->status == 'in_progress')
            <div class="modal fade" id="completeModal{{ $maintenance->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-glass text-start">
                        <div class="modal-header border-0 pb-1">
                            <h5 class="modal-title fw-bold text-success">
                                <i class="fas fa-check-double me-2"></i>Finalize Repair: {{ $maintenance->asset->asset_tag }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.assets.maintenance.complete', $maintenance) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body p-4 pt-1">
                                <p class="text-xs text-muted mb-4 italic text-start">Confirm the resolution details to return this device to available inventory.</p>
                                
                                <div class="mb-3 text-start">
                                    <label class="form-label text-xs">Completion Date <span class="text-danger">*</span></label>
                                    <input type="date" name="completion_at" class="form-control @error('completion_at') is-invalid @enderror" value="{{ old('completion_at', date('Y-m-d')) }}" required>
                                    @error('completion_at') <div class="invalid-feedback text-xs">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-xs">Action Taken <span class="text-danger">*</span></label>
                                    <textarea name="action_taken" class="form-control @error('action_taken') is-invalid @enderror" rows="3" required placeholder="Describe what was fixed...">{{ old('action_taken') }}</textarea>
                                    @error('action_taken') <div class="invalid-feedback text-xs">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-xs">Total Service Cost (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light-soft border-end-0">₹</span>
                                        <input type="number" step="0.01" name="cost" class="form-control border-start-0 @error('cost') is-invalid @enderror" value="{{ old('cost', '0.00') }}" required>
                                    </div>
                                    @error('cost') <div class="invalid-feedback text-xs d-block text-start">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-0 text-start">
                                    <label class="form-label text-xs">Service Bill/Receipt <small class="text-muted">(Optional)</small></label>
                                    <input type="file" name="bill_image" class="form-control form-control-sm @error('bill_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                                    @error('bill_image') <div class="invalid-feedback text-xs d-block text-start">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success px-4">Complete Repair</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection
