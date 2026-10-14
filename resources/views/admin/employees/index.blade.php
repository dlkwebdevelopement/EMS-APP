{{-- resources/views/admin/employees/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Employee Management')
@section('page-title', 'Employee Management')
@section('page-subtitle', 'Manage your workforce - view, edit, and add employees')

@push('styles')
<style>
@media (max-width: 991.98px) {
    .responsive-card-table table, 
    .responsive-card-table thead, 
    .responsive-card-table tbody, 
    .responsive-card-table th, 
    .responsive-card-table td, 
    .responsive-card-table tr { 
        display: block; 
    }
    .responsive-card-table thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    .responsive-card-table tr { 
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        margin: 1rem;
        padding: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .responsive-card-table td { 
        border: none;
        border-bottom: 1px solid #f1f5f9; 
        position: relative;
        padding-left: 50% !important; 
        text-align: right !important;
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        min-height: 48px;
    }
    .responsive-card-table td:last-child {
        border-bottom: 0;
    }
    .responsive-card-table td::before { 
        content: attr(data-label);
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        width: 45%; 
        padding-right: 10px; 
        white-space: nowrap;
        text-align: left;
        font-weight: bold;
        color: #64748b;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
}
.btn-action-xs {
    padding: 0.15rem 0.35rem !important;
    font-size: 0.72rem !important;
    line-height: 1.1 !important;
    border-radius: 4px !important;
}
.btn-action-xs i {
    font-size: 0.72rem !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    {{-- Stats Cards Row --}}
    <div class="row g-4 mb-5 stagger-children">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $employees->total() }}</h3>
                    <p>Total Employees</p>
                    <div class="stats-trend up">
                        <i class="fas fa-chart-line"></i> Active Workforce
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card lime-card">
                <div class="stats-icon">
                    <i class="fas fa-user-check fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $employees->where('is_active', true)->count() }}</h3>
                    <p>Active Employees</p>
                    <div class="stats-trend">
                        <i class="fas fa-briefcase"></i> Currently Working
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card gold-card">
                <div class="stats-icon">
                    <i class="fas fa-building fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $departments->count() }}</h3>
                    <p>Departments</p>
                    <div class="stats-trend">
                        <i class="fas fa-sitemap"></i> Active Departments
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card teal-card">
                <div class="stats-icon">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>₹{{ number_format($employees->sum('basic_salary') / 100000, 1) }}L</h3>
                    <p>Monthly Payroll</p>
                    <div class="stats-trend">
                        <i class="fas fa-rupee-sign"></i> Total Salary
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-filter"></i>
            </div>
            Filter Employees
            <button class="btn btn-sm btn-outline-primary ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.employees.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="fas fa-search" style="color: var(--gl);"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="Search by name, email, code..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-search me-1"></i> Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Employees Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="header-icon">
                    <i class="fas fa-table"></i>
                </div>
                Employee List
            </div>
            <div>
                <a href="{{ route('admin.employees.export', request()->query()) }}" class="btn btn-sm btn-outline-primary me-2">
                    <i class="fas fa-download me-1"></i> Export
                </a>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus me-1"></i> Add Employee
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="responsive-card-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee Code</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td class="fw-semibold" data-label="ID">{{ $employee->id }}</td>
                            <td data-label="Employee Code">
                                <span class="badge badge-green">
                                    <i class="fas fa-id-card me-1"></i>{{ $employee->employee_code }}
                                </span>
                            </td>
                            <td data-label="Employee">
                                <div class="d-flex align-items-center gap-2 justify-content-lg-start justify-content-end">
                                    <div class="user-avatar" style="width: 38px; height: 38px; font-size: 0.8rem;">
                                        @if($employee->profile_photo)
                                            <img src="{{ Storage::url($employee->profile_photo) }}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                                        @else
                                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-semibold">{{ $employee->name }}</div>
                                        <small class="text-muted">{{ $employee->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Department">
                                <span class="badge badge-green">
                                    <i class="fas fa-building me-1"></i>{{ $employee->department }}
                                </span>
                            </td>
                            <td data-label="Designation">
                                <span>{{ $employee->designation }}</span>
                                @if($employee->is_team_leader || $employee->role === 'team_leader')
                                    <span class="badge bg-success text-white ms-1" style="font-size: 0.68rem; padding: 3px 6px;" title="Team Leader (Authorized for multi-worker lead assignments)">
                                        <i class="fas fa-crown me-1"></i>TL
                                    </span>
                                @elseif($employee->role === 'sales')
                                    <span class="badge bg-primary text-white ms-1" style="font-size: 0.68rem; padding: 3px 6px;" title="Sales Team">
                                        <i class="fas fa-briefcase me-1"></i>Sales
                                    </span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if($employee->is_active)
                                    <span class="badge badge-lime">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Active
                                    </span>
                                @else
                                    <span class="badge badge-red">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-center" data-label="Actions">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.employees.show', $employee) }}" 
                                       class="btn btn-action-xs btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.employees.edit', $employee) }}" 
                                       class="btn btn-action-xs btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-action-xs btn-outline-primary" 
                                            onclick="resetPassword({{ $employee->id }})" title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button class="btn btn-action-xs btn-outline-{{ $employee->is_active ? 'danger' : 'success' }}" 
                                            onclick="toggleStatus({{ $employee->id }})" 
                                            title="{{ $employee->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $employee->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <button class="btn btn-action-xs btn-outline-danger" 
                                            onclick="confirmDelete({{ $employee->id }})" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                <h5 class="text-muted">No Employees Found</h5>
                                <p class="text-muted small">Get started by adding your first employee.</p>
                                <a href="{{ route('admin.employees.create') }}" class="btn btn-success mt-2">
                                    <i class="fas fa-plus me-1"></i> Add Employee
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($employees->hasPages())
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-center">
                {{ $employees->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Forms for Actions -->
<form id="resetPasswordForm" method="POST" style="display: none;">
    @csrf
</form>
<form id="toggleStatusForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>
<form id="deleteEmployeeForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function resetPassword(id) {
        Swal.fire({
            title: 'Reset Password?',
            html: `
                <p class="text-muted">This will generate a new random password for the employee.</p>
                <div class="mt-3">
                    <label class="form-label small">Custom Password (Optional)</label>
                    <input type="text" id="customPassword" class="form-control" placeholder="Leave empty for random password">
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, reset it!',
            preConfirm: () => {
                const customPassword = document.getElementById('customPassword').value;
                const form = document.getElementById('resetPasswordForm');
                const existingInput = form.querySelector('input[name="password"]');
                if (existingInput) existingInput.remove();
                
                if (customPassword) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'password';
                    input.value = customPassword;
                    form.appendChild(input);
                }
                form.action = '/admin/employees/' + id + '/reset-password';
                form.submit();
            }
        });
    }
    
    function toggleStatus(id) {
        Swal.fire({
            title: 'Change Status?',
            text: 'Are you sure you want to change the employee status?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, change it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('toggleStatusForm');
                form.action = '/admin/employees/' + id + '/toggle-status';
                form.submit();
            }
        });
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Delete Employee?',
            text: 'This action cannot be undone. All related data will be removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteEmployeeForm');
                form.action = '/admin/employees/' + id;
                form.submit();
            }
        });
    }
</script>
@endpush