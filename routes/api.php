<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', 'App\Http\Controllers\Api\AuthController@login');
Route::post('/admin/login', [\App\Http\Controllers\Api\Admin\AdminAuthApiController::class, 'login']);
Route::post('/sales/login', [\App\Http\Controllers\Api\SalesLeadApiController::class, 'login']);
Route::post('/password/send-otp', 'App\Http\Controllers\Api\AuthController@sendPasswordOtp');
Route::post('/password/reset-with-otp', 'App\Http\Controllers\Api\AuthController@resetPasswordWithOtp');
Route::post('/v1/leads/capture', [\App\Http\Controllers\Api\WebsiteLeadCaptureController::class, 'capture']);
Route::options('/v1/leads/capture', [\App\Http\Controllers\Api\WebsiteLeadCaptureController::class, 'capture']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Current User & Profile
    Route::get('/user', 'App\Http\Controllers\Api\AuthController@user');
    Route::post('/profile/update', 'App\Http\Controllers\Api\AuthController@updateProfile');
    Route::post('/profile/photo', 'App\Http\Controllers\Api\AuthController@updateProfilePhoto');
    Route::post('/profile/delete-account-request', 'App\Http\Controllers\Api\ProfileDetailController@requestAccountDeletion');
    Route::post('/logout', 'App\Http\Controllers\Api\AuthController@logout');
    Route::post('/push-token', 'App\Http\Controllers\Api\AuthController@updatePushToken');
    Route::post('/test-push', 'App\Http\Controllers\Api\AuthController@testPush');

    // Dashboard
    Route::get('/dashboard', 'App\Http\Controllers\Api\DashboardController@index');

    // Projects
    Route::get('/projects', 'App\Http\Controllers\Api\ProjectController@index');

    // Attendance
    Route::prefix('attendance')->group(function () {
        Route::get('/status', 'App\Http\Controllers\Api\AttendanceController@status');
        Route::post('/clock-in', 'App\Http\Controllers\Api\AttendanceController@clockIn');
        Route::post('/clock-out', 'App\Http\Controllers\Api\AttendanceController@clockOut');
        Route::post('/break-start', 'App\Http\Controllers\Api\AttendanceController@breakStart');
        Route::post('/break-end', 'App\Http\Controllers\Api\AttendanceController@breakEnd');
        Route::post('/lunch-start', 'App\Http\Controllers\Api\AttendanceController@lunchStart');
        Route::post('/lunch-end', 'App\Http\Controllers\Api\AttendanceController@lunchEnd');
        Route::get('/history', 'App\Http\Controllers\Api\AttendanceController@history');
    });

    // Leaves
    Route::prefix('leaves')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\LeaveController@index');
        Route::get('/balances', 'App\Http\Controllers\Api\LeaveController@balances');
        Route::post('/apply', 'App\Http\Controllers\Api\LeaveController@store');
        Route::post('/{id}/cancel', 'App\Http\Controllers\Api\LeaveController@cancel');
    });
    
    // Permissions
    Route::prefix('permissions')->group(function () {
        Route::get('/history', 'App\Http\Controllers\Api\PermissionController@history');
        Route::post('/request', 'App\Http\Controllers\Api\PermissionController@request');
    });

    // Payroll
    Route::get('/payroll/payslips', 'App\Http\Controllers\Api\PayrollController@index');
    Route::get('/payroll/payslips/{id}', 'App\Http\Controllers\Api\PayrollController@show');
    Route::get('/payroll/summary', 'App\Http\Controllers\Api\PayrollController@summary');
    Route::post('/payroll/generate', 'App\Http\Controllers\Api\PayrollController@generate');
    Route::post('/payroll/calculate', 'App\Http\Controllers\Api\PayrollController@calculate');

    // Assets
    Route::get('/assets/my-assets', 'App\Http\Controllers\Api\AssetController@myAssets');
    Route::post('/assets/report-issue', 'App\Http\Controllers\Api\AssetController@reportIssue');

    // TimeSheets
    Route::get('/timesheets', 'App\Http\Controllers\Api\TimeSheetController@index');
    Route::post('/timesheets', 'App\Http\Controllers\Api\TimeSheetController@store');

    // Documents
    Route::prefix('documents')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\DocumentController@index');
        Route::post('/upload', 'App\Http\Controllers\Api\DocumentController@store');
        Route::delete('/{id}', 'App\Http\Controllers\Api\DocumentController@destroy');
    });

    // Multi-Experiences
    Route::prefix('experiences')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\ProfileDetailController@indexExperiences');
        Route::post('/', 'App\Http\Controllers\Api\ProfileDetailController@storeExperience');
        Route::delete('/{id}', 'App\Http\Controllers\Api\ProfileDetailController@destroyExperience');
    });

    // Multi-Educations
    Route::prefix('educations')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\ProfileDetailController@indexEducations');
        Route::post('/', 'App\Http\Controllers\Api\ProfileDetailController@storeEducation');
        Route::delete('/{id}', 'App\Http\Controllers\Api\ProfileDetailController@destroyEducation');
    });

    // Organization & Hierarchy
    Route::get('/organization/hierarchy', 'App\Http\Controllers\Api\OrganizationController@hierarchy');

    // Events
    Route::get('/events/holidays', 'App\Http\Controllers\Api\EventController@holidays');
    Route::get('/events/birthdays', 'App\Http\Controllers\Api\EventController@birthdays');

    // Employees (Directory)
    Route::get('/employees', 'App\Http\Controllers\Api\EmployeeController@index');
    Route::post('/employees', 'App\Http\Controllers\Api\EmployeeController@store');
    Route::get('/employees/{id}', 'App\Http\Controllers\Api\EmployeeController@show');
    Route::put('/employees/{id}', 'App\Http\Controllers\Api\EmployeeController@update');
    Route::delete('/employees/{id}', 'App\Http\Controllers\Api\EmployeeController@destroy');

    // Work Shifts API
    Route::prefix('work-shifts')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\WorkShiftController@index');
        Route::post('/', 'App\Http\Controllers\Api\WorkShiftController@store');
        Route::get('/{id}', 'App\Http\Controllers\Api\WorkShiftController@show');
        Route::put('/{id}', 'App\Http\Controllers\Api\WorkShiftController@update');
        Route::delete('/{id}', 'App\Http\Controllers\Api\WorkShiftController@destroy');
    });

    // Notifications API
    Route::prefix('notifications')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\NotificationController@index');
        Route::post('/{id}/read', 'App\Http\Controllers\Api\NotificationController@markAsRead');
        Route::post('/read-all', 'App\Http\Controllers\Api\NotificationController@markAllAsRead');
        Route::post('/save-push-token', 'App\Http\Controllers\Api\NotificationController@savePushToken');
    });

    // Support Tickets
    Route::prefix('support')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\SupportTicketController@index');
        Route::post('/', 'App\Http\Controllers\Api\SupportTicketController@store');
    });

    // ══════════════════════════════════════════════
    // SALES TEAM MOBILE APP API
    // ══════════════════════════════════════════════
    Route::prefix('sales')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\SalesLeadApiController::class, 'dashboard']);
        Route::get('/leads', [\App\Http\Controllers\Api\SalesLeadApiController::class, 'index']);
        Route::post('/leads', [\App\Http\Controllers\Api\SalesLeadApiController::class, 'store']);
        Route::get('/leads/{id}', [\App\Http\Controllers\Api\SalesLeadApiController::class, 'show']);
        Route::put('/leads/{id}', [\App\Http\Controllers\Api\SalesLeadApiController::class, 'update']);
        Route::post('/leads/{id}/assign-tl', [\App\Http\Controllers\Api\SalesLeadApiController::class, 'assignToTl']);
        Route::get('/reports', [\App\Http\Controllers\Api\SalesLeadApiController::class, 'reports']);
    });

    // ══════════════════════════════════════════════
    // TEAM LEADER (TL) PROJECT ASSIGNMENT API
    // ══════════════════════════════════════════════
    Route::prefix('tl')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\TeamLeaderApiController::class, 'dashboard']);
        Route::get('/leads', [\App\Http\Controllers\Api\TeamLeaderApiController::class, 'leads']);
        Route::get('/leads/{id}', [\App\Http\Controllers\Api\TeamLeaderApiController::class, 'leadDetails']);
        Route::post('/leads/{id}/assign-workers', [\App\Http\Controllers\Api\TeamLeaderApiController::class, 'assignWorkers']);
        Route::post('/lead-workers/{id}/approve', [\App\Http\Controllers\Api\TeamLeaderApiController::class, 'approveWorker']);
        Route::get('/reports', [\App\Http\Controllers\Api\TeamLeaderApiController::class, 'reports']);
    });
    Route::get('/team-leaders', [\App\Http\Controllers\Api\TeamLeaderApiController::class, 'teamLeaders']);

    // ══════════════════════════════════════════════
    // EMPLOYEE / WORKER PROJECT EXECUTION API
    // ══════════════════════════════════════════════
    Route::prefix('employee')->group(function () {
        Route::get('/assigned-leads', [\App\Http\Controllers\Api\WorkerLeadApiController::class, 'index']);
        Route::get('/assigned-leads/{id}', [\App\Http\Controllers\Api\WorkerLeadApiController::class, 'show']);
        Route::post('/assigned-leads/{id}/start', [\App\Http\Controllers\Api\WorkerLeadApiController::class, 'start']);
        Route::post('/assigned-leads/{id}/submit', [\App\Http\Controllers\Api\WorkerLeadApiController::class, 'submit']);
    });

    // ══════════════════════════════════════════════
    // ENTERPRISE LEAD REPORTS & METRICS API
    // ══════════════════════════════════════════════
    Route::prefix('leads')->group(function () {
        Route::get('/dashboard-stats', [\App\Http\Controllers\Api\LeadReportApiController::class, 'dashboardStats']);
        Route::get('/reports', [\App\Http\Controllers\Api\LeadReportApiController::class, 'reports']);
    });

    // ══════════════════════════════════════════════
    // ADMIN APP-SIDE MANAGEMENT API
    // ══════════════════════════════════════════════
    Route::prefix('admin')->group(function () {
        // Sales Team Management (Create, Edit, Update, Delete, Toggle Status)
        Route::get('/sales-team', [\App\Http\Controllers\Api\Admin\AdminSalesApiController::class, 'index']);
        Route::post('/sales-team', [\App\Http\Controllers\Api\Admin\AdminSalesApiController::class, 'store']);
        Route::get('/sales-team/{id}', [\App\Http\Controllers\Api\Admin\AdminSalesApiController::class, 'show']);
        Route::put('/sales-team/{id}', [\App\Http\Controllers\Api\Admin\AdminSalesApiController::class, 'update']);
        Route::delete('/sales-team/{id}', [\App\Http\Controllers\Api\Admin\AdminSalesApiController::class, 'destroy']);
        Route::patch('/sales-team/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\AdminSalesApiController::class, 'toggleStatus']);

        // Team Leader Management
        Route::get('/team-leaders', [\App\Http\Controllers\Api\Admin\AdminTeamLeaderApiController::class, 'index']);
        Route::post('/team-leaders/{id}/toggle', [\App\Http\Controllers\Api\Admin\AdminTeamLeaderApiController::class, 'toggle']);
        Route::get('/team-leaders/{id}/leads', [\App\Http\Controllers\Api\Admin\AdminTeamLeaderApiController::class, 'leads']);

        // Lead Management (CRUD & Assignment)
        Route::get('/leads', [\App\Http\Controllers\Api\Admin\AdminLeadApiController::class, 'index']);
        Route::post('/leads', [\App\Http\Controllers\Api\Admin\AdminLeadApiController::class, 'store']);
        Route::get('/leads/{id}', [\App\Http\Controllers\Api\Admin\AdminLeadApiController::class, 'show']);
        Route::put('/leads/{id}', [\App\Http\Controllers\Api\Admin\AdminLeadApiController::class, 'update']);
        Route::delete('/leads/{id}', [\App\Http\Controllers\Api\Admin\AdminLeadApiController::class, 'destroy']);
        Route::post('/leads/{id}/assign-tl', [\App\Http\Controllers\Api\Admin\AdminLeadApiController::class, 'assignTl']);
        Route::post('/leads/{id}/assign-workers', [\App\Http\Controllers\Api\Admin\AdminLeadApiController::class, 'assignWorkers']);
    });
});
