<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function status(Request $request)
    {
        $employee = $request->user();
        $today = Carbon::today()->toDateString();
        
        // Auto mark absent if shift ended and check_out is missing
        Attendance::autoMarkMissedCheckouts($employee->id);
        
        $attendance = Attendance::with('workShift')
            ->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();
            
        // Resolve shift for this employee
        $shiftForStatus = \App\Models\WorkShift::find($employee->shift_id) ?? \App\Models\WorkShift::where('is_default', true)->first();
        $todayForStatus = Carbon::today()->toDateString();
        $shiftEndForStatus = $shiftForStatus
            ? Carbon::parse($todayForStatus . ' ' . $shiftForStatus->end_time)
            : Carbon::parse($todayForStatus . ' 18:00:00');
        $shiftEnded = Carbon::now()->gt($shiftEndForStatus);

        return response()->json([
            'attendance'      => $attendance,
            'is_clocked_in'   => $attendance && !$attendance->check_out,
            'is_clocked_out'  => $attendance && $attendance->check_out,
            'on_break'        => $attendance && $attendance->break_start && !$attendance->break_end,
            'on_lunch'        => $attendance && $attendance->lunch_start && !$attendance->lunch_end,
            'shift_ended'     => $shiftEnded && !($attendance && !$attendance->check_out),
            'shift_end_time'  => $shiftForStatus ? $shiftForStatus->end_time : '18:00:00',
            'shift_name'      => $shiftForStatus ? $shiftForStatus->name : 'General Shift',
        ]);
    }

    public function clockIn(Request $request)
    {
        $employee = $request->user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $existing = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already clocked in for today'], 400);
        }

        // Get Shift or Default
        $shift = WorkShift::find($employee->shift_id) ?? WorkShift::where('is_default', true)->first();
        $shiftStart = $shift ? Carbon::parse($today . ' ' . $shift->start_time) : Carbon::parse($today . ' 09:00:00');
        $shiftEnd   = $shift ? Carbon::parse($today . ' ' . $shift->end_time)   : Carbon::parse($today . ' 18:00:00');
        $graceMinutes = $shift ? ($shift->grace_period_minutes ?? 15) : 15;
        $shiftName    = $shift ? $shift->name : 'General Shift';

        // ── BLOCK: Shift has already ended ─────────────────────────────────
        if ($now->gt($shiftEnd)) {
            $endFormatted = \Carbon\Carbon::parse($shiftEnd)->format('g:i A');
            return response()->json([
                'success'     => false,
                'shift_ended' => true,
                'message'     => "Your shift time has ended (\"$shiftName\" ends at $endFormatted). Check-in is not allowed.",
            ], 403);
        }

        // Calculate Late Minutes
        $lateMinutes = 0;
        $status = 'present';
        if ($now->gt($shiftStart->copy()->addMinutes($graceMinutes))) {
            $lateMinutes = $shiftStart->diffInMinutes($now);
            $status = 'late';
        }

        $attendance = Attendance::create([
            'employee_id'      => $employee->id,
            'shift_id'         => $shift ? $shift->id : null,
            'date'             => $today,
            'check_in'         => $now,
            'status'           => $status,
            'late_minutes'     => $lateMinutes,
            'check_in_method'  => 'manual',
            'check_in_location'=> $request->location ?? 'Office',
            'remarks'          => $request->remarks
        ]);

        return response()->json([
            'success' => true,
            'message' => $status === 'late'
                ? (function() use ($lateMinutes) {
                    $lh = floor($lateMinutes / 60);
                    $lm = $lateMinutes % 60;
                    $lateStr = ($lh > 0 && $lm > 0) ? "{$lh}h {$lm}m" : ($lh > 0 ? "{$lh}h" : "{$lm}m");
                    return "⏰ Clocked in — Late by {$lateStr}";
                  })()
                : '✅ Clocked in successfully',
            'attendance' => $attendance
        ]);
    }

    public function breakStart(Request $request)
    {
        $employee = $request->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Active clock-in required to start break'], 400);
        }

        $attendance->update([
            'break_start' => now(),
            'break_end' => null
        ]);

        return response()->json(['success' => true, 'message' => 'Break started', 'attendance' => $attendance]);
    }

    public function breakEnd(Request $request)
    {
        $employee = $request->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->whereNotNull('break_start')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No active break found'], 400);
        }

        $now = now();
        $breakSecs = Carbon::parse($attendance->break_start)->diffInSeconds($now);
        $totalBreakSecs = ($attendance->break_duration_seconds ?? 0) + $breakSecs;
        $totalBreakMins = ceil($totalBreakSecs / 60);

        $attendance->update([
            'break_end' => $now,
            'break_duration_seconds' => $totalBreakSecs,
            'total_break_minutes' => $totalBreakMins
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Break ended (' . Attendance::formatSeconds($breakSecs) . ')', 
            'attendance' => $attendance
        ]);
    }

    public function lunchStart(Request $request)
    {
        $employee = $request->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Active clock-in required for lunch'], 400);
        }

        $attendance->update([
            'lunch_start' => now(),
            'lunch_end' => null
        ]);

        return response()->json(['success' => true, 'message' => 'Lunch break started', 'attendance' => $attendance]);
    }

    public function lunchEnd(Request $request)
    {
        $employee = $request->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->whereNotNull('lunch_start')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No active lunch break found'], 400);
        }

        $now = now();
        $lunchSecs = Carbon::parse($attendance->lunch_start)->diffInSeconds($now);
        $totalLunchSecs = ($attendance->lunch_duration_seconds ?? 0) + $lunchSecs;

        $attendance->update([
            'lunch_end' => $now,
            'lunch_duration_seconds' => $totalLunchSecs
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Lunch break ended (' . Attendance::formatSeconds($lunchSecs) . ')', 
            'attendance' => $attendance
        ]);
    }

    public function clockOut(Request $request)
    {
        $employee = $request->user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No active clock-in found for today'], 404);
        }

        $shift = WorkShift::find($attendance->shift_id ?? $employee->shift_id) ?? WorkShift::where('is_default', true)->first();
        $shiftEnd = $shift ? Carbon::parse($today . ' ' . $shift->end_time) : Carbon::parse($today . ' 18:00:00');

        $earlyExitMinutes = 0;
        $overtimeSeconds = 0;
        $overtimeHours = 0;

        if ($now->lt($shiftEnd)) {
            $earlyExitMinutes = $now->diffInMinutes($shiftEnd);
        } elseif ($now->gt($shiftEnd)) {
            $overtimeSeconds = $shiftEnd->diffInSeconds($now);
            $overtimeHours = round($overtimeSeconds / 3600, 2);
        }

        $status = $attendance->late_minutes > 0 ? 'late' : 'present';

        $attendance->update([
            'check_out' => $now,
            'status' => $status,
            'early_exit_minutes' => $earlyExitMinutes,
            'overtime_seconds' => $overtimeSeconds,
            'overtime_hours' => $overtimeHours,
            'check_out_method' => 'manual',
            'check_out_location' => $request->location ?? 'Office',
            'remarks' => trim(str_replace('Auto-marked absent: Missed check-out', '', $attendance->remarks))
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clocked out successfully (' . Attendance::formatSeconds($overtimeSeconds) . ' OT)',
            'attendance' => $attendance
        ]);
    }

    public function history(Request $request)
    {
        Attendance::autoMarkMissedCheckouts($request->user()->id);

        $history = Attendance::with('workShift')
            ->where('employee_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
            
        return response()->json($history);
    }
}
