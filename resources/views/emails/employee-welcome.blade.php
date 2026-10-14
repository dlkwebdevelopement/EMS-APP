{{-- resources/views/emails/payslip.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Payslip - {{ $payroll->month }}/{{ $payroll->year }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .salary-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .salary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .salary-row.total {
            border-top: 2px solid #333;
            border-bottom: none;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 18px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payslip Generated</h2>
            <p>{{ $payroll->month }}/{{ $payroll->year }}</p>
        </div>
        
        <div class="content">
            <h3>Dear {{ $employee->name }},</h3>
            
            <p>Your payslip for the month of <strong>{{ DateTime::createFromFormat('!m', $payroll->month)->format('F') }} {{ $payroll->year }}</strong> has been generated.</p>
            
            <div class="salary-details">
                <h4>Salary Details:</h4>
                <div class="salary-row">
                    <span>Basic Salary:</span>
                    <span>₹{{ number_format($payroll->basic_salary, 2) }}</span>
                </div>
                <div class="salary-row">
                    <span>Allowances:</span>
                    <span>₹{{ number_format($payroll->allowances, 2) }}</span>
                </div>
                <div class="salary-row">
                    <span>Overtime Amount:</span>
                    <span>₹{{ number_format($payroll->overtime_amount, 2) }}</span>
                </div>
                <div class="salary-row">
                    <span>Deductions:</span>
                    <span class="text-danger">-₹{{ number_format($payroll->deductions, 2) }}</span>
                </div>
                <div class="salary-row total">
                    <span>Net Salary:</span>
                    <span class="text-success">₹{{ number_format($payroll->net_salary, 2) }}</span>
                </div>
            </div>
            
            <div class="salary-details">
                <h4>Attendance Summary:</h4>
                <div class="salary-row">
                    <span>Present Days:</span>
                    <span>{{ $payroll->present_days }}</span>
                </div>
                <div class="salary-row">
                    <span>Absent Days:</span>
                    <span>{{ $payroll->absent_days }}</span>
                </div>
                <div class="salary-row">
                    <span>Leave Days:</span>
                    <span>{{ $payroll->leave_days }}</span>
                </div>
                <div class="salary-row">
                    <span>Overtime Hours:</span>
                    <span>{{ $payroll->overtime_hours }}</span>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/employee/payslips') }}" class="button">View in Dashboard</a>
            </div>
            
            <p>Please find your detailed payslip attached to this email.</p>
            
            <p>Best regards,<br>
            <strong>Payroll Department</strong><br>
            Employee Management System</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>For any queries regarding your salary, please contact the HR department.</p>
        </div>
    </div>
</body>
</html>