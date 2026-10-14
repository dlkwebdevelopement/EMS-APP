<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display attendance dashboard for employee
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        $today = Carbon::today();
        
        // Check if already checked in today
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();
        
        // Get recent attendance records
        $recentAttendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
        
        // Get monthly summary
        $monthlySummary = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->get();
        
        $presentDays = $monthlySummary->where('status', 'present')->count();
        $lateDays = $monthlySummary->where('status', 'late')->count();
        $absentDays = $monthlySummary->where('status', 'absent')->count();
        
        // Calculate total working hours this month
        $totalHours = 0;
        foreach ($monthlySummary as $attendance) {
            if ($attendance->check_in && $attendance->check_out) {
                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $totalHours += $checkIn->diffInHours($checkOut);
            }
        }
        
        return view('employee.attendance.index', compact(
            'employee',
            'todayAttendance',
            'recentAttendances',
            'presentDays',
            'lateDays',
            'absentDays',
            'totalHours'
        ));
    }
    
    /**
     * Get monthly attendance data for AJAX
     */
    public function monthly(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();
        
        if ($request->ajax()) {
            return response()->json($attendances);
        }
        
        return view('employee.attendance.monthly', compact('attendances', 'month', 'year'));
    }
    
    /**
     * Check in for the day
     */
    public function checkIn(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        $today = Carbon::today();
        
        // Check if already checked in
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();
        
        if ($existingAttendance && $existingAttendance->check_in) {
            return back()->with('error', 'You have already checked in today.');
        }
        
        $checkInTime = Carbon::now();
        $lateThreshold = Carbon::today()->setTime(9, 30, 0); // 9:30 AM threshold
        $status = $checkInTime > $lateThreshold ? 'late' : 'present';
        
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today,
            ],
            [
                'check_in' => $checkInTime,
                'status' => $status,
                'ip_address' => $request->ip(),
            ]
        );
        
        return back()->with('success', 'Checked in successfully at ' . $checkInTime->format('h:i A'));
    }
    
    /**
     * Check out for the day
     */
    public function checkOut(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        $today = Carbon::today();
        
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();
        
        if (!$attendance) {
            return back()->with('error', 'You must check in first.');
        }
        
        if ($attendance->check_out) {
            return back()->with('error', 'You have already checked out today.');
        }
        
        $checkOutTime = Carbon::now();
        $attendance->check_out = $checkOutTime;
        
        // Calculate working hours
        $checkIn = Carbon::parse($attendance->check_in);
        $hoursWorked = $checkIn->diffInHours($checkOutTime);
        $attendance->total_hours = $hoursWorked;
        
        $attendance->save();
        
        return back()->with('success', 'Checked out successfully at ' . $checkOutTime->format('h:i A') . '. Total hours: ' . $hoursWorked);
    }
    
    /**
     * Get attendance summary for the year
     */
    public function summary(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        $year = $request->year ?? Carbon::now()->year;
        
        $monthlySummary = [];
        for ($month = 1; $month <= 12; $month++) {
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();
            
            $monthlySummary[$month] = [
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'total_hours' => $attendances->sum('total_hours'),
            ];
        }
        
        return view('employee.attendance.summary', compact('monthlySummary', 'year'));
    }
}