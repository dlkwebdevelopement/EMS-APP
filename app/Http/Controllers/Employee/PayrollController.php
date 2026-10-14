<?php
// app/Http/Controllers/Employee/PayrollController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    /**
     * Display listing of payslips.
     */
    public function payslips()
    {
        $employee = Auth::user();
        $payslips = Payroll::where('employee_id', $employee->id)
            ->where('status', 'paid') // Only show paid payslips
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);
            
        return view('employee.payslips', compact('payslips'));
    }

    /**
     * Download payslip PDF.
     */
    public function downloadPayslip(Payroll $payroll)
    {
        if ($payroll->employee_id != Auth::id()) {
            abort(403);
        }
        
        if (!$payroll->payslip) {
            return back()->with('error', 'Payslip not found or not yet generated.');
        }
        
        $filePath = public_path($payroll->payslip->file_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Payslip file is missing on the server.');
        }
        
        return response()->download($filePath, $payroll->payslip->file_name);
    }
}
