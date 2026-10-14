<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TimeSheetController;
use App\Http\Controllers\Admin\BirthdayController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AssetAllocationController;
use App\Http\Controllers\Admin\AssetMaintenanceController;
use App\Http\Controllers\Admin\AssetComponentController;
use App\Http\Controllers\Admin\AssetReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Employee\AuthController as EmployeeAuthController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\TimeSheetController as EmployeeTimeSheetController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use App\Http\Controllers\Employee\NotificationController as EmployeeNotificationController;

// =============================================
// PUBLIC LANDING PAGE ROUTES
// =============================================
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/features', [LandingPageController::class, 'features'])->name('features');
Route::get('/about', [LandingPageController::class, 'about'])->name('about');
Route::get('/contact', [LandingPageController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [LandingPageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-and-conditions', [LandingPageController::class, 'terms'])->name('terms');
Route::get('/delete-account', [LandingPageController::class, 'deleteAccount'])->name('delete-account');

// Public Website Lead Capture Routes
Route::post('/leads/capture', [\App\Http\Controllers\Api\WebsiteLeadCaptureController::class, 'capture'])->name('leads.capture');
Route::options('/leads/capture', [\App\Http\Controllers\Api\WebsiteLeadCaptureController::class, 'capture']);

// =============================================
// UNIFIED PORTAL LOGIN & AUTHENTICATION GATEWAY
// =============================================
Route::get('/login', [\App\Http\Controllers\UnifiedAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\UnifiedAuthController::class, 'login'])->name('login.store');
Route::post('/logout', [\App\Http\Controllers\UnifiedAuthController::class, 'logout'])->name('unified.logout');

// =============================================
// EMPLOYEE AUTHENTICATION ROUTES
// =============================================
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('login', [EmployeeAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [EmployeeAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [EmployeeAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        // Dashboard
        Route::get('dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
        
        // Attendance
        Route::get('attendance', [EmployeeAttendanceController::class, 'index'])->name('attendance');
        Route::get('attendance/monthly', [EmployeeAttendanceController::class, 'monthly'])->name('attendance.monthly');
        Route::post('attendance/check-in', [EmployeeAttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('attendance/check-out', [EmployeeAttendanceController::class, 'checkOut'])->name('attendance.check-out');
        
        // Leaves
        Route::get('leaves', [EmployeeLeaveController::class, 'index'])->name('leaves');
        Route::get('leaves/create', [EmployeeLeaveController::class, 'create'])->name('leaves.create');
        Route::post('leaves', [EmployeeLeaveController::class, 'store'])->name('leaves.store');
        Route::get('leaves/{leave}', [EmployeeLeaveController::class, 'show'])->name('leaves.show');
        Route::post('leaves/{leave}/cancel', [EmployeeLeaveController::class, 'cancel'])->name('leaves.cancel');
        Route::get('leaves/balance', [EmployeeLeaveController::class, 'balance'])->name('leaves.balance');
        
        // Payslips
        Route::get('payslips', [EmployeePayrollController::class, 'payslips'])->name('payslips');
        Route::get('payslips/{payroll}/download', [EmployeePayrollController::class, 'downloadPayslip'])->name('payslips.download');
        
        // Profile
        Route::get('profile', [EmployeeDashboardController::class, 'profile'])->name('profile');
        Route::post('profile', [EmployeeDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('change-password', [EmployeeDashboardController::class, 'changePassword'])->name('change-password');
        
        // Time Sheets
        Route::resource('time-sheets', EmployeeTimeSheetController::class);
        Route::get('time-sheets/weekly', [EmployeeTimeSheetController::class, 'weekly'])->name('time-sheets.weekly');
        Route::get('time-sheets/monthly', [EmployeeTimeSheetController::class, 'monthly'])->name('time-sheets.monthly');
        Route::post('time-sheets/export', [EmployeeTimeSheetController::class, 'export'])->name('time-sheets.export');
        
        // Notifications
        Route::get('notifications', [EmployeeNotificationController::class, 'index'])->name('notifications');
        Route::post('notifications/{notification}/read', [EmployeeNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [EmployeeNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('notifications/unread-count', [EmployeeNotificationController::class, 'unreadCount'])->name('notifications.unread-count');

        // Lead Project Assignments & Deliverables
        Route::get('assigned-leads', [\App\Http\Controllers\Employee\AssignedLeadController::class, 'index'])->name('assigned-leads.index');
        Route::post('assigned-leads/{leadWorker}/start', [\App\Http\Controllers\Employee\AssignedLeadController::class, 'start'])->name('assigned-leads.start');
        Route::post('assigned-leads/{leadWorker}/submit', [\App\Http\Controllers\Employee\AssignedLeadController::class, 'submit'])->name('assigned-leads.submit');
    });
});

// =============================================
// SALES TEAM CRM & LEAD MANAGEMENT ROUTES
// =============================================
Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Sales\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Sales\AuthController::class, 'login'])->name('login.submit');
    Route::get('register', [\App\Http\Controllers\Sales\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [\App\Http\Controllers\Sales\AuthController::class, 'register'])->name('register.submit');
    Route::post('logout', [\App\Http\Controllers\Sales\AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:web,admin')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Sales\DashboardController::class, 'index'])->name('dashboard');
        Route::get('reports', [\App\Http\Controllers\Sales\LeadController::class, 'reports'])->name('reports');
        Route::get('leads/{lead}/forward-tl', [\App\Http\Controllers\Sales\LeadController::class, 'forwardTlScreen'])->name('leads.forward-tl');
        Route::match(['post', 'patch'], 'leads/{lead}/assign-tl', [\App\Http\Controllers\Sales\LeadController::class, 'assignToTl'])->name('leads.assign-tl');
        Route::resource('leads', \App\Http\Controllers\Sales\LeadController::class);

        // Sales Profile & Password
        Route::get('profile', [\App\Http\Controllers\Sales\ProfileController::class, 'index'])->name('profile');
        Route::post('profile', [\App\Http\Controllers\Sales\ProfileController::class, 'update'])->name('profile.update');
        Route::post('change-password', [\App\Http\Controllers\Sales\ProfileController::class, 'changePassword'])->name('change-password');

        // Sales Help Desk / Support
        Route::get('support', [\App\Http\Controllers\Sales\SupportController::class, 'index'])->name('support');
        Route::post('support', [\App\Http\Controllers\Sales\SupportController::class, 'store'])->name('support.store');
        Route::get('support/{ticket}', [\App\Http\Controllers\Sales\SupportController::class, 'show'])->name('support.show');
    });
});

// =============================================
// TEAM LEADER (TL) PROJECT ASSIGNMENT ROUTES
// =============================================
Route::prefix('tl')->name('tl.')->group(function () {
    Route::get('login', function () {
        if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
            $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
            if ($user->is_team_leader || $user->role === 'team_leader' || $user->role === 'tl' || $user->is_admin) {
                return redirect()->route('tl.dashboard');
            }
            if ($user->role === 'sales') {
                return redirect()->route('sales.dashboard');
            }
            return redirect()->route('employee.dashboard');
        }
        return view('tl.auth.login');
    })->name('login');

    Route::post('login', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $emp = \App\Models\Employee::where('email', $request->email)->first();
        if ($emp && \Illuminate\Support\Facades\Hash::check($request->password, $emp->password)) {
            if (!$emp->is_active) {
                return back()->withErrors(['email' => 'Your account is deactivated. Please contact administrator.']);
            }
            if (!($emp->is_team_leader || $emp->role === 'team_leader' || $emp->role === 'tl' || $emp->is_admin)) {
                return back()->withErrors(['email' => 'Access denied. Only authorized Team Leaders can access this portal.'])->withInput($request->only('email'));
            }
            \Illuminate\Support\Facades\Auth::guard('web')->login($emp, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('tl.dashboard'));
        }
        return back()->withErrors(['email' => 'Invalid credentials provided.'])->withInput($request->only('email'));
    })->name('login.submit');

    Route::post('logout', function (\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tl.login')->with('success', 'Signed out from Team Leader Hub.');
    })->name('logout');

    Route::middleware('auth:web,admin')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\TeamLeader\DashboardController::class, 'index'])->name('dashboard');
        Route::get('reports', [\App\Http\Controllers\TeamLeader\LeadAssignmentController::class, 'reports'])->name('reports');
        Route::get('leads', [\App\Http\Controllers\TeamLeader\LeadAssignmentController::class, 'index'])->name('leads.index');
        Route::get('leads/{lead}', [\App\Http\Controllers\TeamLeader\LeadAssignmentController::class, 'show'])->name('leads.show');
        Route::post('leads/{lead}/assign-workers', [\App\Http\Controllers\TeamLeader\LeadAssignmentController::class, 'assignWorkers'])->name('leads.assign-workers');
        Route::patch('lead-workers/{workerAssignment}', [\App\Http\Controllers\TeamLeader\LeadAssignmentController::class, 'updateWorker'])->name('workers.update');
        Route::delete('lead-workers/{workerAssignment}', [\App\Http\Controllers\TeamLeader\LeadAssignmentController::class, 'destroyWorker'])->name('workers.destroy');
        Route::post('lead-workers/{leadWorker}/approve', [\App\Http\Controllers\TeamLeader\LeadAssignmentController::class, 'approveWorkerSubmission'])->name('leads.approve-worker');
        Route::post('lead-workers/{leadWorker}/approve-alt', [\App\Http\Controllers\TeamLeader\LeadAssignmentController::class, 'approveWorkerSubmission'])->name('workers.approve');
    });
});

// =============================================
// ADMIN AUTHENTICATION ROUTES
// =============================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/debug-attendance', function () {
    $att = \App\Models\Attendance::with('workShift', 'employee.workShift')->where('employee_id', 5)->whereDate('date', '2026-07-30')->first() ?? \App\Models\Attendance::latest('id')->first();
    $shift = $att->workShift ?? ($att->employee ? $att->employee->workShift : null);
    $shiftEndStr = $shift ? (\Carbon\Carbon::parse($att->date)->format('Y-m-d') . ' ' . $shift->end_time) : null;
    $shiftEnd = $shiftEndStr ? \Carbon\Carbon::parse($shiftEndStr) : null;
    return [
        'check_in' => $att->check_in,
        'check_out' => $att->check_out,
        'shift_end_time' => $shift ? $shift->end_time : null,
        'shift_end_str' => $shiftEndStr,
        'shift_end' => $shiftEnd,
        'is_gt' => $shiftEnd && $att->check_out ? $att->check_out->gt($shiftEnd) : null,
        'overtime_seconds' => $att->overtime_seconds,
        'total_break' => $att->total_break_minutes
    ];
});

Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    // Temporary Dev Route to Reload Data
    Route::get('/dev/reload-data', function () {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true]);
        return "Database wiped and re-seeded successfully! <a href='/'>Go to Login</a>";
    });

    // Temporary Dev Route to Import SQL
    Route::get('/dev/import-sql', function () {
        $sqlPath = base_path('crorebah_employee.sql');
        if (!file_exists($sqlPath)) {
            return "File not found: " . $sqlPath;
        }
        
        $sql = file_get_contents($sqlPath);
        \Illuminate\Support\Facades\DB::unprepared($sql);
        
        return "SQL file (crorebah_employee.sql) imported successfully! <a href='/'>Go to Login</a>";
    });

    // Temporary Dev Route to Fix SQL Dump (Strip CREATE/ALTER TABLE)
    Route::get('/dev/fix-sql', function () {
        $sqlPath = base_path('crorebah_employee.sql');
        if (!file_exists($sqlPath)) return "File not found.";

        $lines = file($sqlPath);
        $newLines = [];
        $insideInsert = false;
        $insertStatement = "";

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), 'INSERT INTO')) {
                $newLines[] = "SET FOREIGN_KEY_CHECKS=0;\n";
                $newLines[] = $line;
            } elseif (str_starts_with(trim($line), '(') && str_ends_with(trim($line), '),')) {
                // Continuation of a multi-line INSERT INTO, but phpMyAdmin usually does single long lines or multiple lines.
                // Actually, phpMyAdmin usually puts INSERT INTO on one long line. Let's just grab lines starting with INSERT INTO.
            }
        }

        // A safer way: regex to extract all INSERT INTO statements.
        $content = file_get_contents($sqlPath);
        preg_match_all('/INSERT INTO.*?;/s', $content, $matches);
        
        $newContent = "SET FOREIGN_KEY_CHECKS=0;\n" . implode("\n", $matches[0]) . "\nSET FOREIGN_KEY_CHECKS=1;\n";
        
        file_put_contents(base_path('crorebah_employee_data.sql'), $newContent);
        
        return "SQL file fixed and saved as crorebah_employee_data.sql! Please update DatabaseSeeder to use this new file.";
    });
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('admin/api/weekly-attendance', [AuthController::class, 'getWeeklyAttendance'])->name('admin.api.weekly-attendance');
    
    Route::middleware('auth:admin')->group(function () {

        // ── Dashboard & Profile ──────────────────────────────────────────
        Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
        Route::get('profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/change-password', [AuthController::class, 'changePassword'])->name('profile.change-password');

        // ── Website Lead Management (CRM) ─────────────────────────────────
        Route::get('leads/dashboard', [\App\Http\Controllers\Admin\LeadController::class, 'dashboard'])->name('leads.dashboard');
        Route::get('leads/export', [\App\Http\Controllers\Admin\LeadController::class, 'export'])->name('leads.export');
        Route::post('leads/auto-assign', [\App\Http\Controllers\Admin\LeadController::class, 'autoAssign'])->name('leads.auto-assign');
        Route::get('leads/followups', [\App\Http\Controllers\Admin\LeadFollowupController::class, 'index'])->name('leads.followups');
        Route::get('leads/analytics', [\App\Http\Controllers\Admin\LeadAnalyticsController::class, 'index'])->name('leads.analytics');
        Route::get('leads/settings', [\App\Http\Controllers\Admin\LeadSettingController::class, 'index'])->name('leads.settings');
        Route::post('leads/settings', [\App\Http\Controllers\Admin\LeadSettingController::class, 'update'])->name('leads.settings.update');
        Route::post('leads/templates', [\App\Http\Controllers\Admin\LeadSettingController::class, 'storeTemplate'])->name('leads.templates.store');
        Route::delete('leads/templates/{template}', [\App\Http\Controllers\Admin\LeadSettingController::class, 'deleteTemplate'])->name('leads.templates.destroy');

        Route::get('leads/reports', [\App\Http\Controllers\Admin\LeadController::class, 'reports'])->name('leads.reports');
        Route::patch('leads/{lead}/status', [\App\Http\Controllers\Admin\LeadController::class, 'updateStatus'])->name('leads.status');
        Route::patch('leads/{lead}/assign', [\App\Http\Controllers\Admin\LeadController::class, 'assignStaff'])->name('leads.assign');
        Route::get('leads/{lead}/forward-tl', [\App\Http\Controllers\Admin\LeadController::class, 'forwardTlScreen'])->name('leads.forward-tl');
        Route::match(['post', 'patch'], 'leads/{lead}/assign-tl', [\App\Http\Controllers\Admin\LeadController::class, 'assignTl'])->name('leads.assign-tl');
        Route::post('leads/{lead}/assign-workers', [\App\Http\Controllers\Admin\LeadController::class, 'assignWorkers'])->name('leads.assign-workers');
        Route::patch('lead-workers/{workerAssignment}', [\App\Http\Controllers\Admin\LeadController::class, 'updateWorkerAssignment'])->name('workers.update');
        Route::delete('lead-workers/{workerAssignment}', [\App\Http\Controllers\Admin\LeadController::class, 'destroyWorkerAssignment'])->name('workers.destroy');
        Route::post('leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convert'])->name('leads.convert');
        Route::post('leads/{lead}/followups', [\App\Http\Controllers\Admin\LeadController::class, 'addFollowup'])->name('leads.followups.add');
        Route::post('leads/followups/{followup}/complete', [\App\Http\Controllers\Admin\LeadController::class, 'completeFollowup'])->name('leads.followups.complete');

        Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class);

        // ── Clients & Projects ───────────────────────────────────────────
        Route::resource('clients', ClientController::class);
        Route::resource('projects', ProjectController::class);

        // ── Employees ────────────────────────────────────────────────────
        Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
        Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::patch('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
        Route::patch('employees/{employee}/toggle-admin', [EmployeeController::class, 'toggleAdminRole'])->name('employees.toggle-admin');
        Route::patch('employees/{employee}/toggle-tl', [EmployeeController::class, 'toggleTlRole'])->name('employees.toggle-tl');
        Route::post('employees/{employee}/pf-settings', [EmployeeController::class, 'updatePfSettings'])->name('employees.pf-settings');
        Route::post('employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
        Route::post('employees/{employee}/update-birthday', [BirthdayController::class, 'updateBirthday'])->name('employees.update-birthday');
        Route::get('employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('employees.attendance');
        Route::get('employees/{employee}/payroll', [EmployeeController::class, 'payroll'])->name('employees.payroll');
        Route::get('employees/{employee}/leaves', [EmployeeController::class, 'leaves'])->name('employees.leaves');
        Route::post('employees/{employee}/documents/upload', [EmployeeController::class, 'uploadDocument'])->name('employees.documents.upload');
        Route::post('documents/{document}/verify', [EmployeeController::class, 'verifyDocument'])->name('documents.verify');
        Route::post('documents/{document}/reject', [EmployeeController::class, 'rejectDocument'])->name('documents.reject');
        Route::post('employees/{employee}/lifecycle-status', [EmployeeController::class, 'updateLifecycleStatus'])->name('employees.lifecycle-status');
        Route::post('employees/{employee}/update-manager', [EmployeeController::class, 'updateManager'])->name('employees.update-manager');
        Route::post('employees/{employee}/update-shift', [EmployeeController::class, 'updateShift'])->name('employees.update-shift');
        Route::post('work-shifts/store', [EmployeeController::class, 'storeShift'])->name('work-shifts.store');
        Route::post('shifts/permissions/{permission}/approve', [App\Http\Controllers\Admin\WorkShiftController::class, 'approvePermission'])->name('shifts.permissions.approve');
        Route::post('shifts/permissions/{permission}/reject', [App\Http\Controllers\Admin\WorkShiftController::class, 'rejectPermission'])->name('shifts.permissions.reject');
        Route::resource('shifts', App\Http\Controllers\Admin\WorkShiftController::class);
        Route::post('employees/{employee}/experiences', [EmployeeController::class, 'addExperience'])->name('employees.experiences.add');
        Route::delete('experiences/{id}', [EmployeeController::class, 'deleteExperience'])->name('employees.experiences.delete');
        Route::post('employees/{employee}/educations', [EmployeeController::class, 'addEducation'])->name('employees.educations.add');
        Route::resource('employees', EmployeeController::class);

        // ── Sales Team Management ────────────────────────────────────────
        Route::patch('sales-team/{sales_team}/toggle-status', [\App\Http\Controllers\Admin\SalesTeamController::class, 'toggleStatus'])->name('sales-team.toggle-status');
        Route::get('sales-team/{sales_team}/leads', [\App\Http\Controllers\Admin\SalesTeamController::class, 'leads'])->name('sales-team.leads');
        Route::resource('sales-team', \App\Http\Controllers\Admin\SalesTeamController::class);

        // ── Account Deletions ──────────────────────────────────────────
        Route::get('account-deletions', [App\Http\Controllers\Admin\AccountDeletionRequestController::class, 'index'])->name('account-deletions.index');
        Route::post('account-deletions/{deletionRequest}/approve', [App\Http\Controllers\Admin\AccountDeletionRequestController::class, 'approve'])->name('account-deletions.approve');
        Route::post('account-deletions/{deletionRequest}/reject', [App\Http\Controllers\Admin\AccountDeletionRequestController::class, 'reject'])->name('account-deletions.reject');

        // ── Organization Hierarchy ──────────────────────────────────────
        Route::get('organization/hierarchy', [App\Http\Controllers\Admin\OrganizationController::class, 'hierarchy'])->name('organization.hierarchy');

        // ── Attendance ───────────────────────────────────────────────────
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark');
        Route::post('attendance/bulk-mark', [AttendanceController::class, 'bulkMark'])->name('attendance.bulk-mark');
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
        Route::get('attendance/summary', [AttendanceController::class, 'summary'])->name('attendance.summary');
        Route::get('attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

        // ── Leaves ───────────────────────────────────────────────────────
        Route::get('leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
        Route::post('leaves/store', [LeaveController::class, 'store'])->name('leaves.store');
        Route::get('leaves/report', [LeaveController::class, 'report'])->name('leaves.report');
        Route::get('leaves/export', [LeaveController::class, 'export'])->name('leaves.export');
        Route::get('leaves/balance', [LeaveController::class, 'balance'])->name('leaves.balance');
        Route::post('leaves/balance/update', [LeaveController::class, 'updateBalance'])->name('leaves.balance.update');
        Route::post('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
        Route::post('leaves/{leave}/cancel', [LeaveController::class, 'cancel'])->name('leaves.cancel');
        Route::resource('leaves', LeaveController::class)->parameters(['leaves' => 'leave']);

        // ── Notifications (Admin) ───────────────────────────────────────
        Route::get('notifications', [App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/mark-read', [App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', [App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::post('notifications/clear-all', [App\Http\Controllers\Admin\AdminNotificationController::class, 'clearAll'])->name('notifications.clear-all');

        // ── Payroll ──────────────────────────────────────────────────────
        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::post('payroll/process', [PayrollController::class, 'process'])->name('payroll.process');
        Route::post('payroll/bulk-generate', [PayrollController::class, 'bulkGeneratePayslips'])->name('payroll.bulk-generate');
        Route::get('payroll/{payroll}/generate-payslip', [PayrollController::class, 'generatePayslip'])->name('payroll.generate-payslip');
        Route::get('payroll/payslip/{payslip}/download', [PayrollController::class, 'downloadPayslip'])->name('payroll.download-payslip');
        Route::post('payroll/payslip/{payslip}/send-email', [PayrollController::class, 'sendPayslipEmail'])->name('payroll.send-payslip-email');
        Route::post('payroll/{payroll}/mark-paid', [PayrollController::class, 'markAsPaid'])->name('payroll.mark-paid');
        Route::put('payroll/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
        Route::delete('payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
        Route::get('payroll/export', [PayrollController::class, 'export'])->name('payroll.export');
        Route::post('payroll/employee/{employee}/calculate', [PayrollController::class, 'calculateSingle'])->name('payroll.calculate-single');
        Route::post('payroll/clear-all', [PayrollController::class, 'clearAll'])->name('payroll.clear-all');
        Route::get('payroll/summary', [PayrollController::class, 'summary'])->name('payroll.summary');

        // ── Time Sheets (Admin) - FIXED ROUTE ORDER ──────────────────────────
        Route::prefix('time-sheets')->name('time-sheets.')->group(function () {
            // SPECIFIC ROUTES FIRST (no wildcards)
            Route::get('/report', [TimeSheetController::class, 'report'])->name('report');
            Route::get('/summary', [TimeSheetController::class, 'summary'])->name('summary');
            Route::get('/export', [TimeSheetController::class, 'export'])->name('export');
            Route::get('/create', [TimeSheetController::class, 'create'])->name('create');
            Route::post('/store', [TimeSheetController::class, 'store'])->name('store');
            Route::post('/bulk-approve', [TimeSheetController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/export', [TimeSheetController::class, 'export.post'])->name('export.post');
            
            // WILDCARD ROUTES LAST
            Route::get('/', [TimeSheetController::class, 'index'])->name('index');
            Route::get('/{timeSheet}', [TimeSheetController::class, 'show'])->name('show');
            Route::post('/{timeSheet}/approve', [TimeSheetController::class, 'approve'])->name('approve');
            Route::post('/{timeSheet}/reject', [TimeSheetController::class, 'reject'])->name('reject');
            Route::delete('/{timeSheet}', [TimeSheetController::class, 'destroy'])->name('destroy');
        });

        // ── Birthday & Anniversary ───────────────────────────────────────
        Route::get('birthdays', [BirthdayController::class, 'index'])->name('birthdays.index');
        Route::post('birthdays/send-wish', [BirthdayController::class, 'sendWish'])->name('birthdays.send-wish');
        Route::post('birthdays/bulk-send', [BirthdayController::class, 'bulkSendWishes'])->name('birthdays.bulk-send');
        Route::get('birthdays/upcoming', [BirthdayController::class, 'getUpcoming'])->name('birthdays.upcoming');
        Route::get('birthdays/calendar', [BirthdayController::class, 'calendar'])->name('birthdays.calendar');
        Route::post('birthdays/reminder', [BirthdayController::class, 'sendReminder'])->name('birthdays.reminder');
        Route::get('birthdays/export', [BirthdayController::class, 'export'])->name('birthdays.export');

        // ── Reports ──────────────────────────────────────────────────────
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::post('reports/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');
        Route::post('reports/asset', [ReportController::class, 'asset'])->name('reports.asset');
        Route::post('reports/attendance/export', [ReportController::class, 'exportAttendance'])->name('reports.attendance.export');
        Route::post('reports/payroll/export', [ReportController::class, 'exportPayroll'])->name('reports.payroll.export');
        Route::post('reports/asset/export', [ReportController::class, 'exportAsset'])->name('reports.asset.export');
        Route::post('reports/time-sheets/export', [ReportController::class, 'exportTimeSheets'])->name('reports.time-sheets.export');
        Route::post('reports/leave/export', [ReportController::class, 'exportLeaves'])->name('reports.leaves.export');
        Route::post('reports/birthday/export', [ReportController::class, 'exportBirthdays'])->name('reports.birthdays.export');
        Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');

        // ── Support Tickets ─────────────────────────────────────────────
        Route::get('support', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('support.index');
        Route::get('support/{ticket}', [App\Http\Controllers\Admin\SupportController::class, 'show'])->name('support.show');
        Route::put('support/{ticket}', [App\Http\Controllers\Admin\SupportController::class, 'update'])->name('support.update');

        // ── Activity Log ───────────────────────────────────────────────────
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

        // ── Admin Notifications ───────────────────────────────────────────
        Route::get('admin-notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('admin-notifications/{notification}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('admin-notifications/read-all', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::post('admin-notifications/clear-all', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'clearAll'])->name('notifications.clear-all');
        
        // ── Settings ─────────────────────────────────────────────────────
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general');
        Route::post('settings/mail', [SettingController::class, 'updateMail'])->name('settings.mail');
        Route::post('settings/backup', [SettingController::class, 'backupDatabase'])->name('settings.backup');
        Route::post('settings/restore', [SettingController::class, 'restoreDatabase'])->name('settings.restore');
        
        // ── Departments ───────────────────────────────────────────────────
        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::get('departments/{department}/employees', [DepartmentController::class, 'employees'])->name('departments.employees');
        
        // ── Holidays ─────────────────────────────────────────────────────
        Route::get('holidays', [HolidayController::class, 'index'])->name('holidays.index');
        Route::post('holidays', [HolidayController::class, 'store'])->name('holidays.store');
        Route::put('holidays/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
        Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
        Route::get('holidays/calendar', [HolidayController::class, 'calendar'])->name('holidays.calendar');
        
        // ── Assets ───────────────────────────────────────────────────────
        Route::get('assets/dashboard', [AssetController::class, 'dashboard'])->name('assets.dashboard');
        Route::get('assets/reports', [AssetReportController::class, 'index'])->name('assets.reports.index');
        Route::post('assets/reports/export', [AssetReportController::class, 'export'])->name('assets.reports.export');
        Route::get('assets/reports/invoice', [AssetReportController::class, 'invoice'])->name('assets.reports.invoice');
        Route::get('assets/reports/maintenance', [AssetReportController::class, 'maintenanceInvoice'])->name('assets.reports.maintenance.invoice');
        Route::get('assets/reports/components', [AssetReportController::class, 'componentsInvoice'])->name('assets.reports.components.invoice');
        Route::resource('assets', AssetController::class);
        
        // ── Asset Allocations ─────────────────────────────────────────────
        Route::resource('allocations', AssetAllocationController::class)->names('assets.allocations');
        Route::get('allocations/{allocation}/handover', [AssetAllocationController::class, 'handover'])->name('assets.allocations.handover');
        Route::post('allocations/{allocation}/return', [AssetAllocationController::class, 'returnAsset'])->name('assets.allocations.return');
        
        // ── Asset Maintenance ─────────────────────────────────────────────
        Route::resource('maintenance', AssetMaintenanceController::class)->names('assets.maintenance');
        Route::post('maintenance/{maintenance}/receive', [AssetMaintenanceController::class, 'receive'])->name('assets.maintenance.receive');
        Route::post('maintenance/{maintenance}/complete', [AssetMaintenanceController::class, 'complete'])->name('assets.maintenance.complete');
        Route::post('maintenance/{maintenance}/cancel', [AssetMaintenanceController::class, 'cancel'])->name('assets.maintenance.cancel');
        
        // ── Asset Components ──────────────────────────────────────────────
        Route::post('assets/components', [AssetComponentController::class, 'store'])->name('assets.components.store');
        
        // ── System Management ────────────────────────────────────────────
        Route::get('system/info', [SystemController::class, 'info'])->name('system.info');
        Route::get('system/logs', [SystemController::class, 'logs'])->name('system.logs');
        Route::delete('system/logs/clear', [SystemController::class, 'clearLogs'])->name('system.logs.clear');
        Route::post('system/cache/clear', [SystemController::class, 'clearCache'])->name('system.cache.clear');
        Route::post('system/optimize', [SystemController::class, 'optimize'])->name('system.optimize');
        Route::get('system/phpinfo', [SystemController::class, 'phpInfo'])->name('system.phpinfo');
        
        // ── API Routes for AJAX ──────────────────────────────────────────
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('employees/search', [EmployeeController::class, 'search'])->name('employees.search');
            Route::get('attendance/summary', [AttendanceController::class, 'getSummary'])->name('attendance.summary');
            Route::get('payroll/summary', [PayrollController::class, 'getSummary'])->name('payroll.summary');
            Route::get('time-sheets/summary', [TimeSheetController::class, 'getSummary'])->name('time-sheets.summary');
            Route::get('leaves/summary', [LeaveController::class, 'getSummary'])->name('leaves.summary');
            Route::get('birthdays/upcoming', [BirthdayController::class, 'getUpcoming'])->name('birthdays.upcoming');
            Route::get('dashboard/stats', [AuthController::class, 'getStats'])->name('dashboard.stats');
            Route::get('notifications', [AuthController::class, 'getNotifications'])->name('notifications');
        });
        
        // ── Export Routes ─────────────────────────────────────────────────
        Route::prefix('exports')->name('exports.')->group(function () {
            Route::get('employees', [EmployeeController::class, 'export'])->name('employees');
            Route::get('attendance', [AttendanceController::class, 'export'])->name('attendance');
            Route::get('payroll', [PayrollController::class, 'export'])->name('payroll');
            Route::get('time-sheets', [TimeSheetController::class, 'export'])->name('time-sheets');
            Route::get('leaves', [LeaveController::class, 'export'])->name('leaves');
            Route::get('birthdays', [BirthdayController::class, 'export'])->name('birthdays');
        });
        
        // ── Bulk Operations ──────────────────────────────────────────────
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::post('employees/activate', [EmployeeController::class, 'bulkActivate'])->name('employees.activate');
            Route::post('employees/deactivate', [EmployeeController::class, 'bulkDeactivate'])->name('employees.deactivate');
            Route::post('employees/delete', [EmployeeController::class, 'bulkDelete'])->name('employees.delete');
            Route::post('attendance/mark', [AttendanceController::class, 'bulkMark'])->name('attendance.mark');
            Route::post('time-sheets/approve', [TimeSheetController::class, 'bulkApprove'])->name('time-sheets.approve');
            Route::post('leaves/approve', [LeaveController::class, 'bulkApprove'])->name('leaves.approve');
        });
    });
});

// =============================================
// ERROR HANDLING ROUTES
// =============================================
Route::fallback(function () {
    return view('errors.404');
});

Route::get('/maintenance', function () {
    return view('errors.maintenance');
})->name('maintenance');

Route::get('/unauthorized', function () {
    return view('errors.403');
})->name('unauthorized');

// =============================================
// HEALTH CHECK ROUTE (for monitoring)
// =============================================
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'environment' => app()->environment(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
});