{{-- resources/views/admin/payroll/show.blade.php --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h6>Employee Information</h6>
            <table class="table table-sm table-bordered">
                <tr>
                    <th style="width: 40%">Employee Code:</th>
                    <td>{{ $payroll->employee->employee_code }}</td>
                </tr>
                <tr>
                    <th>Name:</th>
                    <td>{{ $payroll->employee->name }}</td>
                </tr>
                <tr>
                    <th>Department:</th>
                    <td>{{ $payroll->employee->department }}</td>
                </tr>
                <tr>
                    <th>Designation:</th>
                    <td>{{ $payroll->employee->designation }}</td>
                </tr>
                <tr>
                    <th>Joining Date:</th>
                    <td>{{ \Carbon\Carbon::parse($payroll->employee->joining_date)->format('d M Y') }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6>Payroll Summary</h6>
            <table class="table table-sm table-bordered">
                <tr>
                    <th>Month/Year:</th>
                    <td>{{ DateTime::createFromFormat('!m', $payroll->month)->format('F') }} {{ $payroll->year }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <span class="badge bg-{{ $payroll->status == 'processed' ? 'info' : ($payroll->status == 'paid' ? 'success' : 'secondary') }}">
                            {{ ucfirst($payroll->status) }}
                        </span>
                    </td>
                </tr>
                @if($payroll->processed_date)
                <tr>
                    <th>Processed Date:</th>
                    <td>{{ \Carbon\Carbon::parse($payroll->processed_date)->format('d M Y, h:i A') }}</td>
                </tr>
                @endif
                @if($payroll->paid_date)
                <tr>
                    <th>Paid Date:</th>
                    <td>{{ \Carbon\Carbon::parse($payroll->paid_date)->format('d M Y, h:i A') }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    
    <hr>
    
    <h6>Salary Breakdown</h6>
    <div class="row">
        <div class="col-md-6">
            <h6>Earnings</h6>
            <table class="table table-sm table-bordered">
                <tr><th>Basic Salary</th><td class="text-end">₹{{ number_format($payroll->basic_salary, 2) }}</td></tr>
                <tr><th>Allowances</th><td class="text-end text-success">+ ₹{{ number_format($payroll->allowances, 2) }}</td></tr>
                @if($payroll->overtime_amount > 0)
                <tr><th>Overtime ({{ $payroll->overtime_hours }} hrs)</th><td class="text-end text-success">+ ₹{{ number_format($payroll->overtime_amount, 2) }}</td></tr>
                @endif
                <tr><th class="bg-light">Gross Salary</th><td class="text-end fw-bold">₹{{ number_format($payroll->gross_salary, 2) }}</td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6>Deductions</h6>
            <table class="table table-sm table-bordered">
                <tr><th>Absent/Late Deductions</th><td class="text-end text-danger">- ₹{{ number_format($payroll->deductions - ($payroll->basic_salary * 0.12) - 200, 2) }}</td></tr>
                <tr><th>Provident Fund (12%)</th><td class="text-end text-danger">- ₹{{ number_format($payroll->basic_salary * 0.12, 2) }}</td></tr>
                <tr><th>Professional Tax</th><td class="text-end text-danger">- ₹200.00</td></tr>
                <tr><th class="bg-light">Total Deductions</th><td class="text-end text-danger fw-bold">- ₹{{ number_format($payroll->deductions, 2) }}</td></tr>
            </table>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-success text-center">
                <h5>Net Salary: <strong>₹{{ number_format($payroll->net_salary, 2) }}</strong></h5>
            </div>
        </div>
    </div>
    
    <h6>Attendance Summary</h6>
    <table class="table table-sm table-bordered">
        <tr><th>Present Days:</th><td>{{ $payroll->present_days }}</td></tr>
        <tr><th>Absent Days:</th><td>{{ $payroll->absent_days }}</td></tr>
        <tr><th>Leave Days:</th><td>{{ $payroll->leave_days }}</td></tr>
        <tr><th>Overtime Hours:</th><td>{{ $payroll->overtime_hours }}</td></tr>
    </table>
</div>