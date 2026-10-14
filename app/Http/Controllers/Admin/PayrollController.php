<?php
// app/Http/Controllers/Admin/PayrollController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        
        $payrolls = Payroll::with('employee', 'payslip')
            ->where('month', $month)
            ->where('year', $year)
            ->paginate(20);

        // Stats for the month
        $totalEmployeesInSystem = Employee::where('is_active', true)->count();
        $processedCount = Payroll::where('month', $month)->where('year', $year)->count();
        $paidCount = Payroll::where('month', $month)->where('year', $year)->where('status', 'paid')->count();
        
        $totalGross = Payroll::where('month', $month)->where('year', $year)->sum('gross_salary');
        $totalNet = Payroll::where('month', $month)->where('year', $year)->sum('net_salary');
        $totalDeductions = Payroll::where('month', $month)->where('year', $year)->sum('deductions');
            
        return view('admin.payroll.index', compact(
            'payrolls', 'month', 'year', 
            'totalEmployeesInSystem', 'processedCount', 'paidCount',
            'totalGross', 'totalNet', 'totalDeductions'
        ));
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('employee');
        
        if (request()->ajax()) {
            return view('admin.payroll.show', compact('payroll'));
        }
        
        return view('admin.payroll.details', compact('payroll'));
    }

    public function process(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        
        $employees = Employee::where('is_active', true)->get();
        $processed = 0;
        
        foreach ($employees as $employee) {
            $this->calculateEmployeePayroll($employee, $month, $year);
            $processed++;
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Payroll processed for {$processed} employees"
            ]);
        }
        
        return redirect()->back()->with('success', "Payroll processed for {$processed} employees");
    }

    public function calculateSingle(Employee $employee, Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        
        $payroll = $this->calculateEmployeePayroll($employee, $month, $year);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payroll calculated successfully',
                'payroll' => $payroll
            ]);
        }
        
        return redirect()->back()->with('success', 'Payroll calculated successfully');
    }

    private function calculateEmployeePayroll($employee, $month, $year)
    {
        $attendance = $employee->attendance()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
            
        $startDate = Carbon::create($year, $month, 1);
        $daysInMonth = $startDate->daysInMonth;
        
        // Count actual working days (excluding Sundays)
        $totalWorkingDays = 0;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::create($year, $month, $i);
            if (!$date->isSunday()) {
                $totalWorkingDays++;
            }
        }
        
        $presentDays = $attendance->where('status', 'present')->count();
        $absentDays = $attendance->where('status', 'absent')->count();
        $halfDays = $attendance->where('status', 'half_day')->count();
        
        // 2-Hour Permission & Late Rule
        // Count how many times the employee was 2+ hours late or took a 2-hour permission
        $permissionsTaken = \App\Models\EmployeePermission::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'approved')
            ->get();
            
        $permissionDates = $permissionsTaken->pluck('date')->toArray();
        
        $unapprovedTwoHourLates = $attendance->filter(function($att) use ($permissionDates) {
            return $att->late_minutes >= 120 && !in_array($att->date, $permissionDates);
        })->count();
        
        $totalLatesAndPermissions = $unapprovedTwoHourLates + $permissionsTaken->count();
        
        // For every 3 times of 2-hour late/permission, deduct a half-day
        $penaltyHalfDays = floor($totalLatesAndPermissions / 3);
        $halfDays += $penaltyHalfDays;

        
        $basicSalary = $employee->basic_salary ?: 35000;
        $dailyRate = $basicSalary / ($totalWorkingDays ?: 30);
        
        // Unpaid absence / LOP deduction
        $lopDeductions = round(($absentDays * $dailyRate) + ($halfDays * ($dailyRate / 2)));
        
        // Overtime calculation
        $overtimeHours = $attendance->sum('overtime_hours');
        $hourlyRate = ($dailyRate / 8);
        $overtimeAmount = round($overtimeHours * ($hourlyRate * 1.5));
        
        // Allowances (HRA 40%, Special 20%)
        $hra = round($basicSalary * 0.40);
        $specialAllowance = round($basicSalary * 0.20);
        $totalAllowances = $hra + $specialAllowance;
        
        $grossSalary = round($basicSalary + $totalAllowances + $overtimeAmount - $lopDeductions);
        
        // Statutory & Tax Deductions (Respect Admin PF Enable & Custom Rate/Amount)
        $isPfActive = isset($employee->pf_enabled) ? (bool) $employee->pf_enabled : true;
        if ($isPfActive) {
            if (!empty($employee->pf_amount) && $employee->pf_amount > 0) {
                $pfDeduction = round((float) $employee->pf_amount);
            } else {
                $rate = !empty($employee->pf_rate) ? (float) $employee->pf_rate : 12.0;
                $pfDeduction = round(($basicSalary * $rate) / 100);
            }
        } else {
            $pfDeduction = 0;
        }

        $esiDeduction = ($grossSalary <= 21000) ? round($grossSalary * 0.0075) : 0; // ESI 0.75%
        $ptDeduction = ($grossSalary > 20000) ? 200 : (($grossSalary > 15000) ? 150 : 0); // PT
        
        // TDS Income Tax Deduction
        $annualIncome = $grossSalary * 12;
        $monthlyTds = 0;
        if ($annualIncome > 700000) {
            $taxable = $annualIncome - 75000;
            $monthlyTds = round((($taxable - 300000) * 0.10) / 12);
        }
        
        $statutoryDeductions = $pfDeduction + $esiDeduction + $ptDeduction + $monthlyTds;
        $totalDeductions = $lopDeductions + $statutoryDeductions;
        $netSalary = max(0, $grossSalary - $statutoryDeductions);
        
        // Create or Update Payroll record
        $payroll = Payroll::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'month' => $month,
                'year' => $year
            ],
            [
                'basic_salary' => $basicSalary,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'leave_days' => $absentDays,
                'overtime_hours' => $overtimeHours,
                'overtime_amount' => $overtimeAmount,
                'allowances' => $totalAllowances,
                'allowances_breakdown' => [
                    'hra' => $hra,
                    'special' => $specialAllowance,
                    'overtime' => $overtimeAmount
                ],
                'deductions' => $totalDeductions,
                'deductions_breakdown' => [
                    'pf' => $pfDeduction,
                    'esi' => $esiDeduction,
                    'pt' => $ptDeduction,
                    'tds' => $monthlyTds,
                    'lop' => $lopDeductions
                ],
                'gross_salary' => $grossSalary,
                'net_salary' => $netSalary,
                'status' => 'processed',
                'processed_date' => now()
            ]
        );
        
        return $payroll;
    }

    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load('employee');
        
        // Create directory if not exists
        $payslipDir = storage_path('app/public/payslips');
        if (!file_exists($payslipDir)) {
            mkdir($payslipDir, 0777, true);
        }
        
        $pdf = Pdf::loadView('admin.payroll.payslip', compact('payroll'));
        
        $fileName = "payslip_{$payroll->employee->employee_code}_{$payroll->month}_{$payroll->year}.pdf";
        $filePath = $payslipDir . "/{$fileName}";
        
        $pdf->save($filePath);
        
        $payslip = Payslip::create([
            'employee_id' => $payroll->employee_id,
            'payroll_id' => $payroll->id,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'amount' => $payroll->net_salary,
            'generated_date' => now()
        ]);
        
        // Create notification
        $payroll->employee->notifications()->create([
            'title' => 'Payslip Generated',
            'message' => "Your payslip for {$payroll->month}/{$payroll->year} is ready",
            'type' => 'payroll'
        ]);

        if (!empty($payroll->employee->push_token)) {
            \App\Services\PushService::send(
                $payroll->employee->push_token,
                'Payslip Generated 📄',
                "Your payslip for {$payroll->month}/{$payroll->year} is ready to view.",
                ['type' => 'payroll', 'month' => $payroll->month, 'year' => $payroll->year]
            );
        }
        
        return redirect()->back()->with('success', 'Payslip generated successfully');
    }

    public function bulkGeneratePayslips(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $generated = 0;
        
        $payrolls = Payroll::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'processed')
            ->get();
            
        foreach ($payrolls as $payroll) {
            /** @var Payroll $payroll */
            if (!$payroll->payslip) {
                $this->generatePayslipFile($payroll);
                $generated++;
            }
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Generated {$generated} payslips"
            ]);
        }
        
        return redirect()->back()->with('success', "Generated {$generated} payslips");
    }

    private function generatePayslipFile(Payroll $payroll)
    {
        $payroll->load('employee');
        
        $payslipDir = storage_path('app/public/payslips');
        if (!file_exists($payslipDir)) {
            mkdir($payslipDir, 0777, true);
        }
        
        $pdf = Pdf::loadView('admin.payroll.payslip', compact('payroll'));
        
        $fileName = "payslip_{$payroll->employee->employee_code}_{$payroll->month}_{$payroll->year}.pdf";
        $filePath = $payslipDir . "/{$fileName}";
        
        $pdf->save($filePath);
        
        $payslip = Payslip::create([
            'employee_id' => $payroll->employee_id,
            'payroll_id' => $payroll->id,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'amount' => $payroll->net_salary,
            'generated_date' => now()
        ]);
        
        $payroll->employee->notifications()->create([
            'title' => 'Payslip Generated',
            'message' => "Your payslip for {$payroll->month}/{$payroll->year} is ready",
            'type' => 'payroll'
        ]);
        
        if (!empty($payroll->employee->push_token)) {
            \App\Services\PushService::send(
                $payroll->employee->push_token,
                'Payslip Generated 📄',
                "Your payslip for {$payroll->month}/{$payroll->year} is ready to view.",
                ['type' => 'payroll', 'month' => $payroll->month, 'year' => $payroll->year]
            );
        }
        
        return $payslip;
    }

    public function downloadPayslip(Payslip $payslip)
    {
        if (!file_exists($payslip->file_path)) {
            return redirect()->back()->with('error', 'Payslip file not found');
        }
        
        $payslip->update([
            'is_downloaded' => true,
            'downloaded_at' => now()
        ]);
        
        return response()->download($payslip->file_path, $payslip->file_name);
    }

    public function sendPayslipEmail(Payslip $payslip)
    {
        $payslip->load('employee', 'payroll');
        
        if (!file_exists($payslip->file_path)) {
            return redirect()->back()->with('error', 'Payslip file not found');
        }
        
        Mail::send('emails.payslip', [
            'employee' => $payslip->employee,
            'payslip' => $payslip,
            'payroll' => $payslip->payroll
        ], function($message) use ($payslip) {
            $message->to($payslip->employee->email)
                    ->subject("Payslip for {$payslip->payroll->month}/{$payslip->payroll->year}")
                    ->attach($payslip->file_path);
        });
        
        $payslip->update([
            'is_emailed' => true,
            'emailed_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Payslip sent to employee email');
    }

    public function markAsPaid(Payroll $payroll, Request $request)
    {
        $payroll->update([
            'status' => 'paid',
            'paid_date' => now()
        ]);
        
        $payroll->employee->notifications()->create([
            'title' => 'Salary Credited',
            'message' => "Your salary for {$payroll->month}/{$payroll->year} has been credited.",
            'type' => 'payroll'
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Salary marked as paid'
            ]);
        }
        
        return redirect()->back()->with('success', 'Salary marked as paid');
    }

    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'basic_salary' => 'nullable|numeric',
            'allowances' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'status' => 'required|in:draft,processed,paid,cancelled'
        ]);
        
        $payroll->update([
            'basic_salary' => $request->basic_salary ?? $payroll->basic_salary,
            'allowances' => $request->allowances ?? $payroll->allowances,
            'deductions' => $request->deductions ?? $payroll->deductions,
            'gross_salary' => ($request->basic_salary ?? $payroll->basic_salary) + ($request->allowances ?? $payroll->allowances),
            'net_salary' => ($request->basic_salary ?? $payroll->basic_salary) + ($request->allowances ?? $payroll->allowances) - ($request->deductions ?? $payroll->deductions),
            'status' => $request->status,
            'notes' => $request->notes
        ]);
        
        return redirect()->back()->with('success', 'Payroll updated successfully');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->back()->with('success', 'Payroll record deleted successfully');
    }

    public function export(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $format = $request->input('format', 'excel');
        
        $payrolls = Payroll::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->get();
        
        if ($payrolls->isEmpty()) {
            return redirect()->back()->with('error', 'No payroll records found for the selected period');
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.payroll.report', compact('payrolls', 'month', 'year'));
            return $pdf->download("payroll_report_{$month}_{$year}.pdf");
        }

        // CSV Export for Excel
        $filename = "payroll_report_{$month}_{$year}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Employee Code', 'Employee Name', 'Basic Salary', 'Allowance', 'Deductions', 'Gross Salary', 'Net Salary', 'Status'];

        $callback = function() use($payrolls, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($payrolls as $row) {
                fputcsv($file, [
                    $row->employee->employee_code,
                    $row->employee->name,
                    $row->basic_salary,
                    $row->allowances,
                    $row->deductions,
                    $row->gross_salary,
                    $row->net_salary,
                    $row->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function summary()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $stats = [
            'total_processed' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->count(),
            'total_payout' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->sum('net_salary'),
            'paid_count' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->count(),
            'pending_count' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->where('status', '!=', 'paid')->count(),
        ];

        return view('admin.payroll.summary', compact('stats'));
    }

    public function getSummary()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $summary = [
            'total_net_salary' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->sum('net_salary'),
            'total_gross_salary' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->sum('gross_salary'),
            'total_deductions' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->sum('deductions'),
            'total_employees' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->count(),
        ];

        return response()->json($summary);
    }

    /**
     * Remove / Clear All Payroll Data Records from System.
     */
    public function clearAll(Request $request)
    {
        Payroll::query()->delete();
        Payslip::query()->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'All payroll data records deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'All payroll data records deleted successfully.');
    }
}