@extends('layouts.admin')

@section('title', 'Add Project')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add Project</h2>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.projects.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Client</label>
                        <select name="client_id" class="form-control" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Assign Employees</label>
                        <div>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#employeeSelectModal">
                                <i class="fas fa-users"></i> Select Employees
                            </button>
                            <span id="selectedCount" class="ms-2 text-muted">0 selected</span>
                        </div>
                        
                        <!-- Hidden select to actually submit the data -->
                        <select name="employee_ids[]" id="hiddenEmployeeSelect" multiple style="display: none;">
                            @php $oldEmployees = old('employee_ids', []); @endphp
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" id="hidden-emp-{{ $employee->id }}" {{ in_array($employee->id, $oldEmployees) ? 'selected' : '' }}></option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Employee Select Modal -->
                    <div class="modal fade" id="employeeSelectModal" tabindex="-1" aria-labelledby="employeeSelectModalLabel" aria-hidden="true" data-bs-backdrop="false">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content shadow-none" style="box-shadow: none !important; border: 1px solid rgba(0,0,0,0.1);">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="employeeSelectModalLabel">Select Employees</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="list-group">
                                        @foreach($employees as $employee)
                                            <label class="list-group-item d-flex gap-3 align-items-center" style="cursor: pointer;">
                                                <input class="form-check-input flex-shrink-0 employee-checkbox m-0" type="checkbox" value="{{ $employee->id }}" style="font-size: 1.375em;" {{ in_array($employee->id, $oldEmployees) ? 'checked' : '' }}>
                                                <div class="pt-1 w-100 mb-0">
                                                    <strong>{{ $employee->name }}</strong>
                                                    <small class="d-block text-muted">
                                                        {{ $employee->email }}
                                                    </small>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 mb-3">
                        <label>Project Title</label>
                        <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Program / Tech Stack (e.g. MERN, App, PHP)</label>
                        <input type="text" name="program_tech_stack" class="form-control" value="{{ old('program_tech_stack') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Site Link (www.client.com)</label>
                        <input type="text" name="site_link" class="form-control" value="{{ old('site_link') }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Full Details</label>
                        <textarea name="full_details" class="form-control" rows="3">{{ old('full_details') }}</textarea>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Frontend Section</label>
                        <textarea name="frontend_section" class="form-control" rows="2">{{ old('frontend_section') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Backend Section</label>
                        <textarea name="backend_section" class="form-control" rows="2">{{ old('backend_section') }}</textarea>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Database</label>
                        <textarea name="database" class="form-control" rows="2">{{ old('database') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Server Host Details</label>
                        <textarea name="server_host_details" class="form-control" rows="2">{{ old('server_host_details') }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Credentials Details (Sensitive)</label>
                        <textarea name="credentials_details" class="form-control" rows="3">{{ old('credentials_details') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Project</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.employee-checkbox');
        const countSpan = document.getElementById('selectedCount');

        function updateCount() {
            let checkedCount = 0;
            checkboxes.forEach(cb => {
                const hiddenOpt = document.getElementById('hidden-emp-' + cb.value);
                if (cb.checked) {
                    checkedCount++;
                    if(hiddenOpt) hiddenOpt.selected = true;
                } else {
                    if(hiddenOpt) hiddenOpt.selected = false;
                }
            });
            countSpan.textContent = checkedCount + ' selected';
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateCount);
        });

        updateCount();
    });
</script>
@endpush
