@extends('layouts.admin')

@section('title', 'Asset Details: ' . $asset->asset_tag)
@section('page-title', 'Device Details')
@section('page-subtitle', 'Comprehensive view for Asset Tag: ' . $asset->asset_tag)

@section('content')
<div class="row g-4 stagger-children">
    <!-- Left Column: Details & QR -->
    <div class="col-lg-4">
        <div class="card glass-panel text-center p-4">
            <div class="asset-profile-photo mb-3">
                @if($asset->profile_photo)
                    <img src="{{ asset('storage/' . $asset->profile_photo) }}" alt="Asset Photo" class="img-fluid rounded-lg shadow-sm">
                @else
                    <div class="display-1 text-muted opacity-10">
                        <i class="fas fa-{{ str_contains(strtolower($asset->category->name), 'laptop') ? 'laptop' : 'tablet-alt' }}"></i>
                    </div>
                @endif
            </div>
            
            <h4 class="fw-bold mb-1">{{ $asset->brand }} {{ $asset->model }}</h4>
            <span class="badge badge-lime mb-4">{{ $asset->category->name }}</span>
            <hr class="opacity-10">
            
            <div class="text-start mt-4">
                <p class="mb-1 text-xs text-muted fw-bold">ASSET TAG</p>
                <h5 class="mb-3">{{ $asset->asset_tag }}</h5>
                
                <p class="mb-1 text-xs text-muted fw-bold">SERIAL NUMBER</p>
                <h5 class="mb-3 text-break small">{{ $asset->serial_number }}</h5>
                
                <p class="mb-1 text-xs text-muted fw-bold">WARRANTY UNTIL</p>
                <h5 class="mb-3 text-success">{{ $asset->warranty_expiry ? $asset->warranty_expiry->format('d M, Y') : 'N/A' }}</h5>

                @if($asset->bill_image)
                    <p class="mb-1 text-xs text-muted fw-bold">ORIGINAL INVOICE</p>
                    <h5 class="mb-0">
                        <a href="{{ $asset->public_bill_url }}" target="_blank" class="btn btn-xs btn-outline-success rounded-pill px-3 shadow-none">
                            <i class="fas fa-file-invoice me-1"></i>View Purchase Bill
                        </a>
                    </h5>
                @endif
            </div>
            
            @if($asset->status == 'available')
                <div class="mt-4 d-grid">
                    <a href="{{ route('admin.assets.allocations.create', ['asset_id' => $asset->id]) }}" class="btn btn-success">Assign to Employee</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: History & Status -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4 pb-0">
                <nav>
                    <div class="nav nav-pills" id="asset-tab" role="tablist">
                        <button class="nav-link active me-2 px-4 shadow-sm" id="nav-alloc-tab" data-bs-toggle="tab" data-bs-target="#nav-alloc" type="button" role="tab"><i class="fas fa-history me-2"></i>Allocation Log</button>
                        <button class="nav-link px-4 shadow-sm" id="nav-maint-tab" data-bs-toggle="tab" data-bs-target="#nav-maint" type="button" role="tab"><i class="fas fa-wrench me-2"></i>Service History</button>
                        <button class="nav-link px-4 shadow-sm ms-2" id="nav-comp-tab" data-bs-toggle="tab" data-bs-target="#nav-comp" type="button" role="tab"><i class="fas fa-microchip me-2"></i>Hardware Components</button>
                    </div>
                </nav>
                <div class="status-indicator d-flex align-items-center bg-light px-3 py-1 rounded-pill">
                    <span class="status-dot me-2 bg-{{ $asset->status == 'available' ? 'success' : ($asset->status == 'allocated' ? 'info' : 'warning') }}"></span>
                    <span class="text-xs fw-bold">{{ strtoupper($asset->status) }}</span>
                </div>
            </div>

            <div class="card-body p-4 pt-1">
                <div class="tab-content mt-4" id="nav-tabContent">
                    <!-- Allocation Log Tab -->
                    <div class="tab-pane fade show active" id="nav-alloc" role="tabpanel">
                        @php $currentAllocation = $asset->allocations->where('returned_at', null)->first(); @endphp
                        
                        @if($currentAllocation)
                            <div class="glass-panel p-4 mb-4 border-start border-4 border-success d-flex align-items-center">
                                <div class="employee-avatar-lg me-4">
                                    @if($currentAllocation->employee->profile_photo)
                                        <img src="{{ asset('storage/' . $currentAllocation->employee->profile_photo) }}" alt="Profile">
                                    @else
                                        {{ strtoupper(substr($currentAllocation->employee->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold text-success">Current Holder</h5>
                                    <h4 class="mb-1">{{ $currentAllocation->employee->name }}</h4>
                                    <div class="d-flex flex-wrap gap-3 text-sm opacity-75">
                                        <span><i class="fas fa-id-badge me-1"></i> {{ $currentAllocation->employee->employee_code }}</span>
                                        <span><i class="fas fa-sitemap me-1"></i> {{ $currentAllocation->employee->department }}</span>
                                        <span><i class="fas fa-phone me-1"></i> {{ $currentAllocation->employee->phone }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <h6 class="mb-4 fw-bold text-muted uppercase tracking-wider text-xs">Allocation History</h6>

                        @forelse($asset->allocations->sortByDesc('allocated_at') as $allocation)
                        <div class="timeline-item border-start border-3 border-success ps-4 pb-4 position-relative">
                            <i class="fas fa-user-check timeline-dot text-success position-absolute"></i>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold">Assigned to: {{ $allocation->employee->name }}</h6>
                                    <p class="mb-1 text-sm opacity-75">Condition: {{ $allocation->condition_on_allocation }}</p>
                                    <p class="mb-0 text-xs text-muted">By: {{ $allocation->allocatedBy->name ?? 'Super Admin' }} on {{ $allocation->allocated_at->format('d M, Y') }}</p>
                                    @if($allocation->returned_at)
                                        <p class="mt-2 mb-0 text-xs text-muted italic border-top pt-2">Returned: {{ $allocation->returned_at->format('d M, Y') }}. Condition: {{ $allocation->condition_on_return }}</p>
                                    @endif
                                </div>
                                @if(!$allocation->returned_at)
                                    <button class="btn btn-sm btn-outline-danger shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#returnModal{{ $allocation->id }}">Return Asset</button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 opacity-40">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <p>No history found.</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Service History Tab -->
                    <div class="tab-pane fade" id="nav-maint" role="tabpanel">
                        <div class="text-end mb-4">
                            <a href="{{ route('admin.assets.maintenance.create', ['asset_id' => $asset->id]) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-1"></i>Add Repair Log</a>
                        </div>
                        @forelse($asset->maintenanceRecords->sortByDesc('maintenance_at') as $maint)
                        <div class="maintenance-box p-3 border-start border-3 border-{{ $maint->status == 'completed' ? 'success' : ($maint->status == 'in_progress' ? 'info' : ($maint->status == 'cancelled' ? 'secondary' : 'warning')) }} mb-3 bg-light-soft rounded-end">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-bold">{{ $maint->service_provider ?? 'Internal Service' }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $maint->status == 'completed' ? 'badge-lime' : ($maint->status == 'in_progress' ? 'badge-blue' : ($maint->status == 'cancelled' ? 'badge-gray' : 'badge-gold')) }}">{{ ucfirst($maint->status) }}</span>
                                    
                                    @if($maint->status == 'pending' || $maint->status == 'in_progress')
                                        <button class="btn btn-xs btn-outline-success" data-bs-toggle="modal" data-bs-target="#completeMaintModal{{ $maint->id }}">Complete</button>
                                    @endif
                                </div>
                            </div>
                            <p class="text-sm mb-1"><strong>Issue:</strong> {{ $maint->issue_description }}</p>
                            @if($maint->action_taken)
                                <p class="text-sm border-top mt-2 pt-2 mb-0"><strong>Resolution:</strong> {{ $maint->action_taken }} (₹{{ number_format($maint->cost, 2) }})</p>
                            @endif
                            <small class="text-xs text-muted d-block mt-2">Logged: {{ $maint->maintenance_at->format('d M, Y') }}</small>
                        </div>
                        @empty
                        <div class="text-center py-5 opacity-40">
                            <i class="fas fa-tools fa-2x mb-2"></i>
                            <p>No records found.</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Hardware Components Tab -->
                    <div class="tab-pane fade" id="nav-comp" role="tabpanel">
                        <div class="text-end mb-4">
                            <button class="btn btn-sm btn-outline-success px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPartModal">
                                <i class="fas fa-plus-circle me-1"></i>Attach / Replace Part
                            </button>
                        </div>

                        @php $activeComponents = $asset->components->where('is_active', true); @endphp
                        @if($activeComponents->count() > 0)
                            <h6 class="mb-3 fw-bold text-success text-xs uppercase tracking-wider"><i class="fas fa-check-circle me-1"></i>Active Components</h6>
                            <div class="row g-3 mb-4">
                                @foreach($activeComponents as $comp)
                                <div class="col-md-6">
                                    <div class="glass-panel p-3 border-start border-4 border-success h-100 position-relative">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark">{{ $comp->name }}</h6>
                                                <div class="text-xs text-muted mb-2">Model: {{ $comp->model ?? 'N/A' }}</div>
                                            </div>
                                            @if($comp->bill_image)
                                                <a href="{{ asset('storage/' . $comp->bill_image) }}" target="_blank" class="btn btn-xs btn-outline-info rounded-pill px-2">
                                                    <i class="fas fa-file-invoice me-1"></i>Bill
                                                </a>
                                            @endif
                                        </div>
                                        <div class="text-sm border-top pt-2 mt-1">
                                            <div class="mb-1"><span class="opacity-50">S/N:</span> <span class="fw-bold">{{ $comp->serial_number ?? 'N/A' }}</span></div>
                                            <div class="text-xs {{ $comp->warranty_expiry && $comp->warranty_expiry->isPast() ? 'text-danger' : 'text-success' }}">
                                                <i class="fas fa-shield-alt me-1"></i>Warranty: {{ $comp->warranty_expiry ? $comp->warranty_expiry->format('d M, Y') : 'Life-time' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        @php $historyComponents = $asset->components->where('is_active', false); @endphp
                        @if($historyComponents->count() > 0)
                            <h6 class="mb-3 fw-bold text-muted text-xs uppercase tracking-wider mt-4"><i class="fas fa-history me-1"></i>Replacement History</h6>
                            <div class="table-responsive rounded-lg border">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr class="text-xs text-muted">
                                            <th class="ps-3 py-2">Part Name</th>
                                            <th class="py-2">Model / SN</th>
                                            <th class="py-2">Purchase</th>
                                            <th class="py-2">Cost</th>
                                            <th class="text-end pe-3 py-2">Doc</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($historyComponents as $hcomp)
                                        <tr class="text-xs align-middle">
                                            <td class="ps-3 fw-bold">{{ $hcomp->name }}</td>
                                            <td>{{ $hcomp->model }} <span class="text-muted text-xs">/ {{ $hcomp->serial_number }}</span></td>
                                            <td>{{ $hcomp->purchase_date ? $hcomp->purchase_date->format('d M, Y') : 'N/A' }}</td>
                                            <td class="fw-bold">₹{{ number_format($hcomp->cost, 2) }}</td>
                                            <td class="text-end pe-3">
                                                @if($hcomp->bill_image)
                                                    <a href="{{ asset('storage/' . $hcomp->bill_image) }}" target="_blank" class="text-info"><i class="fas fa-external-link-alt"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if($asset->components->count() == 0)
                            <div class="text-center py-5 opacity-40">
                                <i class="fas fa-microchip fa-3x mb-3 text-success"></i>
                                <p class="fw-bold">No hardware components tracked yet.</p>
                                <p class="text-xs px-5">Use the button above to register batteries, RAM sticks, or other internal modules.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals (Consolidated) -->
    @foreach($asset->allocations as $allocation)
        @if(!$allocation->returned_at)
            <div class="modal fade" id="returnModal{{ $allocation->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-glass">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-danger"><i class="fas fa-undo me-2"></i>Return Asset</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.assets.allocations.return', $allocation) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4 text-start">
                                <div class="mb-3">
                                    <label class="form-label text-xs">Return Date</label>
                                    <input type="date" name="returned_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-xs">Condition on Return</label>
                                    <select name="condition_on_return" class="form-select" required>
                                        <option value="Good">Good</option>
                                        <option value="Minor Damage">Minor Damage</option>
                                        <option value="Faulty">Faulty - Requires Repair</option>
                                        <option value="Broken">Broken</option>
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label text-xs">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-link text-muted text-decoration-none px-4" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger px-4 shadow-sm">Confirm Return</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @foreach($asset->maintenanceRecords as $maint)
        @if($maint->status == 'pending' || $maint->status == 'in_progress')
            <div class="modal fade" id="completeMaintModal{{ $maint->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-glass">
                        <div class="modal-header border-0 pb-1">
                            <h5 class="modal-title fw-bold text-success">
                                <i class="fas fa-check-double me-2"></i>Finalize Repair: {{ $asset->asset_tag }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.assets.maintenance.complete', $maint) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4 pt-1 text-start">
                                <div class="mb-3">
                                    <label class="form-label text-xs">Completion Date <span class="text-danger">*</span></label>
                                    <input type="date" name="completion_at" class="form-control @error('completion_at') is-invalid @enderror" value="{{ old('completion_at', date('Y-m-d')) }}" required>
                                    @error('completion_at') <div class="invalid-feedback text-xs">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-xs">Action Taken <span class="text-danger">*</span></label>
                                    <textarea name="action_taken" class="form-control @error('action_taken') is-invalid @enderror" rows="2" required placeholder="Describe the resolution...">{{ old('action_taken') }}</textarea>
                                    @error('action_taken') <div class="invalid-feedback text-xs">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-0">
                                    <label class="form-label text-xs">Service Cost (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light-soft border-end-0" style="border-radius: var(--r-pill) 0 0 var(--r-pill); border-color: rgba(126,213,58,.25);">₹</span>
                                        <input type="number" step="0.01" name="cost" class="form-control border-start-0 @error('cost') is-invalid @enderror" value="{{ old('cost', '0.00') }}" required style="border-radius: 0 var(--r-pill) var(--r-pill) 0;">
                                    </div>
                                    @error('cost') <div class="invalid-feedback text-xs d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success px-4 shadow-sm">Complete Repair</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Add Hardware Part Modal -->
    <div class="modal fade" id="addPartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-glass">
                <div class="modal-header border-0 pb-1">
                    <h5 class="modal-title fw-bold text-success">
                        <i class="fas fa-microchip me-2"></i>Register Hardware Component
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.assets.components.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                    <div class="modal-body p-4 pt-1 text-start">
                        <p class="text-xs text-muted mb-4 italic">Add parts like Battery, RAM, or SSD to this asset. Existing parts with the same name will be moved to history.</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-xs">Part Name <span class="text-danger">*</span></label>
                                <select name="name" class="form-select @error('name') is-invalid @enderror" required>
                                    <option value="Main Battery">Main Battery</option>
                                    <option value="RAM Memory">RAM Memory</option>
                                    <option value="Storage (SSD/HDD)">Storage (SSD/HDD)</option>
                                    <option value="Display Panel">Display Panel</option>
                                    <option value="Keyboard">Keyboard</option>
                                    <option value="Charger/Adapter">Charger/Adapter</option>
                                    <option value="Other Component">Other Component</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-xs">Serial Number</label>
                                <input type="text" name="serial_number" class="form-control" placeholder="Optional S/N">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-xs">Brand</label>
                                <input type="text" name="brand" class="form-control" placeholder="e.g. Dell, Samsung">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-xs">Model</label>
                                <input type="text" name="model" class="form-control" placeholder="e.g. L-500 EVO">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-xs">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-xs">Warranty Expiry</label>
                                <input type="date" name="warranty_expiry" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-xs">Cost (₹)</label>
                                <input type="number" step="0.01" name="cost" class="form-control" placeholder="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-xs">Bill Image / Receipt</label>
                                <input type="file" name="bill_image" class="form-control" accept="image/*">
                                <small class="text-muted text-xs">Max size: 2MB (JPEG, PNG)</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-xs">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2" placeholder="Any extra details..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 shadow-sm">Register Component</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline-dot { left: -11px; top: 0; background: #fff; width: 22px; height: 22px; border: 3px solid #f4fbf6; border-radius: 50%; font-size: 0.6rem; display: flex; align-items: center; justify-content: center; z-index: 1; }
    .status-dot { width: 10px; height: 10px; border-radius: 50%; }
    .nav-pills .nav-link { border-radius: var(--r-pill); color: var(--txt-m); background: #f8f9fa; font-weight: 700; font-size: 0.8rem; border: 1px solid #eee; transition: var(--tr); }
    .nav-pills .nav-link.active { background: linear-gradient(135deg, var(--gl), #5ab020) !important; color: var(--gd) !important; border-color: transparent !important; }
    .asset-profile-photo { height: 180px; display: flex; align-items: center; justify-content: center; background: #f8fbf9; border-radius: var(--r-md); overflow: hidden; }
    .employee-avatar-lg { width: 64px; height: 64px; border-radius: 50%; background: var(--gs); color: var(--gb); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; border: 3px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
    .employee-avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
    .uppercase { text-transform: uppercase; }
    .tracking-wider { letter-spacing: 0.1em; }
    .text-start { text-align: left !important; }
    .italic { font-style: italic; }
</style>
@endsection
