@extends('layouts.admin')

@section('title', 'Edit Asset: ' . $asset->asset_tag)
@section('page-title', 'Edit Device')
@section('page-subtitle', 'Updating info for ' . $asset->asset_tag)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card glass-panel fade-in-up">
            <div class="card-header border-0 pb-0">
                <div class="header-icon"><i class="fas fa-edit"></i></div>
                Update Details
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('admin.assets.update', $asset) }}" method="POST" enctype="multipart/form-data" class="stagger-children">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="glass-panel p-4 text-center mb-4 position-relative overflow-hidden">
                                <div id="image-preview-container">
                                    @if($asset->profile_photo)
                                        <img src="{{ asset('storage/' . $asset->profile_photo) }}" alt="Asset Photo" class="img-fluid rounded mb-3 shadow-sm border border-white" id="current-photo">
                                    @else
                                        <div class="upload-placeholder mb-3 py-4 bg-light-soft rounded-pill">
                                            <i class="fas fa-camera fa-2x opacity-20"></i>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-xs mt-2 fw-bold text-muted">Device Photo</p>
                                <label for="profile_photo" class="btn btn-xs btn-outline-success rounded-pill px-3 mt-2">
                                    <i class="fas fa-upload me-1"></i> Replace Image
                                </label>
                                <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*" onchange="previewDeviceImage(this)">
                            </div>
                            
                            <script>
                                function previewDeviceImage(input) {
                                    if (input.files && input.files[0]) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            const container = document.getElementById('image-preview-container');
                                            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded mb-3 shadow-sm animate-fade-in" style="max-height: 200px; object-fit: contain;">`;
                                        };
                                        reader.readAsDataURL(input.files[0]);
                                    }
                                }
                            </script>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="allocated" {{ $asset->status == 'allocated' ? 'selected' : '' }}>Allocated</option>
                                    <option value="faulty" {{ $asset->status == 'faulty' ? 'selected' : '' }}>Faulty</option>
                                    <option value="repairing" {{ $asset->status == 'repairing' ? 'selected' : '' }}>Repairing</option>
                                    <option value="retired" {{ $asset->status == 'retired' ? 'selected' : '' }}>Retired</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $asset->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Asset Tag (Internal)</label>
                                    <input type="text" name="asset_tag" class="form-control" value="{{ $asset->asset_tag }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Serial Number (S/N)</label>
                                    <input type="text" name="serial_number" class="form-control" value="{{ $asset->serial_number }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Brand</label>
                                    <input type="text" name="brand" class="form-control" value="{{ $asset->brand }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Model</label>
                                    <input type="text" name="model" class="form-control" value="{{ $asset->model }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Warranty Expiry</label>
                                    <input type="date" name="warranty_expiry" class="form-control" value="{{ $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Purchase Cost (INR)</label>
                                    <input type="number" step="0.01" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', $asset->purchase_cost) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-flex justify-content-between">
                                        <span>Purchase Bill / Invoice</span>
                                        @if($asset->bill_image)
                                            <a href="{{ $asset->public_bill_url }}" target="_blank" class="text-xs text-success fw-bold"><i class="fas fa-external-link-alt me-1"></i>View Current</a>
                                        @endif
                                    </label>
                                    <input type="file" name="bill_image" class="form-control @error('bill_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                                    <small class="text-muted text-xs">Optional: Replace original receipt (PDF, JPG, PNG)</small>
                                    @error('bill_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-5">
                        <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-primary px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5">Apply Updates</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
