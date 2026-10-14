@extends('layouts.admin')

@section('title', 'Add New Asset')
@section('page-title', 'New Device Entry')
@section('page-subtitle', 'Register a new hardware item into the inventory')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card glass-panel fade-in-up">
            <div class="card-header border-0 pb-0">
                <div class="header-icon"><i class="fas fa-plus"></i></div>
                Asset Details
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('admin.assets.store') }}" method="POST" enctype="multipart/form-data" class="stagger-children">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Left Column: Image/Main Info -->
                        <div class="col-md-4">
                            <div class="glass-panel p-4 text-center mb-4 position-relative overflow-hidden">
                                <div id="image-preview-container" class="upload-placeholder mb-3 py-4 bg-light-soft rounded-pill">
                                    <i class="fas fa-camera fa-2x opacity-20"></i>
                                    <p class="text-xs mt-2 fw-bold text-muted">Upload Device Photo</p>
                                </div>
                                <label for="profile_photo" class="btn btn-xs btn-outline-success rounded-pill px-3 mt-2">
                                    <i class="fas fa-upload me-1"></i> Selection Image
                                </label>
                                <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*" onchange="previewDeviceImage(this)">
                                <small class="text-muted d-block mt-2">Max size: 2MB (JPG/PNG)</small>
                            </div>

                            <script>
                                function previewDeviceImage(input) {
                                    if (input.files && input.files[0]) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            const container = document.getElementById('image-preview-container');
                                            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded mb-3 shadow-sm animate-fade-in" style="max-height: 200px; object-fit: contain;">`;
                                            container.classList.remove('py-4');
                                        };
                                        reader.readAsDataURL(input.files[0]);
                                    }
                                }
                            </script>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Right Column: Specs & Serial -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Asset Tag (Internal)</label>
                                        <input type="text" name="asset_tag" class="form-control @error('asset_tag') is-invalid @enderror" value="{{ old('asset_tag') }}" placeholder="e.g. LAP-2024-001" required>
                                        @error('asset_tag') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Serial Number (S/N)</label>
                                        <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}" required>
                                        @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Brand</label>
                                        <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" placeholder="e.g. Apple, Dell" required>
                                        @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Model</label>
                                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}" placeholder="e.g. MacBook Pro M3" required>
                                        @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Purchase Date</label>
                                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Warranty Expiry</label>
                                        <input type="date" name="warranty_expiry" class="form-control" value="{{ old('warranty_expiry') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Purchase Cost (INR)</label>
                                        <input type="number" step="0.01" name="purchase_cost" class="form-control" value="{{ old('purchase_cost') }}" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Purchase Bill / Invoice</label>
                                        <input type="file" name="bill_image" class="form-control @error('bill_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                                        <small class="text-muted text-xs">Optional: Upload original receipt (PDF, JPG, PNG)</small>
                                        @error('bill_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="text-end">
                        <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-primary px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-success px-5">Register Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .upload-placeholder {
        border: 2px dashed rgba(126, 213, 58, 0.25);
        border-radius: 18px;
        padding: 3rem 1rem;
        position: relative;
        transition: var(--tr);
    }
    .upload-placeholder:hover {
        background: var(--gs);
        border-color: var(--gl);
    }
</style>
@endsection
