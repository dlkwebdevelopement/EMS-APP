<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function hierarchy()
    {
        $employees = Employee::with(['reportingManager', 'directReports'])->where('is_active', true)->get();
        $departments = Department::with('manager')->get();

        // Top level managers (who have no reporting manager or report to super admin)
        $topLevelEmployees = Employee::whereNull('reporting_manager_id')->where('is_active', true)->get();

        return view('admin.organization.hierarchy', compact('employees', 'departments', 'topLevelEmployees'));
    }
}
