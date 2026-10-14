<?php
// app/Http/Controllers/Employee/LeaveController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display a listing of personal leave requests.
     */
    public function index()
    {
        $employee = Auth::user();
        $leaves = Leave::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $leaveBalance = $employee->currentLeaveBalance;
            
        return view('employee.leaves', compact('leaves', 'leaveBalance'));
    }

    /**
     * Show form for creating leave.
     */
    public function create()
    {
        $employee = Auth::user();
        $leaveBalance = $employee->currentLeaveBalance;
        return view('employee.leaves.create', compact('leaveBalance'));
    }

    /**
     * Store new leave request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:sick,casual,annual,unpaid,maternity,paternity',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $employee = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Check for overlapping leaves
        $overlapping = Leave::where('employee_id', $employee->id)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        if ($overlapping) {
            return back()->with('error', 'You already have a leave request for these dates.');
        }

        // Check leave balance
        if ($request->leave_type != 'unpaid') {
            $balance = $employee->currentLeaveBalance;
            $available = 0;
            switch ($request->leave_type) {
                case 'sick':
                    $available = $balance->sick_leave_total - $balance->sick_leave_used;
                    break;
                case 'casual':
                    $available = $balance->casual_leave_total - $balance->casual_leave_used;
                    break;
                case 'annual':
                    $available = $balance->annual_leave_total - $balance->annual_leave_used;
                    break;
                default:
                    $available = 999; // For other types like maternity/paternity
                    break;
            }

            if ($totalDays > $available) {
                return back()->with('error', "Insufficient leave balance. Available: {$available} days");
            }
        }

        // Handle attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        Leave::create([
            'employee_id' => $employee->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending'
        ]);

        return redirect()->route('employee.leaves')
            ->with('success', 'Leave application submitted successfully.');
    }

    /**
     * Show leave details.
     */
    public function show(Leave $leave)
    {
        if ($leave->employee_id != Auth::id()) {
            abort(403);
        }
        return view('employee.leaves.show', compact('leave'));
    }

    /**
     * Cancel personal leave request.
     */
    public function cancel(Leave $leave)
    {
        if ($leave->employee_id != Auth::id()) {
            abort(403);
        }

        if ($leave->status != 'pending') {
            return back()->with('error', 'Only pending leaves can be cancelled.');
        }

        $leave->status = 'cancelled';
        $leave->save();

        return redirect()->route('employee.leaves')
            ->with('success', 'Leave cancelled successfully.');
    }

    /**
     * View personal leave balance.
     */
    public function balance()
    {
        $employee = Auth::user();
        $leaveBalance = $employee->currentLeaveBalance;
        return view('employee.leaves.balance', compact('leaveBalance'));
    }
}
