<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SalesTeamController extends Controller
{
    /**
     * Display all Sales Team Employees with Performance Stats
     */
    public function index(Request $request)
    {
        $query = Employee::where(function ($q) {
            $q->where('role', 'sales')
              ->orWhere('department', 'like', '%Sales%');
        })->withCount([
            'salesLeads as total_leads_count',
            'salesLeads as converted_leads_count' => function ($q) {
                $q->where('status', 'converted');
            }
        ]);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('employee_code', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $salesTeam = $query->orderBy('name')->paginate(15)->withQueryString();

        // High-level Sales Team KPIs
        $totalSalesReps = Employee::where(function ($q) {
            $q->where('role', 'sales')->orWhere('department', 'like', '%Sales%');
        })->count();

        $activeSalesReps = Employee::where(function ($q) {
            $q->where('role', 'sales')->orWhere('department', 'like', '%Sales%');
        })->where('is_active', true)->count();

        $salesIds = Employee::where(function ($q) {
            $q->where('role', 'sales')->orWhere('department', 'like', '%Sales%');
        })->pluck('id');

        $totalSalesLeads = Lead::whereIn('sales_id', $salesIds)->count();
        $totalSalesRevenue = Lead::whereIn('sales_id', $salesIds)->where('status', 'converted')->sum('paid_amount');

        return view('admin.sales-team.index', compact(
            'salesTeam',
            'totalSalesReps',
            'activeSalesReps',
            'totalSalesLeads',
            'totalSalesRevenue'
        ));
    }

    /**
     * Show form to create a new Sales Team Member
     */
    public function create()
    {
        // Suggest next standardized DLK employee code (e.g. DLK26@387)
        $suggestedCode = Employee::generateNextEmployeeCode();

        return view('admin.sales-team.create', compact('suggestedCode'));
    }

    /**
     * Store newly created Sales Team Member
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'employee_code' => 'nullable|string|max:50|unique:employees,employee_code',
            'password' => 'required|string|min:6|confirmed',
            'designation' => 'required|string|max:255',
            'joining_date' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
        ]);

        if (empty($validated['employee_code'])) {
            $validated['employee_code'] = Employee::generateNextEmployeeCode($validated['joining_date'] ?? null);
        }

        $employee = Employee::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'employee_code' => $validated['employee_code'],
            'password' => Hash::make($validated['password']),
            'department' => 'Sales & Business Development',
            'designation' => $validated['designation'],
            'role' => 'sales',
            'is_team_leader' => false,
            'is_admin' => false,
            'is_active' => true,
            'joining_date' => $validated['joining_date'] ?? now()->toDateString(),
            'basic_salary' => $validated['basic_salary'] ?? 0.00,
            'address' => $validated['address'] ?? null,
            'employment_type' => 'full_time',
        ]);

        return redirect()->route('admin.sales-team.index')->with('success', "Sales Executive {$employee->name} added successfully!");
    }

    /**
     * Show form to edit a Sales Team Member
     */
    public function edit(Employee $sales_team)
    {
        $employee = $sales_team;
        return view('admin.sales-team.edit', compact('employee'));
    }

    /**
     * Update Sales Team Member
     */
    public function update(Request $request, Employee $sales_team)
    {
        $employee = $sales_team;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('employees')->ignore($employee->id)],
            'phone' => 'required|string|max:20',
            'employee_code' => ['nullable', 'string', Rule::unique('employees')->ignore($employee->id)],
            'designation' => 'required|string|max:255',
            'joining_date' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'designation' => $validated['designation'],
            'joining_date' => $validated['joining_date'] ?? $employee->joining_date,
            'basic_salary' => $validated['basic_salary'] ?? $employee->basic_salary,
            'address' => $validated['address'] ?? $employee->address,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $employee->is_active,
        ];

        if (!empty($validated['employee_code'])) {
            $updateData['employee_code'] = $validated['employee_code'];
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $employee->update($updateData);

        return redirect()->route('admin.sales-team.index')->with('success', "Sales Executive {$employee->name} details updated successfully!");
    }

    /**
     * Delete Sales Team Member
     */
    public function destroy(Employee $sales_team)
    {
        $employee = $sales_team;
        $name = $employee->name;

        // Check if employee has captured leads
        $leadCount = Lead::where('sales_id', $employee->id)->count();

        if ($leadCount > 0) {
            // Deactivate instead of hard delete to preserve CRM history
            $employee->update(['is_active' => false]);
            return redirect()->route('admin.sales-team.index')->with('warning', "{$name} has {$leadCount} leads attached in CRM history. Account was deactivated instead of permanently deleted to preserve attribution.");
        }

        $employee->delete();
        return redirect()->route('admin.sales-team.index')->with('success', "Sales Executive {$name} removed successfully.");
    }

    /**
     * Toggle Active / Inactive Status
     */
    public function toggleStatus(Employee $sales_team)
    {
        $employee = $sales_team;
        $employee->update(['is_active' => !$employee->is_active]);

        $statusText = $employee->is_active ? 'Activated' : 'Deactivated';
        return redirect()->back()->with('success', "Sales Executive {$employee->name} account {$statusText} successfully.");
    }

    /**
     * View Leads captured by this sales executive
     */
    public function leads(Employee $sales_team)
    {
        $employee = $sales_team;
        return redirect()->route('admin.leads.index', ['sales_id' => $employee->id]);
    }
}
