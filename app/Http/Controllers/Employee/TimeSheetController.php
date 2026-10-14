<?php
// app/Http/Controllers/Employee/TimeSheetController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TimeSheet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeSheetController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeSheet::where('employee_id', auth()->id());

        if ($request->month) {
            $query->whereMonth('date', $request->month);
        }
        if ($request->year) {
            $query->whereYear('date', $request->year);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $timeSheets = $query->orderBy('date', 'desc')->paginate(20);
        
        return view('employee.time-sheets.index', compact('timeSheets'));
    }

    public function create()
    {
        return view('employee.time-sheets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_hours' => 'required|numeric|min:0|max:4',
            'project' => 'nullable|string|max:100',
            'task_description' => 'required|string|min:10'
        ]);

        $startTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endTime = Carbon::parse($request->date . ' ' . $request->end_time);
        $totalHours = TimeSheet::calculateTotalHours($startTime, $endTime, $request->break_hours);

        TimeSheet::create([
            'employee_id' => auth()->id(),
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_hours' => $request->break_hours,
            'total_hours' => $totalHours,
            'project' => $request->project,
            'task_description' => $request->task_description,
            'status' => 'pending'
        ]);

        return redirect()->route('employee.time-sheets.index')
            ->with('success', 'Time sheet submitted successfully.');
    }

    public function edit(TimeSheet $timeSheet)
    {
        if ($timeSheet->employee_id != auth()->id()) {
            abort(403);
        }
        
        if ($timeSheet->status != 'pending') {
            return redirect()->back()->with('error', 'Cannot edit approved/rejected time sheet.');
        }

        return view('employee.time-sheets.edit', compact('timeSheet'));
    }

    public function update(Request $request, TimeSheet $timeSheet)
    {
        if ($timeSheet->employee_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_hours' => 'required|numeric|min:0|max:4',
            'project' => 'nullable|string|max:100',
            'task_description' => 'required|string|min:10'
        ]);

        $startTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endTime = Carbon::parse($request->date . ' ' . $request->end_time);
        $totalHours = TimeSheet::calculateTotalHours($startTime, $endTime, $request->break_hours);

        $timeSheet->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_hours' => $request->break_hours,
            'total_hours' => $totalHours,
            'project' => $request->project,
            'task_description' => $request->task_description,
        ]);

        return redirect()->route('employee.time-sheets.index')
            ->with('success', 'Time sheet updated successfully.');
    }

    public function destroy(TimeSheet $timeSheet)
    {
        if ($timeSheet->employee_id != auth()->id()) {
            abort(403);
        }

        if ($timeSheet->status != 'pending') {
            return redirect()->back()->with('error', 'Cannot delete approved/rejected time sheet.');
        }

        $timeSheet->delete();
        return redirect()->back()->with('success', 'Time sheet deleted successfully.');
    }

    public function weekly()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $timeSheets = TimeSheet::where('employee_id', auth()->id())
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get();

        $totalHours = $timeSheets->sum('total_hours');
        $pendingHours = $timeSheets->where('status', 'pending')->sum('total_hours');
        $approvedHours = $timeSheets->where('status', 'approved')->sum('total_hours');

        return view('employee.time-sheets.weekly', compact('timeSheets', 'totalHours', 'pendingHours', 'approvedHours', 'startOfWeek', 'endOfWeek'));
    }

    public function monthly()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $timeSheets = TimeSheet::where('employee_id', auth()->id())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $totalHours = $timeSheets->sum('total_hours');
        $workingDays = $timeSheets->count();

        return view('employee.time-sheets.monthly', compact('timeSheets', 'totalHours', 'workingDays', 'month', 'year'));
    }
}