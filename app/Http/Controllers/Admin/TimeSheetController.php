<?php
// app/Http/Controllers/Admin/TimeSheetController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\TimeSheet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeSheetController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeSheet::with('employee');

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $timeSheets = $query->orderBy('date', 'desc')->paginate(20);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.time-sheets.index', compact('timeSheets', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        return view('admin.time-sheets.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'break_hours' => 'required|numeric|min:0|max:8',
            'task_description' => 'required|string|max:1000',
            'project' => 'nullable|string|max:255'
        ]);

        $total_hours = TimeSheet::calculateTotalHours(
            $request->start_time, 
            $request->end_time, 
            $request->break_hours
        );

        $timeSheet = TimeSheet::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_hours' => $request->break_hours,
            'total_hours' => $total_hours,
            'project' => $request->project,
            'task_description' => $request->task_description,
            'status' => 'approved',
            'approved_by' => auth()->guard('admin')->id(),
            'approved_at' => now(),
            'remarks' => 'Created by Administrator'
        ]);

        return redirect()->route('admin.time-sheets.index')->with('success', 'Time sheet entry created and approved successfully.');
    }

    public function show(TimeSheet $timeSheet)
    {
        $timeSheet->load('employee');
        return view('admin.time-sheets.show', compact('timeSheet'));
    }

    public function approve(TimeSheet $timeSheet)
    {
        $timeSheet->status = 'approved';
        $timeSheet->approved_by = auth()->guard('admin')->id();
        $timeSheet->approved_at = Carbon::now();
        $timeSheet->save();

        // Create notification
        $timeSheet->employee->notifications()->create([
            'title' => 'Time Sheet Approved',
            'message' => "Your time sheet for " . Carbon::parse($timeSheet->date)->format('d M Y') . " has been approved.",
            'type' => 'attendance'
        ]);

        return redirect()->back()->with('success', 'Time sheet approved successfully.');
    }

    public function reject(Request $request, TimeSheet $timeSheet)
    {
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);

        $timeSheet->status = 'rejected';
        $timeSheet->remarks = $request->remarks;
        $timeSheet->save();

        // Create notification
        $timeSheet->employee->notifications()->create([
            'title' => 'Time Sheet Rejected',
            'message' => "Your time sheet for " . Carbon::parse($timeSheet->date)->format('d M Y') . " was rejected. Reason: {$request->remarks}",
            'type' => 'attendance'
        ]);

        return redirect()->back()->with('success', 'Time sheet rejected successfully.');
    }

    public function destroy(TimeSheet $timeSheet)
    {
        $timeSheet->delete();
        return redirect()->back()->with('success', 'Time sheet deleted successfully.');
    }

    public function report(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $timeSheets = TimeSheet::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $summary = [];
        foreach ($timeSheets->groupBy('employee_id') as $employeeId => $sheets) {
            $employee = Employee::find($employeeId);
            $summary[] = [
                'employee' => $employee,
                'total_hours' => $sheets->sum('total_hours'),
                'total_days' => $sheets->count(),
                'approved_hours' => $sheets->where('status', 'approved')->sum('total_hours'),
                'pending_hours' => $sheets->where('status', 'pending')->sum('total_hours'),
            ];
        }

        return view('admin.time-sheets.report', compact('summary', 'month', 'year'));
    }
}