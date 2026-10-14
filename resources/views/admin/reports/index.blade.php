{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="fade-in">

    <div class="row g-4">

        {{-- ── Attendance Report Card ──────────────────────────────────────── --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Attendance Report
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reports.attendance') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <select name="year" class="form-select" required>
                                @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Employee (optional)</label>
                            <select name="employee_id" class="form-select">
                                <option value="">All Employees</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </button>
                            <button type="submit" name="export" value="csv" class="btn btn-success">
                                <i class="fas fa-download"></i> Export CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Payroll Report Card ─────────────────────────────────────────── --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Payroll Report
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reports.payroll') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <select name="year" class="form-select" required>
                                @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department (optional)</label>
                            <select name="department" class="form-select">
                                <option value="">All Departments</option>
                                <option value="IT">IT</option>
                                <option value="HR">HR</option>
                                <option value="Sales">Sales</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Finance">Finance</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </button>
                            <button type="submit" name="export" value="csv" class="btn btn-success">
                                <i class="fas fa-download"></i> Export CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        {{-- ── Asset Report Card ───────────────────────────────────────────── --}}
        <div class="col-md-6 mt-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-laptop me-2"></i>Asset Inventory & Allocation Report
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reports.asset') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Asset Category (optional)</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach(\App\Models\AssetCategory::orderBy('name')->get() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Allocation Status (optional)</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="allocated">Allocated</option>
                                <option value="available">Available</option>
                                <option value="repairing">Under Maintenance / Repairing</option>
                                <option value="faulty">Faulty / Damaged</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </button>
                            <button type="submit" name="export" value="csv" class="btn btn-success">
                                <i class="fas fa-download"></i> Export CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection