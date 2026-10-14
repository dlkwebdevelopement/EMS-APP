@extends('layouts.admin')

@section('title', 'Assign Asset')
@section('page-title', 'Asset Allocation')
@section('page-subtitle', 'Assign hardware to a specific employee')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card glass-panel fade-in-up">
            <div class="card-header border-0 pb-0">
                <div class="header-icon"><i class="fas fa-hand-holding"></i></div>
                New Allocation
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('admin.assets.allocations.store') }}" method="POST" class="stagger-children" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <label class="form-label">Asset to Assign</label>
                            <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                <option value="">Select Available Asset</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ ($selectedAsset && $selectedAsset->id == $asset->id) ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->brand }} {{ $asset->model }} ({{ $asset->serial_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                <option value="">Select Employee</option>
                                @php $preSelected = $selectedEmployee->id ?? old('employee_id'); @endphp
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $preSelected == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Allocation Date</label>
                            <input type="date" name="allocated_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Device Module Section (Hardware Components) -->
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-microchip me-2 text-primary"></i>
                                <span class="fw-bold text-sm">Device Module (Internal Parts Tracking)</span>
                            </div>
                            <div class="glass-panel p-3 border-soft shadow-none bg-light-soft">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-xs">Part/Component Name</label>
                                        <select name="component_name" class="form-select form-select-sm">
                                            <option value="">-- No New Part Added --</option>
                                            <option value="Main Battery">Main Battery</option>
                                            <option value="Internal RAM">Internal RAM</option>
                                            <option value="SSD/Storage">SSD/Storage</option>
                                            <option value="Keyboard">Keyboard</option>
                                            <option value="Internal Screen">Internal Screen</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs">Part Serial Number</label>
                                        <input type="text" name="component_serial" class="form-control form-control-sm" placeholder="e.g. SN-BAT-123">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs">Brand</label>
                                        <input type="text" name="component_brand" class="form-control form-control-sm" placeholder="e.g. Dell, HP, Samsung">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs">Model</label>
                                        <input type="text" name="component_model" class="form-control form-control-sm" placeholder="e.g. 6-Cell 56Wh">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs text-primary fw-bold">Part Cost (₹)</label>
                                        <input type="number" name="component_cost" class="form-control form-control-sm border-primary-soft shadow-none" placeholder="e.g. 4500" step="0.01">
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 11px; background: rgba(13,110,253,0.05); border: none;">
                                            <i class="fas fa-info-circle me-1"></i> Adding a new part will automatically move any existing part with the same name to <strong>History/Decommissioned</strong>.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Condition on Allocation</label>
                            <select name="condition_on_allocation" class="form-select" required>
                                <option value="Brand New">Brand New</option>
                                <option value="Excellent">Excellent</option>
                                <option value="Good">Good</option>
                                <option value="Used/Fair">Used/Fair</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-primary px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-success px-5">Assign Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
