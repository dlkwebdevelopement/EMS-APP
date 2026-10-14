<?php
// app/Http/Controllers/Admin/ReportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    // =========================================================================
    // INDEX — report dashboard
    // =========================================================================

    public function index()
    {
        $month     = (int) Carbon::now()->month;
        $year      = (int) Carbon::now()->year;
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        return view('admin.reports.index', compact('month', 'year', 'employees'));
    }

    // =========================================================================
    // ATTENDANCE REPORT
    // =========================================================================

    public function attendance(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000|max:2100',
        ]);

        $month     = (int) $request->month;
        $year      = (int) $request->year;
        $employees = Employee::where('is_active', true)->get();
        $report    = [];

        foreach ($employees as $employee) {
            $attendance = $employee->attendance()
                ->whereMonth('date', $month)
                ->whereYear('date',  $year)
                ->get();

            $report[] = [
                'employee'  => $employee,
                'present'   => $attendance->where('status', 'present')->count(),
                'absent'    => $attendance->where('status', 'absent')->count(),
                'late'      => $attendance->where('status', 'late')->count(),
                'half_day'  => $attendance->where('status', 'half_day')->count(),
                'holiday'   => $attendance->where('status', 'holiday')->count(),
                'overtime'  => round($attendance->sum('overtime_hours'), 2),
            ];
        }

        // Export CSV if requested
        if ($request->export === 'csv') {
            return $this->exportAttendanceCsv($report, $month, $year);
        }

        return view('admin.reports.attendance', compact('report', 'month', 'year'));
    }

    // =========================================================================
    // PAYROLL REPORT
    // =========================================================================

    public function payroll(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000|max:2100',
        ]);

        $month = (int) $request->month;
        $year  = (int) $request->year;

        $payrolls = Payroll::with('employee')
            ->where('month', $month)
            ->where('year',  $year)
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_employees'  => $payrolls->count(),
            'total_basic'      => $payrolls->sum('basic_salary'),
            'total_allowances' => $payrolls->sum('allowances'),
            'total_overtime'   => $payrolls->sum('overtime_amount'),
            'total_deductions' => $payrolls->sum('deductions'),
            'total_gross'      => $payrolls->sum('gross_salary'),
            'total_net'        => $payrolls->sum('net_salary'),
        ];

        // Export CSV if requested
        if ($request->export === 'csv') {
            return $this->exportPayrollCsv($payrolls, $month, $year);
        }

        return view('admin.reports.payroll', compact('payrolls', 'summary', 'month', 'year'));
    }

    // =========================================================================
    // PRIVATE — CSV export helpers
    // =========================================================================

    private function exportAttendanceCsv(array $report, int $month, int $year): StreamedResponse
    {
        $fileName = "attendance_report_{$year}_{$month}.csv";

        return response()->stream(function () use ($report) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'Employee Code', 'Name', 'Department', 'Designation',
                'Present', 'Absent', 'Late', 'Half Day', 'Holiday', 'Overtime (hrs)',
            ]);

            foreach ($report as $row) {
                fputcsv($handle, [
                    $row['employee']->employee_code,
                    $row['employee']->name,
                    $row['employee']->department,
                    $row['employee']->designation,
                    $row['present'],
                    $row['absent'],
                    $row['late'],
                    $row['half_day'],
                    $row['holiday'],
                    $row['overtime'],
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
        ]);
    }

    private function exportPayrollCsv($payrolls, int $month, int $year): StreamedResponse
    {
        $fileName = "payroll_report_{$year}_{$month}.csv";

        return response()->stream(function () use ($payrolls) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'Employee Code', 'Name', 'Department',
                'Basic Salary', 'Allowances', 'Overtime',
                'Deductions', 'Gross Salary', 'Net Salary', 'Status',
            ]);

            foreach ($payrolls as $payroll) {
                fputcsv($handle, [
                    $payroll->employee->employee_code ?? '-',
                    $payroll->employee->name          ?? '-',
                    $payroll->employee->department    ?? '-',
                    $payroll->basic_salary,
                    $payroll->allowances,
                    $payroll->overtime_amount,
                    $payroll->deductions,
                    $payroll->gross_salary,
                    $payroll->net_salary,
                    $payroll->status,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
        ]);
    }

    // =========================================================================
    // ASSET REPORT
    // =========================================================================

    public function asset(Request $request)
    {
        $category_id = $request->category_id;
        $status      = $request->status;

        $categories = \App\Models\AssetCategory::orderBy('name')->get();

        $query = \App\Models\Asset::with(['category', 'currentAllocation.employee']);

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $assets = $query->latest()->get();

        $summary = [
            'total_assets' => $assets->count(),
            'allocated'    => $assets->where('status', 'allocated')->count(),
            'available'    => $assets->where('status', 'available')->count(),
            'total_cost'   => $assets->sum('purchase_cost'),
        ];

        if ($request->export === 'csv') {
            return $this->exportAssetCsv($assets);
        }

        return view('admin.reports.asset', compact('assets', 'summary', 'categories', 'category_id', 'status'));
    }

    private function exportAssetCsv($assets): StreamedResponse
    {
        $fileName = "asset_report_" . date('Y_m_d') . ".csv";

        return response()->stream(function () use ($assets) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'Asset Tag', 'Name / Brand', 'Model', 'Serial Number',
                'Category', 'Current Holder', 'Status', 'Purchase Date', 'Purchase Cost (₹)'
            ]);

            foreach ($assets as $asset) {
                fputcsv($handle, [
                    $asset->asset_tag,
                    $asset->brand . ' ' . $asset->model,
                    $asset->model,
                    $asset->serial_number ?? 'N/A',
                    $asset->category->name ?? 'N/A',
                    $asset->currentAllocation->employee->name ?? 'Unallocated',
                    ucfirst($asset->status),
                    $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : 'N/A',
                    $asset->purchase_cost ?? 0,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
        ]);
    }
}