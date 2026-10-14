<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkShiftController extends Controller
{
    public function index()
    {
        $employee = Auth::guard('sanctum')->user();
        if (!$employee) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $shifts = WorkShift::with('employees')->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $shifts
        ]);
    }

    public function show($id)
    {
        $employee = Auth::guard('sanctum')->user();
        if (!$employee) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $shift = WorkShift::with('employees')->find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $shift
        ]);
    }

    public function store(Request $request)
    {
        $employee = Auth::guard('sanctum')->user();
        if (!$employee) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $isAdmin = $employee->role === 'admin' || $employee->role === 'manager' || $employee->is_admin == 1;
        if (!$isAdmin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $this->validateShift($request);
        $data = $this->prepareShiftData($request, $validated);

        $shift = WorkShift::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Work Shift created successfully',
            'data' => $shift
        ], 210); // Laravel default 201
    }

    public function update(Request $request, $id)
    {
        $employee = Auth::guard('sanctum')->user();
        if (!$employee) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $isAdmin = $employee->role === 'admin' || $employee->role === 'manager' || $employee->is_admin == 1;
        if (!$isAdmin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $shift = WorkShift::find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        $validated = $this->validateShift($request, $id);
        $data = $this->prepareShiftData($request, $validated);

        $shift->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Work Shift updated successfully',
            'data' => $shift
        ]);
    }

    public function destroy($id)
    {
        $employee = Auth::guard('sanctum')->user();
        if (!$employee) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $isAdmin = $employee->role === 'admin' || $employee->role === 'manager' || $employee->is_admin == 1;
        if (!$isAdmin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $shift = WorkShift::find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        $shift->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Work Shift deleted successfully'
        ]);
    }

    protected function validateShift(Request $request, $id = null)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:work_shifts,code,' . ($id ?? 'NULL'),
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'grace_period_minutes' => 'nullable|integer|min:0|max:120',
            'break_details_policy' => 'nullable|string',
            'first_weekly_off' => 'nullable|string',
            'second_weekly_off' => 'nullable|string',
            'second_wo_type' => 'nullable|string',
            'half_day_shift' => 'nullable|string',
            'remove_duplicate_punch' => 'nullable|string',
            'max_early_hour_allow' => 'nullable|string',
            'max_ot_allow' => 'nullable|string',
            'no_of_present_for_weekly_off' => 'nullable|numeric|min:0',
            'single_punch_policy' => 'nullable|string',
        ]);
    }

    protected function prepareShiftData(Request $request, array $validated)
    {
        $data = $validated;

        // Parse main shift times
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $data['start_time'] = $start->format('H:i:s');
        $data['end_time'] = $end->format('H:i:s');

        // Calculate shift duration
        if ($end->lt($start)) {
            $end->addDay();
        }
        $diff = $start->diff($end);
        $data['shift_duration'] = sprintf('%02d:%02d', $diff->h + ($diff->days * 24), $diff->i);

        // Checkboxes
        $data['break1_enabled'] = filter_var($request->input('break1_enabled'), FILTER_VALIDATE_BOOLEAN);
        $data['break2_enabled'] = filter_var($request->input('break2_enabled'), FILTER_VALIDATE_BOOLEAN);
        $data['break3_enabled'] = filter_var($request->input('break3_enabled'), FILTER_VALIDATE_BOOLEAN);
        $data['break4_enabled'] = filter_var($request->input('break4_enabled'), FILTER_VALIDATE_BOOLEAN);
        $data['break5_enabled'] = filter_var($request->input('break5_enabled'), FILTER_VALIDATE_BOOLEAN);
        $data['break6_enabled'] = filter_var($request->input('break6_enabled'), FILTER_VALIDATE_BOOLEAN);
        $data['mark_absent_aaa'] = filter_var($request->input('mark_absent_aaa'), FILTER_VALIDATE_BOOLEAN);
        $data['mark_absent_l_wo_l'] = filter_var($request->input('mark_absent_l_wo_l'), FILTER_VALIDATE_BOOLEAN);

        // Break 1 Durations
        if ($data['break1_enabled'] && $request->break1_begin_time && $request->break1_end_time) {
            $b1Start = Carbon::parse($request->break1_begin_time);
            $b1End = Carbon::parse($request->break1_end_time);
            $data['break1_begin_time'] = $b1Start->format('H:i:s');
            $data['break1_end_time'] = $b1End->format('H:i:s');
            if ($b1End->lt($b1Start)) {
                $b1End->addDay();
            }
            $b1Diff = $b1Start->diff($b1End);
            $data['break1_duration'] = sprintf('%02d:%02d', $b1Diff->h + ($b1Diff->days * 24), $b1Diff->i);
        } else {
            $data['break1_begin_time'] = null;
            $data['break1_end_time'] = null;
            $data['break1_duration'] = null;
        }

        // Break 2 Durations
        if ($data['break2_enabled'] && $request->break2_begin_time && $request->break2_end_time) {
            $b2Start = Carbon::parse($request->break2_begin_time);
            $b2End = Carbon::parse($request->break2_end_time);
            $data['break2_begin_time'] = $b2Start->format('H:i:s');
            $data['break2_end_time'] = $b2End->format('H:i:s');
            if ($b2End->lt($b2Start)) {
                $b2End->addDay();
            }
            $b2Diff = $b2Start->diff($b2End);
            $data['break2_duration'] = sprintf('%02d:%02d', $b2Diff->h + ($b2Diff->days * 24), $b2Diff->i);
        } else {
            $data['break2_begin_time'] = null;
            $data['break2_end_time'] = null;
            $data['break2_duration'] = null;
        }

        // Break 3 Durations
        if ($data['break3_enabled'] && $request->break3_begin_time && $request->break3_end_time) {
            $b3Start = Carbon::parse($request->break3_begin_time);
            $b3End = Carbon::parse($request->break3_end_time);
            $data['break3_begin_time'] = $b3Start->format('H:i:s');
            $data['break3_end_time'] = $b3End->format('H:i:s');
            if ($b3End->lt($b3Start)) {
                $b3End->addDay();
            }
            $b3Diff = $b3Start->diff($b3End);
            $data['break3_duration'] = sprintf('%02d:%02d', $b3Diff->h + ($b3Diff->days * 24), $b3Diff->i);
        } else {
            $data['break3_begin_time'] = null;
            $data['break3_end_time'] = null;
            $data['break3_duration'] = null;
        }

        // Break 4 Durations
        if ($data['break4_enabled'] && $request->break4_begin_time && $request->break4_end_time) {
            $b4Start = Carbon::parse($request->break4_begin_time);
            $b4End = Carbon::parse($request->break4_end_time);
            $data['break4_begin_time'] = $b4Start->format('H:i:s');
            $data['break4_end_time'] = $b4End->format('H:i:s');
            if ($b4End->lt($b4Start)) {
                $b4End->addDay();
            }
            $b4Diff = $b4Start->diff($b4End);
            $data['break4_duration'] = sprintf('%02d:%02d', $b4Diff->h + ($b4Diff->days * 24), $b4Diff->i);
        } else {
            $data['break4_begin_time'] = null;
            $data['break4_end_time'] = null;
            $data['break4_duration'] = null;
        }

        // Break 5 Durations
        if ($data['break5_enabled'] && $request->break5_begin_time && $request->break5_end_time) {
            $b5Start = Carbon::parse($request->break5_begin_time);
            $b5End = Carbon::parse($request->break5_end_time);
            $data['break5_begin_time'] = $b5Start->format('H:i:s');
            $data['break5_end_time'] = $b5End->format('H:i:s');
            if ($b5End->lt($b5Start)) {
                $b5End->addDay();
            }
            $b5Diff = $b5Start->diff($b5End);
            $data['break5_duration'] = sprintf('%02d:%02d', $b5Diff->h + ($b5Diff->days * 24), $b5Diff->i);
        } else {
            $data['break5_begin_time'] = null;
            $data['break5_end_time'] = null;
            $data['break5_duration'] = null;
        }

        // Break 6 Durations
        if ($data['break6_enabled'] && $request->break6_begin_time && $request->break6_end_time) {
            $b6Start = Carbon::parse($request->break6_begin_time);
            $b6End = Carbon::parse($request->break6_end_time);
            $data['break6_begin_time'] = $b6Start->format('H:i:s');
            $data['break6_end_time'] = $b6End->format('H:i:s');
            if ($b6End->lt($b6Start)) {
                $b6End->addDay();
            }
            $b6Diff = $b6Start->diff($b6End);
            $data['break6_duration'] = sprintf('%02d:%02d', $b6Diff->h + ($b6Diff->days * 24), $b6Diff->i);
        } else {
            $data['break6_begin_time'] = null;
            $data['break6_end_time'] = null;
            $data['break6_duration'] = null;
        }

        // Handle second weekly off weeks array or string
        $weeks = $request->input('second_weekly_off_weeks', []);
        if (is_array($weeks)) {
            $data['second_weekly_off_weeks'] = implode(',', $weeks);
        } else {
            $data['second_weekly_off_weeks'] = $weeks;
        }

        return $data;
    }
}
