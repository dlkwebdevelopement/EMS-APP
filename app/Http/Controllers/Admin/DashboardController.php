<?php
// app/Http/Controllers/Admin/AuthController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            $admin->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput($request->only('email'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        // Auto mark absent if shift ended and check_out is missing
        \App\Models\Attendance::autoMarkMissedCheckouts();

        // Employee statistics
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('is_active', true)->count();
        $inactiveEmployees = Employee::where('is_active', false)->count();
        $newEmployeesMonth = Employee::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Today's attendance
        $presentToday = Attendance::where('date', now()->toDateString())
            ->where('status', 'present')
            ->count();
        $lateToday = Attendance::where('date', now()->toDateString())
            ->where('status', 'late')
            ->count();
        $absentToday = Attendance::where('date', now()->toDateString())
            ->where('status', 'absent')
            ->count();
        
        // Leave statistics
        $onLeaveToday = Leave::where('status', 'approved')
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString())
            ->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $pendingPermissions = \App\Models\EmployeePermission::where('status', 'pending')->count();
        $approvedLeavesThisMonth = Leave::where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $rejectedLeavesThisMonth = Leave::where('status', 'rejected')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Payroll statistics
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $totalPayrollAmount = Payroll::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('net_salary');
        $processedPayrolls = Payroll::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->count();
        
        // Recent attendance
        $recentAttendance = Attendance::with('employee')
            ->where('date', now()->toDateString())
            ->latest()
            ->take(10)
            ->get();

        // Get last 7 days attendance data for chart
        $last7Days = [];
        $attendanceData = [
            'present' => [],
            'late' => [],
            'absent' => []
        ];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last7Days[] = $date->format('d M');
            
            $dayAttendance = Attendance::where('date', $date->toDateString())->get();
            $attendanceData['present'][] = $dayAttendance->where('status', 'present')->count();
            $attendanceData['late'][] = $dayAttendance->where('status', 'late')->count();
            $attendanceData['absent'][] = $dayAttendance->where('status', 'absent')->count();
        }
        
        // Department distribution for chart
        $departments = Employee::select('department', DB::raw('count(*) as total'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->pluck('total', 'department')
            ->toArray();
        
        $departmentLabels = array_keys($departments);
        $departmentData = array_values($departments);

        return view('admin.dashboard', compact(
            'totalEmployees', 
            'activeEmployees',
            'inactiveEmployees',
            'newEmployeesMonth',
            'presentToday',
            'lateToday',
            'absentToday',
            'onLeaveToday', 
            'pendingLeaves',
            'pendingPermissions',
            'approvedLeavesThisMonth',
            'rejectedLeavesThisMonth',
            'totalPayrollAmount',
            'processedPayrolls',
            'recentAttendance', 
            'last7Days',
            'attendanceData', 
            'departmentLabels', 
            'departmentData'
        ));
    }

    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
        ]);
        
        $admin->update($request->only('name', 'email'));
        
        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        
        $admin = Auth::guard('admin')->user();
        
        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        $admin->password = Hash::make($request->new_password);
        $admin->save();
        
        return redirect()->back()->with('success', 'Password changed successfully');
    }
}