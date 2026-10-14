<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkShiftController extends Controller
{
    public function index()
    {
        $shifts = WorkShift::latest()->paginate(10);
        $permissions = \App\Models\EmployeePermission::with('employee')->orderBy('date', 'desc')->paginate(15);
        return view('admin.shifts.index', compact('shifts', 'permissions'));
    }

    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateShift($request);

        $data = $this->prepareShiftData($request, $validated);

        WorkShift::create($data);

        return redirect()->route('admin.shifts.index')->with('success', 'Work Shift created successfully.');
    }

    public function edit(WorkShift $shift)
    {
        // Decode second weekly off weeks from comma separated string
        $selectedWeeks = $shift->second_weekly_off_weeks ? explode(',', $shift->second_weekly_off_weeks) : [];
        return view('admin.shifts.edit', compact('shift', 'selectedWeeks'));
    }

    public function update(Request $request, WorkShift $shift)
    {
        $validated = $this->validateShift($request, $shift->id);

        $data = $this->prepareShiftData($request, $validated);

        $shift->update($data);

        return redirect()->route('admin.shifts.index')->with('success', 'Work Shift updated successfully.');
    }

    public function destroy(WorkShift $shift)
    {
        $shift->delete();
        return redirect()->route('admin.shifts.index')->with('success', 'Work Shift deleted successfully.');
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
        $data['break1_enabled'] = $request->has('break1_enabled');
        $data['break2_enabled'] = $request->has('break2_enabled');
        $data['break3_enabled'] = $request->has('break3_enabled');
        $data['break4_enabled'] = $request->has('break4_enabled');
        $data['break5_enabled'] = $request->has('break5_enabled');
        $data['break6_enabled'] = $request->has('break6_enabled');
        $data['mark_absent_aaa'] = $request->has('mark_absent_aaa');
        $data['mark_absent_l_wo_l'] = $request->has('mark_absent_l_wo_l');
        $data['is_2hour_permission_enabled'] = $request->has('is_2hour_permission_enabled');

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

        // Handle second weekly off weeks
        $data['second_weekly_off_weeks'] = implode(',', $request->input('second_weekly_off_weeks', []));

        return $data;
    }

    public function approvePermission(\App\Models\EmployeePermission $permission)
    {
        $permission->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Permission request approved successfully.');
    }

    public function rejectPermission(\App\Models\EmployeePermission $permission)
    {
        $permission->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Permission request rejected.');
    }
}
