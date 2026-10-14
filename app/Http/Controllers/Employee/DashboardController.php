<?php
// app/Http/Controllers/Employee/DashboardController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\TimeSheet;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Display the employee dashboard summary.
     */
    public function index()
    {
        $employee = Auth::user();
        $today = Carbon::today();
        
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today->toDateString())
            ->first();
            
        $monthlyAttendance = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->count();
            
        $pendingLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();
            
        $approvedLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->count();
            
        $recentPayslips = Payroll::where('employee_id', $employee->id)
            ->where('status', 'paid')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(3)
            ->get();
            
        $recentTimeSheets = TimeSheet::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();
            
        $unreadNotifications = Notification::where('employee_id', $employee->id)
            ->where('is_read', false)
            ->count();
            
        $leaveBalance = $employee->currentLeaveBalance;

        return view('employee.dashboard', compact(
            'employee', 'todayAttendance', 'monthlyAttendance',
            'pendingLeaves', 'approvedLeaves', 'recentPayslips',
            'recentTimeSheets', 'unreadNotifications', 'leaveBalance'
        ));
    }

    /**
     * Profile view.
     */
    public function profile()
    {
        $employee = Auth::user();
        return view('employee.profile', compact('employee'));
    }

    /**
     * Profile update.
     */
    public function updateProfile(Request $request)
    {
        $employee = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $employee->profile_photo = $path;
        }

        $employee->update($request->only([
            'name', 'phone', 'address', 'emergency_contact_name', 'emergency_contact_phone'
        ]));

        return redirect()->route('employee.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Password update.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        $employee = Auth::user();

        if (!Hash::check($request->current_password, $employee->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $employee->password = Hash::make($request->new_password);
        $employee->save();

        return redirect()->route('employee.profile')
            ->with('success', 'Password changed successfully.');
    }
}