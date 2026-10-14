<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * Display a listing of payslips for the logged-in employee.
     */
    public function index()
    {
        $employee = Auth::user();
        
        $payrolls = Payroll::with('payslip')
            ->where('employee_id', $employee->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payrolls
        ]);
    }

    /**
     * Display the specified payroll record.
     */
    public function show($id)
    {
        $employee = Auth::user();
        
        $payroll = Payroll::with('payslip')
            ->where('employee_id', $employee->id)
            ->find($id);

        if (!$payroll) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payslip record not found in database.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $payroll
        ]);
    }

    /**
     * Automated Statutory Payroll Calculation Endpoint.
     */
    public function calculate(Request $request)
    {
        $basic = (float) $request->input('basic_salary', 35000);
        $hra = (float) $request->input('hra_allowance', 14000);
        $special = (float) $request->input('special_allowance', 6000);
        $overtimeHours = (float) $request->input('overtime_hours', 0);
        $lopDays = (int) $request->input('lop_days', 0);
        
        $pfEnabled = (bool) $request->input('pf_enabled', true);
        $pfRate = (float) $request->input('pf_rate', 12);
        $esiEnabled = (bool) $request->input('esi_enabled', true);
        $ptEnabled = (bool) $request->input('pt_enabled', true);
        $tdsEnabled = (bool) $request->input('tds_enabled', true);
        $taxRegime = $request->input('tax_regime', 'new');

        $hourlyRate = ($basic / 30 / 8);
        $overtimePay = round($overtimeHours * $hourlyRate * 1.5);
        $grossEarnings = $basic + $hra + $special + $overtimePay;
        
        $perDaySalary = $grossEarnings / 30;
        $lopDeduction = round($lopDays * $perDaySalary);
        $earnedGross = max(0, $grossEarnings - $lopDeduction);

        // Employee PF (12% of Basic)
        $pfDeduction = $pfEnabled ? round(($basic * $pfRate) / 100) : 0;
        $employerPfEPF = $pfEnabled ? round(($basic * 3.67) / 100) : 0;
        $employerPfEPS = $pfEnabled ? round(($basic * 8.33) / 100) : 0;

        // ESI (0.75% for Gross <= 21,000)
        $esiDeduction = ($esiEnabled && $earnedGross <= 21000) ? round($earnedGross * 0.0075) : 0;

        // Professional Tax (PT)
        $ptDeduction = 0;
        if ($ptEnabled) {
            if ($earnedGross > 20000) $ptDeduction = 200;
            elseif ($earnedGross > 15000) $ptDeduction = 150;
        }

        // Monthly TDS Calculation
        $monthlyTds = 0;
        if ($tdsEnabled) {
            $annualEstimate = $earnedGross * 12;
            if ($taxRegime === 'new') {
                if ($annualEstimate > 700000) {
                    $taxable = $annualEstimate - 75000;
                    $annualTax = max(0, ($taxable - 300000) * 0.10);
                    $monthlyTds = round($annualTax / 12);
                }
            } else {
                if ($annualEstimate > 500000) {
                    $taxable = $annualEstimate - 50000;
                    $annualTax = max(0, ($taxable - 250000) * 0.20);
                    $monthlyTds = round($annualTax / 12);
                }
            }
        }

        $totalStatutoryDeductions = $pfDeduction + $esiDeduction + $ptDeduction + $monthlyTds;
        $netSalary = max(0, $earnedGross - $totalStatutoryDeductions);

        return response()->json([
            'status' => 'success',
            'calculation' => [
                'basic_salary' => $basic,
                'hra_allowance' => $hra,
                'special_allowance' => $special,
                'overtime_amount' => $overtimePay,
                'lop_deduction' => $lopDeduction,
                'gross_salary' => $earnedGross,
                'pf_deduction' => $pfDeduction,
                'employer_pf_epf' => $employerPfEPF,
                'employer_pf_eps' => $employerPfEPS,
                'esi_deduction' => $esiDeduction,
                'pt_deduction' => $ptDeduction,
                'tds_deduction' => $monthlyTds,
                'total_deductions' => $totalStatutoryDeductions,
                'net_salary' => $netSalary,
            ]
        ]);
    }

    /**
     * Batch Generate & Distribute Payroll for All Employees.
     */
    public function generate(Request $request)
    {
        try {
            $month = (int) $request->input('month', date('n'));
            $year = (int) $request->input('year', date('Y'));
            
            $employees = Employee::where('is_active', true)->get();
            if ($employees->isEmpty()) {
                $employees = Employee::all();
            }
            
            $processedCount = 0;

            foreach ($employees as $emp) {
                // Fetch actual attendance records for the target month & year
                $attendance = \App\Models\Attendance::where('employee_id', $emp->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();

                $startDate = \Carbon\Carbon::create($year, $month, 1);
                $daysInMonth = $startDate->daysInMonth;
                
                // Count working days (excluding Sundays)
                $totalWorkingDays = 0;
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $d = \Carbon\Carbon::create($year, $month, $i);
                    if (!$d->isSunday()) {
                        $totalWorkingDays++;
                    }
                }
                $workingDays = $totalWorkingDays ?: 26;

                $presentDays = $attendance->where('status', 'present')->count();
                $absentDays = $attendance->where('status', 'absent')->count();
                $halfDays = $attendance->where('status', 'half_day')->count();

                // 2-Hour Permission & Late Rule
                $permissionsTaken = \App\Models\EmployeePermission::where('employee_id', $emp->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('status', 'approved')
                    ->get();
                    
                $permissionDates = $permissionsTaken->pluck('date')->toArray();
                
                $unapprovedTwoHourLates = $attendance->filter(function($att) use ($permissionDates) {
                    return $att->late_minutes >= 120 && !in_array($att->date, $permissionDates);
                })->count();
                
                $totalLatesAndPermissions = $unapprovedTwoHourLates + $permissionsTaken->count();
                $penaltyHalfDays = floor($totalLatesAndPermissions / 3);
                $halfDays += $penaltyHalfDays;


                $basicSalary = (float) ($emp->basic_salary ?: 35000);
                $dailyRate = $basicSalary / $workingDays;

                // LOP (Loss of Pay) deduction based on actual absent and half days
                $lopDeductions = round(($absentDays * $dailyRate) + ($halfDays * ($dailyRate / 2)));
                
                // Overtime calculation from actual worked hours
                $overtimeHours = (float) $attendance->sum('overtime_hours');
                $hourlyRate = ($dailyRate / 8);
                $overtimePay = round($overtimeHours * ($hourlyRate * 1.5));

                // Allowances (HRA 40%, Special 20%)
                $hra = round($basicSalary * 0.40);
                $special = round($basicSalary * 0.20);
                $totalAllowances = $hra + $special;

                $earnedGross = max(0, round(($basicSalary + $totalAllowances + $overtimePay) - $lopDeductions));

                // Statutory Deductions (Respect Admin PF, ESI, PT, TDS settings)
                $isPfActive = isset($emp->pf_enabled) ? (bool) $emp->pf_enabled : true;
                if ($isPfActive) {
                    if (!empty($emp->pf_amount) && $emp->pf_amount > 0) {
                        $pfDeduction = round((float) $emp->pf_amount);
                    } else {
                        $rate = !empty($emp->pf_rate) ? (float) $emp->pf_rate : 12.0;
                        $pfDeduction = round(($basicSalary * $rate) / 100);
                    }
                } else {
                    $pfDeduction = 0;
                }

                $esiDeduction = ($emp->esi_enabled !== false && $earnedGross <= 21000) ? round($earnedGross * 0.0075) : 0;
                $ptDeduction = ($emp->pt_enabled !== false && $earnedGross > 20000) ? 200 : (($earnedGross > 15000) ? 150 : 0);

                $annualEstimate = $earnedGross * 12;
                $monthlyTds = 0;
                if ($emp->tds_enabled !== false && $annualEstimate > 700000) {
                    $taxable = $annualEstimate - 75000;
                    $monthlyTds = round((($taxable - 300000) * 0.10) / 12);
                }

                $totalDeductions = $pfDeduction + $esiDeduction + $ptDeduction + $monthlyTds;
                $netSalary = max(0, $earnedGross - $totalDeductions);

                Payroll::updateOrCreate(
                    [
                        'employee_id' => $emp->id,
                        'month' => $month,
                        'year' => $year,
                    ],
                    [
                        'basic_salary' => $basicSalary,
                        'allowances' => $totalAllowances,
                        'deductions' => $totalDeductions,
                        'gross_salary' => $earnedGross,
                        'net_salary' => $netSalary,
                        'status' => 'paid',
                        'processed_date' => now(),
                        'paid_date' => now(),
                    ]
                );
                $processedCount++;

                // Send Notification & Push Alert to Employee
                \App\Services\NotificationService::send(
                    $emp->id,
                    'Payslip Disbursed!',
                    "Your monthly payslip for {$month}/{$year} has been disbursed. Earned Gross: ₹" . number_format($earnedGross, 2) . ", Net Payout: ₹" . number_format($netSalary, 2),
                    'payroll',
                    ['month' => $month, 'year' => $year]
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => "Successfully generated & distributed attendance-based payroll for {$processedCount} employees for {$month}/{$year}.",
                'processed_count' => $processedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate payroll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary of recent payrolls for the dashboard.
     */
    public function summary()
    {
        $employee = Auth::user();
        
        $recentPayrolls = Payroll::where('employee_id', $employee->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $recentPayrolls
        ]);
    }
}
