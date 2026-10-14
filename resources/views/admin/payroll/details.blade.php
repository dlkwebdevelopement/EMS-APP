{{-- resources/views/admin/payroll/details.blade.php --}}
@extends('layouts.admin')

@section('title', 'Payroll Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <div class="organic-rule"></div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-money-check-alt me-2 text-primary"></i>Payroll Details
            </h1>
            <p class="text-muted small mt-1">Detailed breakdown for {{ $payroll->employee->name }} - {{ DateTime::createFromFormat('!m', $payroll->month)->format('F') }} {{ $payroll->year }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($payroll->status == 'processed' && !$payroll->payslip)
                <a href="{{ route('admin.payroll.generate-payslip', $payroll) }}" class="btn btn-success">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Generate Payslip
                </a>
            @endif
            @if($payroll->status == 'processed')
                <form action="{{ route('admin.payroll.mark-paid', $payroll) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle me-2"></i>Mark as Paid
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-4 stagger-children">
        <div class="col-md-4">
            <div class="stats-card lime-card">
                <div class="stats-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="stats-info">
                    <p>Gross Salary</p>
                    <h3>₹{{ number_format($payroll->gross_salary, 2) }}</h3>
                    <span class="stats-trend"><i class="fas fa-plus-circle"></i> Earnings</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                <div class="stats-icon"><i class="fas fa-minus-circle"></i></div>
                <div class="stats-info">
                    <p>Total Deductions</p>
                    <h3>₹{{ number_format($payroll->deductions, 2) }}</h3>
                    <span class="stats-trend"><i class="fas fa-arrow-down"></i> Deducted</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card teal-card">
                <div class="stats-icon"><i class="fas fa-wallet"></i></div>
                <div class="stats-info">
                    <p>Net Take-Home</p>
                    <h3>₹{{ number_format($payroll->net_salary, 2) }}</h3>
                    <span class="stats-trend up"><i class="fas fa-check-double"></i> Payable</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card mb-4 fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-user-tie"></i></div>
                    Employee Information
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="user-avatar shadow-sm me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            {{ strtoupper(substr($payroll->employee->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $payroll->employee->name }}</h5>
                            <code class="text-primary">{{ $payroll->employee->employee_code }}</code>
                        </div>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-building me-2"></i>Department</span>
                            <span class="fw-bold">{{ $payroll->employee->department }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-briefcase me-2"></i>Designation</span>
                            <span class="fw-bold">{{ $payroll->employee->designation }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Joining Date</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($payroll->employee->joining_date)->format('d M Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-info-circle me-2"></i>Status</span>
                            <span class="badge bg-{{ $payroll->status == 'processed' ? 'info' : ($payroll->status == 'paid' ? 'success' : 'secondary') }}">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Detailed Breakdown -->
        <div class="col-lg-8">
            <div class="card shadow mb-4 fade-in-up" style="animation-delay: 0.3s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-list-ul"></i></div>
                    Earnings & Deductions Breakdown
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Earnings Column -->
                        <div class="col-md-6 border-end">
                            <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-arrow-circle-up me-2"></i>Earnings
                            </h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Basic Salary</td>
                                    <td class="text-end fw-bold">₹{{ number_format($payroll->basic_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Allowances</td>
                                    <td class="text-end text-success">+ ₹{{ number_format($payroll->allowances, 2) }}</td>
                                </tr>
                                @if($payroll->overtime_amount > 0)
                                <tr>
                                    <td class="text-muted">Overtime ({{ $payroll->overtime_hours }} hrs)</td>
                                    <td class="text-end text-success">+ ₹{{ number_format($payroll->overtime_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="fw-bold">Gross Total</td>
                                    <td class="text-end fw-bold text-dark">₹{{ number_format($payroll->gross_salary, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Deductions Column -->
                        <div class="col-md-6">
                            <h6 class="text-danger fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-arrow-circle-down me-2"></i>Deductions
                            </h6>
                            <table class="table table-borderless table-sm">
                                @if($payroll->absent_days > 0)
                                <tr>
                                    <td class="text-muted">Leave/Absent</td>
                                    <td class="text-end text-danger">- ₹{{ number_format($payroll->deductions, 2) }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td class="text-muted">No Deductions</td>
                                    <td class="text-end text-success">₹0.00</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="fw-bold">Total Deductions</td>
                                    <td class="text-end fw-bold text-danger">- ₹{{ number_format($payroll->deductions, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Final Net Salary Display -->
                    <div class="mt-4 p-3 rounded-3 bg-light border-start border-4 border-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-0">Monthly Take-home Salary</p>
                                <h2 class="text-success fw-bold mb-0">₹{{ number_format($payroll->net_salary, 2) }}</h2>
                            </div>
                            <div class="text-end">
                                <p class="text-muted small mb-1">Status: <span class="fw-bold text-{{ $payroll->status == 'paid' ? 'success' : 'primary' }}">{{ strtoupper($payroll->status) }}</span></p>
                                <p class="text-muted extra-small mb-0">Last updated: {{ $payroll->updated_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Detail Footer Card -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm fade-in-up" style="animation-delay: 0.4s;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="fas fa-clock text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Processed Date</p>
                                    <p class="fw-bold mb-0 small">{{ $payroll->processed_date ? \Carbon\Carbon::parse($payroll->processed_date)->format('d M Y, h:i A') : 'Pending' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm fade-in-up" style="animation-delay: 0.4s;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="fas fa-check-double text-success"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Paid Date</p>
                                    <p class="fw-bold mb-0 small">{{ $payroll->paid_date ? \Carbon\Carbon::parse($payroll->paid_date)->format('d M Y, h:i A') : 'Not Yet Paid' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($payroll->notes)
            <div class="card border-start border-4 border-primary shadow-sm fade-in-up" style="animation-delay: 0.5s;">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="fas fa-sticky-note me-2"></i>Admin Notes</h6>
                    <p class="text-muted mb-0 small">{{ $payroll->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
/div>
@endsection