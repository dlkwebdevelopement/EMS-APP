<!DOCTYPE html>
<html>
<head>
    <title>Payroll Report - {{ $month }}/{{ $year }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { text-align: right; margin-top: 30px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Payroll Report</h2>
        <p>Month/Year: {{ $month }}/{{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Employee</th>
                <th>Basic</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Net Salary</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $payroll)
            <tr>
                <td>{{ $payroll->employee->employee_code }}</td>
                <td>{{ $payroll->employee->name }}</td>
                <td>₹{{ number_format($payroll->basic_salary, 2) }}</td>
                <td>₹{{ number_format($payroll->allowances, 2) }}</td>
                <td>₹{{ number_format($payroll->deductions, 2) }}</td>
                <td>₹{{ number_format($payroll->net_salary, 2) }}</td>
                <td>{{ ucfirst($payroll->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Payout: ₹{{ number_format($payrolls->sum('net_salary'), 2) }}</p>
    </div>
</body>
</html>
