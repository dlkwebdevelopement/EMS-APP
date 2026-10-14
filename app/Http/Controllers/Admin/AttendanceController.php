<?php
// app/Http/Controllers/Admin/AttendanceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // =========================================================================
    // INDEX — daily view
    // =========================================================================

    public function index(Request $request)
    {
        // Auto mark absent if shift ended and check_out is missing
        Attendance::autoMarkMissedCheckouts();

        $date  = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $month = (int) $date->month;
        $year  = (int) $date->year;

        // Today's attendance records with workShift relation
        $attendances = Attendance::with(['employee', 'workShift'])
            ->whereDate('date', $date)
            ->paginate(10)->withQueryString();

        $employees = Employee::where('is_active', true)->get();

        // Monthly summary expected by the index view's summary cards
        $report = [];

        foreach ($employees as $employee) {
            $monthAttendance = $employee->attendance()
                ->whereMonth('date', $month)
                ->whereYear('date',  $year)
                ->get();

            $report[] = [
                'employee' => $employee,
                'present'  => $monthAttendance->where('status', 'present')->count(),
                'absent'   => $monthAttendance->where('status', 'absent')->count(),
                'late'     => $monthAttendance->where('status', 'late')->count(),
                'half_day' => $monthAttendance->where('status', 'half_day')->count(),
                'holiday'  => $monthAttendance->where('status', 'holiday')->count(),
                'overtime' => round($monthAttendance->sum('overtime_hours'), 2),
            ];
        }

        return view('admin.attendance.index', compact(
            'attendances', 'employees', 'date', 'month', 'year', 'report'
        ));
    }

    // =========================================================================
    // MARK — single record
    // =========================================================================

    public function mark(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'status'      => 'required|in:present,absent,late,half_day,holiday',
        ]);

        $date = Carbon::parse($request->date);
        $dateStr = $date->format('Y-m-d');
        $existing = Attendance::where('employee_id', $request->employee_id)
            ->where('date', $dateStr)
            ->first();

        if ($request->status === 'absent' && $existing && $existing->check_in && !$request->boolean('force_absent')) {
            $checkInTime = Carbon::parse($existing->check_in)->format('g:i:s A');
            return back()->with('error', "Alert: Cannot mark as Absent! This employee has already clocked in and worked on {$dateStr} (Checked in at {$checkInTime}).")->withInput();
        }

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date'        => $dateStr,
            ],
            [
                'status'           => $request->status,
                'check_in_method'  => 'manual',
                'remarks'          => $request->remarks,
            ]
        );

        if ($request->status === 'absent') {
            $attendance->check_in = null;
            $attendance->check_out = null;
            $attendance->break_start = null;
            $attendance->break_end = null;
            $attendance->lunch_start = null;
            $attendance->lunch_end = null;
            $attendance->save();
        } else {
            if ($request->filled('check_in_hour')) {
                $inStr = $date->format('Y-m-d') . ' ' . $request->check_in_hour . ':' . ($request->check_in_min ?? '00') . ':00 ' . ($request->check_in_ampm ?? 'AM');
                $attendance->check_in = Carbon::parse($inStr);
            } elseif ($request->check_in) {
                $attendance->check_in = Carbon::parse($date->format('Y-m-d') . ' ' . $request->check_in);
            }

            if ($request->filled('check_out_hour')) {
                $outStr = $date->format('Y-m-d') . ' ' . $request->check_out_hour . ':' . ($request->check_out_min ?? '00') . ':00 ' . ($request->check_out_ampm ?? 'PM');
                $attendance->check_out = Carbon::parse($outStr);
            } elseif ($request->check_out) {
                $attendance->check_out = Carbon::parse($date->format('Y-m-d') . ' ' . $request->check_out);
            }

            if ($request->filled('tea_break_mins')) {
                $attendance->break_duration_seconds = (int)$request->tea_break_mins * 60;
                $attendance->total_break_minutes = (int)$request->tea_break_mins;
            }
            
            if ($request->filled('lunch_break_mins')) {
                $attendance->lunch_duration_seconds = (int)$request->lunch_break_mins * 60;
                $attendance->total_break_minutes = ($attendance->total_break_minutes ?? 0) + (int)$request->lunch_break_mins;
            }
            
            // Assign Shift ID if missing
            if (!$attendance->shift_id && $attendance->employee) {
                $attendance->shift_id = $attendance->employee->shift_id;
            }
            $shift = $attendance->workShift ?? ($attendance->employee ? $attendance->employee->workShift : null);
            $shiftBreakMins = $shift ? $shift->break_duration_minutes : 0;
            $shiftEndTime = $shift ? $shift->end_time : '18:00:00';

            // Auto-assign Break Time if completely blank
            if (!$request->filled('tea_break_mins') && (!$request->filled('lunch_break_mins'))) {
                $attendance->total_break_minutes = $shiftBreakMins;
            }

            if ($request->filled('overtime_hours')) {
                $attendance->overtime_hours = (float)$request->overtime_hours;
                $attendance->overtime_seconds = (int)($attendance->overtime_hours * 3600);
            } else {
                // Auto calculate overtime if no manual input
                if ($attendance->check_out && $attendance->check_in) {
                    $shiftEndStr = Carbon::parse($attendance->date)->format('Y-m-d') . ' ' . $shiftEndTime;
                    $shiftEnd = Carbon::parse($shiftEndStr);
                    if ($attendance->check_out->gt($shiftEnd)) {
                        $overtimeSecs = abs($shiftEnd->diffInSeconds($attendance->check_out));
                        $attendance->overtime_seconds = $overtimeSecs;
                        $attendance->overtime_hours = round($overtimeSecs / 3600, 2);
                    } else {
                        $attendance->overtime_seconds = 0;
                        $attendance->overtime_hours = 0;
                    }
                }
            }

            $attendance->save();
        }

        return redirect()->back()->with('success', 'Attendance marked successfully.');
    }

    // =========================================================================
    // REPORT — monthly summary per employee
    // =========================================================================

    public function report(Request $request)
    {
        $month = (int) ($request->month ?? Carbon::now()->month);
        $year  = (int) ($request->year  ?? Carbon::now()->year);

        $employees = Employee::where('is_active', true)->get();
        $report    = [];

        foreach ($employees as $employee) {
            $attendance = $employee->attendance()
                ->whereMonth('date', $month)
                ->whereYear('date',  $year)
                ->get();

            $report[] = [
                'employee' => $employee,
                'present'  => $attendance->where('status', 'present')->count(),
                'absent'   => $attendance->where('status', 'absent')->count(),
                'late'     => $attendance->where('status', 'late')->count(),
                'half_day' => $attendance->where('status', 'half_day')->count(),
                'holiday'  => $attendance->where('status', 'holiday')->count(),
                'overtime' => $attendance->sum('overtime_hours'),
            ];
        }

        return view('admin.attendance.report', compact('report', 'month', 'year'));
    }

    // =========================================================================
    // BULK MARK
    // =========================================================================

    public function bulkMark(Request $request)
    {
        $request->validate([
            'date'                          => 'required|date',
            'attendances'                   => 'required|array',
            'attendances.*.employee_id'     => 'required|exists:employees,id',
            'attendances.*.status'          => 'required|in:present,absent,late,half_day',
        ]);

        $date = Carbon::parse($request->date);

        foreach ($request->attendances as $data) {
            Attendance::updateOrCreate(
                [
                    'employee_id' => $data['employee_id'],
                    'date'        => $date->format('Y-m-d'),
                ],
                [
                    'status'          => $data['status'],
                    'check_in_method' => 'manual',
                    'remarks'         => $data['remarks'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Bulk attendance marked successfully.');
    }
}