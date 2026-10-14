<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AssetAllocation;
use App\Models\Leave;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user();
        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Today's Attendance
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // Assets Count
        $assetsCount = AssetAllocation::where('employee_id', $employee->id)
            ->whereNull('returned_at')
            ->count();

        // Pending Leave Applications
        $pendingLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();

        // Permissions taken this month
        $permissionsThisMonth = \App\Models\EmployeePermission::where('employee_id', $employee->id)
            ->whereMonth('date', $thisMonth)
            ->whereYear('date', $thisYear)
            ->where('status', 'approved')
            ->count();

        // Leave Balance Summary
        $balance = $employee->current_leave_balance;
        $totalBalance = ($balance->sick_leave_total + $balance->casual_leave_total + $balance->annual_leave_total) 
            - ($balance->sick_leave_used + $balance->casual_leave_used + $balance->annual_leave_used);

        return response()->json([
            'employee' => [
                'name' => $employee->name,
                'designation' => $employee->designation,
                'department' => $employee->department,
                'profile_photo_url' => $employee->profile_photo ? asset('storage/' . $employee->profile_photo) : null,
                'work_shift' => $employee->workShift ? [
                    'name' => $employee->workShift->name,
                    'start_time' => $employee->workShift->start_time,
                    'end_time' => $employee->workShift->end_time,
                    'shift_duration' => $employee->workShift->shift_duration,
                    'break1_enabled' => $employee->workShift->break1_enabled,
                    'break1_begin_time' => $employee->workShift->break1_begin_time,
                    'break1_end_time' => $employee->workShift->break1_end_time,
                    'break1_duration' => $employee->workShift->break1_duration,
                    'break2_enabled' => $employee->workShift->break2_enabled,
                    'break2_begin_time' => $employee->workShift->break2_begin_time,
                    'break2_end_time' => $employee->workShift->break2_end_time,
                    'break2_duration' => $employee->workShift->break2_duration,
                ] : null,
            ],
            'stats' => [
                'is_clocked_in' => $attendance && !$attendance->check_out,
                'clock_in_time' => $attendance ? $attendance->check_in?->format('h:i A') : null,
                'total_hours_today' => $attendance ? $attendance->working_hours : 0,
                'total_minutes_today' => $attendance ? $attendance->working_minutes : 0,
                'active_assets' => $assetsCount,
                'pending_leave_requests' => $pendingLeaves,
                'permissions_this_month' => $permissionsThisMonth,
                'available_leave_days' => $totalBalance,
                'leave_breakdown' => [
                    'casual' => max(0, $balance->casual_leave_total - $balance->casual_leave_used),
                    'sick' => max(0, $balance->sick_leave_total - $balance->sick_leave_used),
                    'annual' => max(0, $balance->annual_leave_total - $balance->annual_leave_used),
                ]
            ],
            'recent_activity' => [
                'attendance' => Attendance::where('employee_id', $employee->id)
                    ->orderBy('date', 'desc')
                    ->limit(5)
                    ->get(),
            ]
        ]);
    }
}
