<?php
// app/Http/Controllers/Admin/LeaveController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\AuditLog;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    // =========================================================================
    // INDEX
    // =========================================================================

    public function index(Request $request)
    {
        $query = Leave::with('employee');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->from_date) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('end_date', '<=', $request->to_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        // Advanced Stats for Dashboard
        $today = now()->toDateString();
        $stats = [
            'pending' => Leave::where('status', 'pending')->count(),
            'on_leave_today' => Leave::where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->count(),
            'approved_month' => Leave::where('status', 'approved')
                ->whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)
                ->count(),
            'rejected_month' => Leave::where('status', 'rejected')
                ->whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)
                ->count(),
            'upcoming' => Leave::where('status', 'approved')
                ->where('start_date', '>', $today)
                ->count(),
            'total_days_month' => Leave::where('status', 'approved')
                ->whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)
                ->sum('total_days'),
        ];

        return view('admin.leaves.index', compact('leaves', 'employees', 'stats'));
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    public function show(Leave $leave)
    {
        // Eagerly load the employee relationship
        $leave->load('employee');

        $leaveBalance = null;

        if ($leave->employee) {
            $year = Carbon::parse($leave->start_date)->year;

            $leaveBalance = LeaveBalance::where('employee_id', $leave->employee_id)
                ->where('year', $year)
                ->first();

            // If no balance record exists, use a default unsaved instance for display only
            if (!$leaveBalance) {
                $leaveBalance = new LeaveBalance([
                    'employee_id'            => $leave->employee_id,
                    'year'                   => $year,
                    'sick_leave_total'       => 12,
                    'sick_leave_used'        => 0,
                    'casual_leave_total'     => 12,
                    'casual_leave_used'      => 0,
                    'annual_leave_total'     => 15,
                    'annual_leave_used'      => 0,
                    'unpaid_leave_total'     => 0,
                    'unpaid_leave_used'      => 0,
                    'maternity_leave_total'  => 90,
                    'maternity_leave_used'   => 0,
                    'paternity_leave_total'  => 7,
                    'paternity_leave_used'   => 0,
                ]);
            }
        }

        return view('admin.leaves.show', compact('leave', 'leaveBalance'));
    }

    // =========================================================================
    // CREATE / STORE
    // =========================================================================

    public function create()
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        return view('admin.leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|in:sick,casual,annual,unpaid,maternity,paternity',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'required|string|max:1000',
            'attachment'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Check for overlapping active leaves
        $overlap = Leave::where('employee_id', $request->employee_id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })->exists();

        if ($overlap) {
            return back()->with('error', 'Employee already has an overlapping active leave request for these dates.')->withInput();
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $totalDays = max(1, $startDate->diffInDays($endDate) + 1);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $leave = Leave::create([
            'employee_id' => $request->employee_id,
            'leave_type'  => $request->leave_type,
            'start_date'  => $startDate->format('Y-m-d'),
            'end_date'    => $endDate->format('Y-m-d'),
            'total_days'  => $totalDays,
            'reason'      => $request->reason,
            'attachment'  => $attachmentPath,
            'status'      => 'pending',
        ]);

        Notification::create([
            'employee_id' => $leave->employee_id,
            'title'       => 'Leave Request Created',
            'message'     => "A {$leave->leave_type} leave from " .
                             $startDate->format('d M Y') . ' to ' .
                             $endDate->format('d M Y') . ' has been created by admin.',
            'type'        => 'leave',
        ]);

        return redirect()->route('admin.leaves.index')
            ->with('success', 'Leave request created successfully.');
    }

    // =========================================================================
    // APPROVE
    // =========================================================================

    public function approve(Leave $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leaves can be approved.');
        }

        // Guard: employee must still exist
        if (!$leave->employee) {
            return back()->with('error', 'Cannot approve: the employee record no longer exists.');
        }

        $leave->status      = 'approved';
        $leave->approved_by = auth()->guard('admin')->id();
        $leave->approved_at = \Illuminate\Support\Carbon::now();
        $leave->save();

        // Deduct from leave balance
        $balance = LeaveBalance::firstOrCreate(
            [
                'employee_id' => $leave->employee_id,
                'year'        => Carbon::parse($leave->start_date)->year,
            ],
            [
                'sick_leave_total'   => 12,
                'sick_leave_used'    => 0,
                'casual_leave_total' => 12,
                'casual_leave_used'  => 0,
                'annual_leave_total' => 15,
                'annual_leave_used'  => 0,
                'unpaid_leave_total' => 0,
                'unpaid_leave_used'  => 0,
                'maternity_leave_total' => 90,
                'maternity_leave_used' => 0,
                'paternity_leave_total' => 7,
                'paternity_leave_used' => 0,
            ]
        );

        switch ($leave->leave_type) {
            case 'sick':
                $balance->sick_leave_used += $leave->total_days;
                break;
            case 'casual':
                $balance->casual_leave_used += $leave->total_days;
                break;
            case 'annual':
                $balance->annual_leave_used += $leave->total_days;
                break;
            case 'unpaid':
                $balance->unpaid_leave_used += $leave->total_days;
                break;
            case 'maternity':
                $balance->maternity_leave_used += $leave->total_days;
                break;
            case 'paternity':
                $balance->paternity_leave_used += $leave->total_days;
                break;
        }
        $balance->save();

        Notification::create([
            'employee_id' => $leave->employee_id,
            'title'       => 'Leave Approved',
            'message'     => "Your {$leave->leave_type} leave from " .
                             Carbon::parse($leave->start_date)->format('d M Y') .
                             ' to ' .
                             Carbon::parse($leave->end_date)->format('d M Y') .
                             ' has been approved.',
            'type'        => 'leave',
        ]);

        if ($leave->employee && !empty($leave->employee->push_token)) {
            \App\Services\PushService::send(
                $leave->employee->push_token,
                'Leave Approved ✅',
                "Your {$leave->leave_type} leave from " . Carbon::parse($leave->start_date)->format('d M') . " has been approved.",
                ['type' => 'leave', 'leave_id' => $leave->id]
            );
        }

        // Audit log
        AuditLog::log(
            'approved',
            "Leave #{$leave->id} ({$leave->leave_type}) for employee '{$leave->employee->name}' approved. ({$leave->total_days} day(s))",
            $leave
        );

        return redirect()->route('admin.leaves.index')
            ->with('success', 'Leave approved successfully.');
    }

    // =========================================================================
    // REJECT
    // =========================================================================

    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leaves can be rejected.');
        }

        // Guard: employee must still exist
        if (!$leave->employee) {
            return back()->with('error', 'Cannot reject: the employee record no longer exists.');
        }

        $leave->status           = 'rejected';
        $leave->rejection_reason = $request->rejection_reason;
        $leave->approved_by      = auth()->guard('admin')->id();
        $leave->approved_at      = \Illuminate\Support\Carbon::now();
        $leave->save();

        Notification::create([
            'employee_id' => $leave->employee_id,
            'title'       => 'Leave Rejected',
            'message'     => "Your {$leave->leave_type} leave from " .
                             Carbon::parse($leave->start_date)->format('d M Y') .
                             ' to ' .
                             Carbon::parse($leave->end_date)->format('d M Y') .
                             " has been rejected. Reason: {$request->rejection_reason}",
            'type'        => 'leave',
        ]);

        if ($leave->employee && !empty($leave->employee->push_token)) {
            \App\Services\PushService::send(
                $leave->employee->push_token,
                'Leave Rejected ❌',
                "Your {$leave->leave_type} leave has been rejected. Reason: {$request->rejection_reason}",
                ['type' => 'leave', 'leave_id' => $leave->id]
            );
        }

        // Audit log
        AuditLog::log(
            'rejected',
            "Leave #{$leave->id} ({$leave->leave_type}) for employee '{$leave->employee->name}' rejected. Reason: {$request->rejection_reason}",
            $leave
        );

        return redirect()->route('admin.leaves.index')
            ->with('success', 'Leave rejected successfully.');
    }

    // =========================================================================
    // CANCEL
    // =========================================================================

    public function cancel(Leave $leave)
    {
        if (!in_array($leave->status, ['pending', 'approved'])) {
            return back()->with('error', 'This leave cannot be cancelled.');
        }

        // If it was approved, restore the balance
        if ($leave->status === 'approved') {
            $balance = LeaveBalance::where('employee_id', $leave->employee_id)
                ->where('year', Carbon::parse($leave->start_date)->year)
                ->first();

            if ($balance) {
                switch ($leave->leave_type) {
                    case 'sick':
                        $balance->sick_leave_used = max(0, $balance->sick_leave_used - $leave->total_days);
                        break;
                    case 'casual':
                        $balance->casual_leave_used = max(0, $balance->casual_leave_used - $leave->total_days);
                        break;
                    case 'annual':
                        $balance->annual_leave_used = max(0, $balance->annual_leave_used - $leave->total_days);
                        break;
                    case 'unpaid':
                        $balance->unpaid_leave_used = max(0, $balance->unpaid_leave_used - $leave->total_days);
                        break;
                    case 'maternity':
                        $balance->maternity_leave_used = max(0, $balance->maternity_leave_used - $leave->total_days);
                        break;
                    case 'paternity':
                        $balance->paternity_leave_used = max(0, $balance->paternity_leave_used - $leave->total_days);
                        break;
                }
                $balance->save();
            }
        }

        $leave->status = 'cancelled';
        $leave->save();

        // Only notify if employee still exists
        if ($leave->employee_id) {
            Notification::create([
                'employee_id' => $leave->employee_id,
                'title'       => 'Leave Cancelled',
                'message'     => "Your {$leave->leave_type} leave from " .
                                 Carbon::parse($leave->start_date)->format('d M Y') .
                                 ' to ' .
                                 Carbon::parse($leave->end_date)->format('d M Y') .
                                 ' has been cancelled by admin.',
                'type'        => 'leave',
            ]);
        }

        return back()->with('success', 'Leave cancelled successfully.');
    }

    public function getSummary()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $summary = [
            'total_pending' => Leave::where('status', 'pending')->count(),
            'total_approved' => Leave::where('status', 'approved')->whereMonth('start_date', $currentMonth)->count(),
            'total_on_leave' => Leave::where('status', 'approved')
                ->where('start_date', '<=', now()->toDateString())
                ->where('end_date', '>=', now()->toDateString())
                ->count(),
        ];

        return response()->json($summary);
    }

    public function report(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;
        
        $stats = [
            'sick' => Leave::where('status', 'approved')->where('leave_type', 'sick')->whereYear('start_date', $year)->sum('total_days'),
            'casual' => Leave::where('status', 'approved')->where('leave_type', 'casual')->whereYear('start_date', $year)->sum('total_days'),
            'annual' => Leave::where('status', 'approved')->where('leave_type', 'annual')->whereYear('start_date', $year)->sum('total_days'),
            'unpaid' => Leave::where('status', 'approved')->where('leave_type', 'unpaid')->whereYear('start_date', $year)->sum('total_days'),
            'total_approved' => Leave::where('status', 'approved')->whereYear('start_date', $year)->count(),
        ];

        $monthly_stats = Leave::selectRaw('month(start_date) as month, sum(total_days) as total_days')
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->groupBy('month')
            ->get()
            ->pluck('total_days', 'month')
            ->toArray();

        // Department-wise distribution
        $dept_stats = Leave::join('employees', 'leaves.employee_id', '=', 'employees.id')
            ->selectRaw('employees.department, count(*) as count')
            ->where('leaves.status', 'approved')
            ->whereYear('leaves.start_date', $year)
            ->groupBy('employees.department')
            ->get()
            ->pluck('count', 'department')
            ->toArray();

        return view('admin.leaves.report', compact('stats', 'monthly_stats', 'dept_stats', 'year'));
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');
        $query = Leave::with('employee');

        if ($request->status) $query->where('status', $request->status);
        if ($request->from_date) $query->whereDate('start_date', '>=', $request->from_date);
        if ($request->to_date) $query->whereDate('end_date', '<=', $request->to_date);

        $leaves = $query->get();

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.leaves.export_pdf', compact('leaves'));
            return $pdf->download('leave_report.pdf');
        }

        $filename = "leave_report_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Employee', 'Type', 'Start Date', 'End Date', 'Days', 'Status', 'Reason'];

        $callback = function() use($leaves, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($leaves as $leave) {
                fputcsv($file, [
                    $leave->employee->name ?? 'N/A',
                    ucfirst($leave->leave_type),
                    $leave->start_date,
                    $leave->end_date,
                    $leave->total_days,
                    $leave->status,
                    $leave->reason
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Edit and Update Employee Leave Balances (CL, SL, AL / PL, Unpaid).
     */
    public function updateBalance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'year' => 'required|integer',
            'casual_leave_total' => 'required|integer|min:0',
            'casual_leave_used' => 'nullable|integer|min:0',
            'sick_leave_total' => 'required|integer|min:0',
            'sick_leave_used' => 'nullable|integer|min:0',
            'annual_leave_total' => 'required|integer|min:0',
            'annual_leave_used' => 'nullable|integer|min:0',
        ]);

        $employeeId = $request->input('employee_id');
        $year = $request->input('year', date('Y'));
        
        $balanceData = [
            'casual_leave_total' => $request->input('casual_leave_total', 12),
            'casual_leave_used'  => $request->input('casual_leave_used', 0),
            'sick_leave_total'   => $request->input('sick_leave_total', 12),
            'sick_leave_used'    => $request->input('sick_leave_used', 0),
            'annual_leave_total' => $request->input('annual_leave_total', 15),
            'annual_leave_used'  => $request->input('annual_leave_used', 0),
        ];

        if ($employeeId === 'all') {
            $employees = Employee::all();
            foreach ($employees as $emp) {
                LeaveBalance::updateOrCreate(
                    ['employee_id' => $emp->id, 'year' => $year],
                    $balanceData
                );
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Leave balances updated successfully for all employees."
                ]);
            }
            return redirect()->back()->with('success', "Leave balances updated successfully for all employees.");
        } else {
            // Validate specific employee exists
            $request->validate(['employee_id' => 'exists:employees,id']);
            
            $balance = LeaveBalance::updateOrCreate(
                ['employee_id' => $employeeId, 'year' => $year],
                $balanceData
            );

            $employee = Employee::find($employeeId);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Leave balances updated successfully for {$employee->name}.",
                    'data' => $balance
                ]);
            }
            return redirect()->back()->with('success', "Leave balances updated successfully for {$employee->name}.");
        }
    }
}