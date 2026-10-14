@extends('layouts.admin')

@section('title', 'Log Asset Maintenance')
@section('page-title', 'Maintenance Record')
@section('page-subtitle', 'Log a new service or repair for an asset')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card glass-panel fade-in-up">
            <div class="card-header border-0 pb-0">
                <div class="header-icon"><i class="fas fa-wrench"></i></div>
                New Service Entry
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('admin.assets.maintenance.store') }}" method="POST" class="stagger-children">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <label class="form-label">Asset to Service</label>
                            <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                <option value="">Select Asset</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ ($selectedAsset && $selectedAsset->id == $asset->id) ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->brand }} {{ $asset->model }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Service Date</label>
                            <input type="date" name="maintenance_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Service Provider / Vendor</label>
                            <input type="text" name="service_provider" class="form-control" placeholder="e.g. Authorized Service Center">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Issue Description</label>
                            <textarea name="issue_description" class="form-control" rows="4" placeholder="Describe the fault or service needed..." required></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-primary px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-success px-5">Log Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
