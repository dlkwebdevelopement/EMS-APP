{{-- resources/views/admin/payroll/payslip.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: #fff;
            padding: 20px;
        }
        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e0e0e0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            opacity: 0.9;
        }
        .company-info {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        .company-info h3 {
            margin: 0;
            color: #333;
        }
        .employee-info {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .employee-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .employee-info td {
            padding: 5px;
        }
        .employee-info td:first-child {
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        .salary-details {
            padding: 15px;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .salary-table th {
            background: #f8f9fa;
            padding: 10px;
            text-align: left;
            border: 1px solid #e0e0e0;
        }
        .salary-table td {
            padding: 8px 10px;
            border: 1px solid #e0e0e0;
        }
        .salary-table .text-right {
            text-align: right;
        }
        .total-row {
            background: #f8f9fa;
            font-weight: bold;
        }
        .net-salary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            padding: 15px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 10px;
            color: #999;
        }
        .signature {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            text-align: center;
        }
        .signature hr {
            width: 150px;
            margin-top: 30px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .no-print {
            text-align: center;
            margin-bottom: 10px;
        }
        .btn-print {
            background: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-print:hover {
            background: #2e59d9;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print Payslip
        </button>
    </div>

    <div class="payslip-container">
        <div class="header">
            <h1>PAYSLIP</h1>
            <p>For the month of {{ DateTime::createFromFormat('!m', $payroll->month)->format('F') }} {{ $payroll->year }}</p>
        </div>

        <div class="company-info">
            <h3>Employee Management System</h3>
            <p>123 Business Avenue, Suite 100, New York, NY 10001</p>
            <p>Phone: +1 234 567 890 | Email: support@ems.com</p>
        </div>

        <div class="employee-info">
            <table>
                <tr>
                    <td>Employee Code:</td>
                    <td><strong>{{ $payroll->employee->employee_code }}</strong></td>
                    <td>Date of Joining:</td>
                    <td>{{ \Carbon\Carbon::parse($payroll->employee->joining_date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td>Name:</td>
                    <td><strong>{{ $payroll->employee->name }}</strong></td>
                    <td>Department:</td>
                    <td>{{ $payroll->employee->department }}</td>
                </tr>
                <tr>
                    <td>Designation:</td>
                    <td>{{ $payroll->employee->designation }}</td>
                    <td>PAN Number:</td>
                    <td>{{ $payroll->employee->pan_number ?? 'Not Provided' }}</td>
                </tr>
                <tr>
                    <td>Bank Name:</td>
                    <td>{{ $payroll->employee->bank_name ?? 'Not Provided' }}</td>
                    <td>Account Number:</td>
                    <td>{{ $payroll->employee->bank_account_number ?? 'Not Provided' }}</td>
                </tr>
            </table>
        </div>

        <div class="salary-details">
            <h4>Salary Details</h4>
            <table class="salary-table">
                <thead>
                    <tr>
                        <th>Earnings</th>
                        <th class="text-right">Amount (₹)</th>
                        <th>Deductions</th>
                        <th class="text-right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Salary</td>
                        <td class="text-right">{{ number_format($payroll->basic_salary, 2) }}</td>
                        <td>Absent Deductions</td>
                        <td class="text-right">{{ number_format($payroll->deductions - ($payroll->basic_salary * 0.12), 2) }}</td>
                    </tr>
                    <tr>
                        <td>House Rent Allowance (HRA)</td>
                        <td class="text-right">{{ number_format($payroll->basic_salary * 0.20, 2) }}</td>
                        <td>Provident Fund (PF)</td>
                        <td class="text-right">{{ number_format($payroll->basic_salary * 0.12, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Dearness Allowance (DA)</td>
                        <td class="text-right">{{ number_format($payroll->basic_salary * 0.10, 2) }}</td>
                        <td>Professional Tax</td>
                        <td class="text-right">200.00</td>
                    </tr>
                    <tr>
                        <td>Conveyance Allowance</td>
                        <td class="text-right">1,600.00</td>
                        <td></td>
                        <td class="text-right"></td>
                    </tr>
                    <tr>
                        <td>Medical Allowance</td>
                        <td class="text-right">1,250.00</td>
                        <td></td>
                        <td class="text-right"></td>
                    </tr>
                    @if($payroll->overtime_amount > 0)
                    <tr>
                        <td>Overtime ({{ $payroll->overtime_hours }} hrs)</td>
                        <td class="text-right">{{ number_format($payroll->overtime_amount, 2) }}</td>
                        <td></td>
                        <td class="text-right"></td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>Total Earnings</strong></td>
                        <td class="text-right"><strong>{{ number_format($payroll->gross_salary, 2) }}</strong></td>
                        <td><strong>Total Deductions</strong></td>
                        <td class="text-right"><strong>{{ number_format($payroll->deductions, 2) }}</strong></td>
                    </tr>
                    <tr class="net-salary">
                        <td colspan="3"><strong>Net Salary</strong></td>
                        <td class="text-right"><strong>₹ {{ number_format($payroll->net_salary, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 15px;">
                <h4>Attendance Summary</h4>
                <table class="salary-table" style="width: 50%;">
                    <tr>
                        <td>Working Days:</td>
                        <td class="text-right">{{ \Carbon\Carbon::create($payroll->year, $payroll->month, 1)->daysInMonth }}</td>
                    </tr>
                    <tr>
                        <td>Present Days:</td>
                        <td class="text-right">{{ $payroll->present_days }}</td>
                    </tr>
                    <tr>
                        <td>Absent Days:</td>
                        <td class="text-right">{{ $payroll->absent_days }}</td>
                    </tr>
                    <tr>
                        <td>Leave Days:</td>
                        <td class="text-right">{{ $payroll->leave_days }}</td>
                    </tr>
                    <tr>
                        <td>Overtime Hours:</td>
                        <td class="text-right">{{ $payroll->overtime_hours }}</td>
                    </tr>
                </table>
            </div>

            <div style="margin-top: 15px;">
                <p><strong>Amount in Words:</strong> {{ numberToWords($payroll->net_salary) }} Rupees Only</p>
            </div>
        </div>

        <div class="signature">
            <div>
                <hr>
                <p>Employee Signature</p>
            </div>
            <div>
                <hr>
                <p>Authorized Signatory</p>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer generated document and does not require a physical signature.</p>
            <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>

<?php
// Helper function for number to words
function numberToWords($number) {
    $words = array(
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 
        18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 
        80 => 'Eighty', 90 => 'Ninety'
    );
    
    $number = round($number, 2);
    $rupees = floor($number);
    $paise = round(($number - $rupees) * 100);
    
    $rupeesInWords = '';
    if ($rupees < 20) {
        $rupeesInWords = $words[$rupees];
    } else {
        $tens = floor($rupees / 10) * 10;
        $units = $rupees % 10;
        $rupeesInWords = $words[$tens];
        if ($units > 0) {
            $rupeesInWords .= ' ' . $words[$units];
        }
    }
    
    if ($paise > 0) {
        return ucfirst($rupeesInWords) . ' and ' . $paise . ' Paise';
    }
    return ucfirst($rupeesInWords);
}
?>