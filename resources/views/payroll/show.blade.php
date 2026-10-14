{{-- resources/views/admin/payroll/show.blade.php --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-4">
                <h6 class="text-muted mb-2">Employee Information</h6>
                <div class="bg-light p-3 rounded">
                    <p class="mb-2"><strong>Name:</strong> {{ $payroll->employee->name }}</p>
                    <p class="mb-2"><strong>Employee Code:</strong> {{ $payroll->employee->employee_code }}</p>
                    <p class="mb-2"><strong>Department:</strong> {{ $payroll->employee->department }}</p>
                    <p class="mb-2"><strong>Designation:</strong> {{ $payroll->employee->designation }}</p>
                    <p class="mb-0"><strong>Joining Date:</strong> {{ \Carbon\Carbon::parse($payroll->employee->joining_date)->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-4">
                <h6 class="text-muted mb-2">Payroll Summary</h6>
                <div class="bg-light p-3 rounded">
                    <p class="mb-2"><strong>Period:</strong> {{ DateTime::createFromFormat('!m', $payroll->month)->format('F') }} {{ $payroll->year }}</p>
                    <p class="mb-2"><strong>Status:</strong> 
                        <span class="badge bg-{{ $payroll->status == 'processed' ? 'info' : ($payroll->status == 'paid' ? 'success' : 'secondary') }}">
                            {{ ucfirst($payroll->status) }}
                        </span>
                    </p>
                    @if($payroll->processed_date)
                    <p class="mb-2"><strong>Processed Date:</strong> {{ \Carbon\Carbon::parse($payroll->processed_date)->format('d M Y, h:i A') }}</p>
                    @endif
                    @if($payroll->paid_date)
                    <p class="mb-0"><strong>Paid Date:</strong> {{ \Carbon\Carbon::parse($payroll->paid_date)->format('d M Y, h:i A') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <hr>

    <h6 class="mb-3">Salary Breakdown</h6>
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-light border-0 mb-3">
                <div class="card-body">
                    <h6 class="text-success mb-3"><i class="fas fa-plus-circle me-1"></i> Earnings</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Basic Salary:</span>
                        <span class="fw-semibold">₹{{ number_format($payroll->basic_salary, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Allowances:</span>
                        <span class="text-success">+ ₹{{ number_format($payroll->allowances, 2) }}</span>
                    </div>
                    @if($payroll->overtime_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Overtime ({{ $payroll->overtime_hours }} hrs):</span>
                        <span class="text-success">+ ₹{{ number_format($payroll->overtime_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between pt-2 border-top mt-2">
                        <strong>Gross Salary:</strong>
                        <strong class="text-primary">₹{{ number_format($payroll->gross_salary, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light border-0 mb-3">
                <div class="card-body">
                    <h6 class="text-danger mb-3"><i class="fas fa-minus-circle me-1"></i> Deductions</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Absent/Late:</span>
                        <span class="text-danger">- ₹{{ number_format($payroll->deductions - ($payroll->basic_salary * 0.12) - 200, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Provident Fund (12%):</span>
                        <span class="text-danger">- ₹{{ number_format($payroll->basic_salary * 0.12, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Professional Tax:</span>
                        <span class="text-danger">- ₹200.00</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top mt-2">
                        <strong>Total Deductions:</strong>
                        <strong class="text-danger">- ₹{{ number_format($payroll->deductions, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-success text-center mb-0">
        <h5 class="mb-0">Net Salary: <strong>₹{{ number_format($payroll->net_salary, 2) }}</strong></h5>
    </div>

    <hr class="my-3">

    <h6 class="mb-3">Attendance Summary</h6>
    <div class="row">
        <div class="col-md-3">
            <div class="text-center p-3 bg-light rounded">
                <div class="text-primary fw-bold h4">{{ $payroll->present_days }}</div>
                <small class="text-muted">Present Days</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3 bg-light rounded">
                <div class="text-danger fw-bold h4">{{ $payroll->absent_days }}</div>
                <small class="text-muted">Absent Days</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3 bg-light rounded">
                <div class="text-warning fw-bold h4">{{ $payroll->leave_days }}</div>
                <small class="text-muted">Leave Days</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3 bg-light rounded">
                <div class="text-info fw-bold h4">{{ $payroll->overtime_hours }}</div>
                <small class="text-muted">Overtime Hours</small>
            </div>
        </div>
    </div>
</div>