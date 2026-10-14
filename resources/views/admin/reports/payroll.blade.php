{{-- resources/views/admin/reports/payroll.blade.php --}}
@extends('layouts.admin')

@section('title', 'Payroll Report')

@section('content')
<div class="fade-in">

    {{-- ── Summary cards ────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">Employees</h6>
                    <h4 class="fw-bold text-primary mb-0">{{ $summary['total_employees'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">Total Basic</h6>
                    <h4 class="fw-bold mb-0">₹{{ number_format($summary['total_basic'], 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">Allowances</h6>
                    <h4 class="fw-bold mb-0">₹{{ number_format($summary['total_allowances'], 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">Deductions</h6>
                    <h4 class="fw-bold text-danger mb-0">₹{{ number_format($summary['total_deductions'], 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">Total Gross</h6>
                    <h4 class="fw-bold text-warning mb-0">₹{{ number_format($summary['total_gross'], 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">Total Net Pay</h6>
                    <h4 class="fw-bold text-success mb-0">₹{{ number_format($summary['total_net'], 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Table ────────────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-money-bill-wave me-2"></i>
                Payroll Report — {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
            </h5>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.reports.payroll') }}">
                    @csrf
                    <input type="hidden" name="month"  value="{{ $month }}">
                    <input type="hidden" name="year"   value="{{ $year }}">
                    <input type="hidden" name="export" value="csv">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th class="text-end">Basic</th>
                            <th class="text-end">Allowances</th>
                            <th class="text-end">Overtime</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Gross</th>
                            <th class="text-end">Net Pay</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $payroll)
                        <tr>
                            <td>
                                <strong>{{ $payroll->employee->name ?? '—' }}</strong><br>
                                <small class="text-muted">{{ $payroll->employee->employee_code ?? '' }}</small>
                            </td>
                            <td>{{ $payroll->employee->department ?? '—' }}</td>
                            <td class="text-end">₹{{ number_format($payroll->basic_salary, 2) }}</td>
                            <td class="text-end">₹{{ number_format($payroll->allowances, 2) }}</td>
                            <td class="text-end">₹{{ number_format($payroll->overtime_amount, 2) }}</td>
                            <td class="text-end text-danger">₹{{ number_format($payroll->deductions, 2) }}</td>
                            <td class="text-end">₹{{ number_format($payroll->gross_salary, 2) }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($payroll->net_salary, 2) }}</td>
                            <td>
                                @php
                                    $color = match($payroll->status) {
                                        'paid'      => 'success',
                                        'processed' => 'warning',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ ucfirst($payroll->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No payroll data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection