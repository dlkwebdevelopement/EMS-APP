<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function hierarchy(Request $request)
    {
        $currentUser = $request->user();

        // Get manager details if any
        $manager = null;
        if ($currentUser->reporting_manager_id) {
            $mgrObj = Employee::find($currentUser->reporting_manager_id);
            if ($mgrObj) {
                $manager = [
                    'id' => $mgrObj->id,
                    'name' => $mgrObj->name,
                    'email' => $mgrObj->email,
                    'phone' => $mgrObj->phone,
                    'designation' => $mgrObj->designation,
                    'department' => $mgrObj->department,
                ];
            }
        }

        // Get direct reports
        $directReports = Employee::where('reporting_manager_id', $currentUser->id)
            ->get()
            ->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->name,
                    'email' => $emp->email,
                    'phone' => $emp->phone,
                    'designation' => $emp->designation,
                    'department' => $emp->department,
                    'employment_status' => $emp->employment_status ?? 'active',
                ];
            });

        // Get overall org hierarchy (tree grouped by top-level or departments)
        $departments = Department::with(['manager', 'employees'])->get()->map(function ($dept) {
            return [
                'id' => $dept->id,
                'name' => $dept->name,
                'code' => $dept->code,
                'manager' => $dept->manager ? [
                    'id' => $dept->manager->id,
                    'name' => $dept->manager->name,
                    'designation' => $dept->manager->designation
                ] : null,
                'employee_count' => $dept->employees->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'my_manager' => $manager,
            'my_direct_reports' => $directReports,
            'departments' => $departments,
        ]);
    }
}
