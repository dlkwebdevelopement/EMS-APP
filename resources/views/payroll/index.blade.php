{{-- resources/views/admin/payroll/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Payroll Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-money-bill-wave me-2"></i>Payroll Management
        </h1>
        <div>
            <button type="button" class="btn btn-success" onclick="processPayroll()">
                <i class="fas fa-calculator me-1"></i> Process Payroll
            </button>
            <button type="button" class="btn btn-primary" onclick="bulkGeneratePayslips()">
                <i class="fas fa-file-pdf me-1"></i> Generate All Payslips
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Payroll</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payroll.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        @for($i = date('Y')-2; $i <= date('Y')+2; $i++)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="draft">Draft</option>
                        <option value="processed">Processed</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Employees</h6>
                            <h2 class="mb-0">{{ $payrolls->total() }}</h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Gross Salary</h6>
                            <h2 class="mb-0">₹{{ number_format($payrolls->sum('gross_salary'), 0) }}</h2>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Deductions</h6>
                            <h2 class="mb-0">₹{{ number_format($payrolls->sum('deductions'), 0) }}</h2>
                        </div>
                        <i class="fas fa-minus-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Net Salary</h6>
                            <h2 class="mb-0">₹{{ number_format($payrolls->sum('net_salary'), 0) }}</h2>
                        </div>
                        <i class="fas fa-rupee-sign fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Payroll List - {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Basic Salary</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Gross Salary</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Payslip</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        @forelse($payrolls as $payroll)
                        
                            <td><code>{{ $payroll->employee->employee_code }}</code>
                            <td>
                                <strong>{{ $payroll->employee->name }}</strong>
                            
                            <td>{{ $payroll->employee->department }}
                            <td>₹{{ number_format($payroll->basic_salary, 2) }}
                            <td class="text-success">₹{{ number_format($payroll->allowances, 2) }}
                            <td class="text-danger">₹{{ number_format($payroll->deductions, 2) }}
                            <td class="text-primary">₹{{ number_format($payroll->gross_salary, 2) }}
                            <td class="fw-bold text-success">₹{{ number_format($payroll->net_salary, 2) }}
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'processed' => 'info',
                                        'paid' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$payroll->status] }}">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            
                            <td>
                                @if($payroll->payslip)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Generated
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle"></i> Not Generated
                                    </span>
                                @endif
                            
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.payroll.show', $payroll) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($payroll->status == 'processed' && !$payroll->payslip)
                                        <a href="{{ route('admin.payroll.generate-payslip', $payroll) }}" class="btn btn-sm btn-primary" title="Generate Payslip">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                    @if($payroll->payslip)
                                        <a href="{{ route('admin.payroll.download-payslip', $payroll->payslip->id) }}" class="btn btn-sm btn-success" title="Download Payslip">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-info" onclick="sendPayslipEmail({{ $payroll->payslip->id }})" title="Email Payslip">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    @endif
                                    @if($payroll->status == 'processed')
                                        <form action="{{ route('admin.payroll.mark-paid', $payroll) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark as Paid">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            
                                       @empty
                        
                            <td colspan="11" class="text-center py-5">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No payroll records found for {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}.</p>
                                <button class="btn btn-primary" onclick="processPayroll()">
                                    <i class="fas fa-calculator"></i> Process Payroll Now
                                </button>
                            
                                       @endforelse
                    </tbody>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $payrolls->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Process Payroll Modal -->
<div class="modal fade" id="processPayrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calculator me-2"></i>Process Payroll
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.payroll.process') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Month</label>
                        <select name="month" class="form-select" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Year</label>
                        <select name="year" class="form-select" required>
                            @for($i = date('Y')-1; $i <= date('Y'); $i++)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This will calculate salaries for all active employees based on their attendance records.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play"></i> Process Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function processPayroll() {
        $('#processPayrollModal').modal('show');
    }
    
    function bulkGeneratePayslips() {
        if (confirm('Generate payslips for all processed payroll records?')) {
            const month = $('select[name="month"]').val();
            const year = $('select[name="year"]').val();
            
            $.ajax({
                url: '{{ route("admin.payroll.bulk-generate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    month: month,
                    year: year
                },
                success: function(response) {
                    alert(response.message || 'Payslips generated successfully');
                    location.reload();
                },
                error: function() {
                    alert('Failed to generate payslips');
                }
            });
        }
    }
    
    function sendPayslipEmail(payslipId) {
        if (confirm('Send payslip to employee email?')) {
            $.ajax({
                url: '/admin/payroll/payslip/' + payslipId + '/send-email',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Payslip sent successfully');
                },
                error: function() {
                    alert('Failed to send email');
                }
            });
        }
    }
</script>
@endpush
@endsection