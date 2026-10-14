<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $leaves = Leave::where('employee_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($leaves);
    }

    public function balances(Request $request)
    {
        $employee = $request->user();
        
        $balance = LeaveBalance::firstOrCreate(
            ['employee_id' => $employee->id, 'year' => (int) date('Y')],
            [
                'sick_leave_total' => 12,
                'sick_leave_used' => 0,
                'casual_leave_total' => 12,
                'casual_leave_used' => 0,
                'annual_leave_total' => 15,
                'annual_leave_used' => 0,
                'unpaid_leave_total' => 0,
                'unpaid_leave_used' => 0,
            ]
        );
        
        return response()->json([
            'year' => date('Y'),
            'sick_leave' => [
                'total' => $balance->sick_leave_total,
                'used' => $balance->sick_leave_used,
                'available' => max(0, $balance->sick_leave_total - $balance->sick_leave_used),
            ],
            'casual_leave' => [
                'total' => $balance->casual_leave_total,
                'used' => $balance->casual_leave_used,
                'available' => max(0, $balance->casual_leave_total - $balance->casual_leave_used),
            ],
            'annual_leave' => [
                'total' => $balance->annual_leave_total,
                'used' => $balance->annual_leave_used,
                'available' => max(0, $balance->annual_leave_total - $balance->annual_leave_used),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string|in:sick,casual,annual,unpaid,maternity,paternity,other',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|min:5',
        ]);

        $employee = $request->user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $balance = LeaveBalance::firstOrCreate(
            ['employee_id' => $employee->id, 'year' => (int) date('Y')],
            [
                'sick_leave_total' => 12,
                'sick_leave_used' => 0,
                'casual_leave_total' => 12,
                'casual_leave_used' => 0,
                'annual_leave_total' => 15,
                'annual_leave_used' => 0,
                'unpaid_leave_total' => 0,
                'unpaid_leave_used' => 0,
            ]
        );

        $mappedType = in_array($request->leave_type, ['sick', 'casual', 'annual']) ? $request->leave_type : 'casual';
        $typeField = $mappedType . '_leave_used';
        $totalField = $mappedType . '_leave_total';

        if (in_array($request->leave_type, ['sick', 'casual', 'annual'])) {
            $available = $balance->$totalField - $balance->$typeField;
            if ($totalDays > $available) {
                return response()->json([
                    'message' => "Insufficient {$request->leave_type} leave balance. You have {$available} days available."
                ], 400);
            }
        }

        $dbLeaveType = in_array($request->leave_type, ['sick', 'casual', 'annual', 'unpaid', 'maternity', 'paternity']) ? $request->leave_type : 'casual';

        $leave = Leave::create([
            'employee_id' => $employee->id,
            'leave_type'  => $dbLeaveType,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'total_days'  => $totalDays,
            'reason'      => $request->reason,
            'status'      => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave application submitted successfully! Pending HR Approval.',
            'leave'   => $leave
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $leave = Leave::where('employee_id', $request->user()->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$leave) {
            return response()->json(['message' => 'Leave application not found or cannot be cancelled'], 404);
        }

        $leave->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Leave application cancelled successfully']);
    }
}
