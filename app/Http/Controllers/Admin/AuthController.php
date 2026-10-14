<?php
// app/Http/Controllers/Admin/AuthController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\TimeSheet;
use App\Models\Asset;
use App\Models\AssetAllocation;
use App\Models\AssetMaintenance;
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

            // Log the login event
            AuditLog::log(
                'login',
                "Admin '{$admin->name}' logged in from IP {$request->ip()}",
                $admin
            );

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput($request->only('email'));
    }

    public function logout()
    {
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            AuditLog::log(
                'logout',
                "Admin '{$admin->name}' signed out.",
                $admin
            );
        }
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    /**
     * Display the admin dashboard with consolidated metrics.
     */
    public function dashboard()
    {
        $employeeStats = $this->getEmployeeStats();
        $attendanceStats = $this->getAttendanceStats($employeeStats['totalEmployees']);
        $leaveStats = $this->getLeaveStats();
        $operationalStats = $this->getOperationalStats();
        $listStats = $this->getListStats($employeeStats['totalEmployees']);

        return view('admin.dashboard', array_merge(
            $employeeStats,
            $attendanceStats,
            $leaveStats,
            $operationalStats,
            $listStats
        ));
    }

    /**
     * Get basic employee statistics.
     */
    private function getEmployeeStats(): array
    {
        return [
            'totalEmployees' => Employee::count(),
            'activeEmployees' => Employee::where('is_active', true)->count(),
            'inactiveEmployees' => Employee::where('is_active', false)->count(),
            'newEmployeesMonth' => Employee::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Get today's attendance and weekly trends.
     */
    private function getAttendanceStats(int $totalEmployees): array
    {
        $todayStr = now()->toDateString();
        
        $presentToday = Attendance::where('date', $todayStr)->where('status', 'present')->count();
        $lateToday = Attendance::where('date', $todayStr)->where('status', 'late')->count();
        $absentToday = Attendance::where('date', $todayStr)->where('status', 'absent')->count();
        $totalPresentToday = $presentToday + $lateToday;
        
        $attendanceRate = $totalEmployees > 0 ? round(($totalPresentToday / $totalEmployees) * 100) : 0;
        
        $weeklyAttendance = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayAttendance = Attendance::where('date', $date->toDateString())->get();
            $weeklyAttendance[] = [
                'date' => $date->format('d M'),
                'present' => $dayAttendance->where('status', 'present')->count(),
                'absent' => $dayAttendance->where('status', 'absent')->count(),
                'late' => $dayAttendance->where('status', 'late')->count(),
            ];
        }

        return compact('presentToday', 'lateToday', 'absentToday', 'attendanceRate', 'weeklyAttendance');
    }

    /**
     * Get leave requests and distribution data.
     */
    private function getLeaveStats(): array
    {
        $leaveTypes = Leave::select('leave_type', DB::raw('count(*) as total'))
            ->whereYear('created_at', now()->year)
            ->groupBy('leave_type')
            ->pluck('total', 'leave_type')
            ->toArray();

        return [
            'pendingLeaves' => Leave::where('status', 'pending')->count(),
            'approvedLeavesMonth' => Leave::where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'rejectedLeavesMonth' => Leave::where('status', 'rejected')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'onLeaveToday' => Leave::where('status', 'approved')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->count(),
            'leaveTypes' => $leaveTypes ?: [],
        ];
    }

    /**
     * Get financial, timesheet, and asset metrics.
     */
    private function getOperationalStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $totalPayrollAmount = 0; $processedPayrolls = 0; $pendingPayrolls = 0;
        
        try {
            $totalPayrollAmount = Payroll::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('net_salary');
            
            $processedPayrolls = Payroll::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('status', 'paid')
                ->count();
            
            $pendingPayrolls = Payroll::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('status', 'pending')
                ->count();
        } catch (\Exception $e) { /* Silently fail if table missing */ }

        return [
            'pendingTimeSheets' => TimeSheet::where('status', 'pending')->count(),
            'totalHoursMonth' => TimeSheet::where('status', 'approved')
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->sum('total_hours'),
            'totalPayrollAmount' => $totalPayrollAmount,
            'processedPayrolls' => $processedPayrolls,
            'pendingPayrolls' => $pendingPayrolls,
            'totalAssets' => Asset::count(),
            'allocatedAssets' => Asset::where('status', 'allocated')->count(),
            'pendingMaintenance' => AssetMaintenance::where('status', 'pending')->count(),
        ];
    }

    /**
     * Get list-based data like birthdays and recent entries.
     */
    private function getListStats(int $totalEmployees): array
    {
        $departments = Employee::select('department', DB::raw('count(*) as total'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->pluck('total', 'department')
            ->toArray();

        $upcomingBirthdays = Employee::whereNotNull('date_of_birth')
            ->get()
            ->map(function ($employee) {
                $dob = \Carbon\Carbon::parse($employee->date_of_birth);
                $nextBirthday = \Carbon\Carbon::create(now()->year, $dob->month, $dob->day);
                if ($nextBirthday->isPast()) $nextBirthday->addYear();
                $employee->next_birthday = $nextBirthday;
                return $employee;
            })
            ->filter(function ($employee) {
                $daysUntil = now()->diffInDays($employee->next_birthday, false);
                return $daysUntil >= 0 && $daysUntil <= 30;
            })
            ->sortBy(fn($e) => $e->next_birthday->format('m-d'))
            ->take(10);

        return [
            'recentLeaves' => Leave::with('employee')->orderBy('created_at', 'desc')->take(10)->get(),
            'departments' => $departments ?: ['No Data' => $totalEmployees],
            'upcomingBirthdays' => $upcomingBirthdays,
            'recentEmployees' => Employee::orderBy('created_at', 'desc')->take(10)->get(),
        ];
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

        $old = $admin->only('name', 'email');
        $admin->update($request->only('name', 'email'));

        // Log profile update with field changes
        AuditLog::log(
            'profile_updated',
            "Admin '{$admin->name}' updated their profile.",
            $admin,
            [
                'name'  => ['old' => $old['name'],  'new' => $admin->name],
                'email' => ['old' => $old['email'], 'new' => $admin->email],
            ]
        );
        
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

        // Log password change
        AuditLog::log(
            'password_changed',
            "Admin '{$admin->name}' changed their password.",
            $admin
        );
        
        return redirect()->back()->with('success', 'Password changed successfully');
    }
}