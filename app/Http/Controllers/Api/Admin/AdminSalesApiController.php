<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminSalesApiController extends Controller
{
    /**
     * Admin: List all Sales Team Members with Performance Stats
     */
    public function index(Request $request)
    {
        $query = Employee::where(function ($q) {
            $q->where('role', 'sales')
              ->orWhere('department', 'like', '%Sales%');
        })->withCount([
            'salesLeads as total_leads',
            'salesLeads as converted_leads' => function ($q) {
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

        $salesTeam = $query->orderBy('name')->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $salesTeam,
        ]);
    }

    /**
     * Admin: Create / Provision New Sales Team Employee
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'employee_code' => 'nullable|string|max:50|unique:employees,employee_code',
            'designation' => 'nullable|string|max:255',
            'basic_salary' => 'nullable|numeric|min:0',
            'joining_date' => 'nullable|date',
            'address' => 'nullable|string',
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
            'designation' => $validated['designation'] ?? 'Sales Executive',
            'role' => 'sales',
            'is_team_leader' => false,
            'is_admin' => false,
            'is_active' => true,
            'joining_date' => $validated['joining_date'] ?? now()->toDateString(),
            'basic_salary' => $validated['basic_salary'] ?? 0.00,
            'address' => $validated['address'] ?? null,
            'employment_type' => 'full_time',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sales Executive {$employee->name} created successfully!",
            'employee' => $employee,
        ], 201);
    }

    /**
     * Admin: Get Sales Representative Details & Pipeline Performance
     */
    public function show(Request $request, $id)
    {
        $employee = Employee::where(function ($q) {
            $q->where('role', 'sales')->orWhere('department', 'like', '%Sales%');
        })->withCount([
            'salesLeads as total_leads',
            'salesLeads as converted_leads' => function ($q) {
                $q->where('status', 'converted');
            }
        ])->findOrFail($id);

        $leads = Lead::where('sales_id', $employee->id)
            ->with('teamLeader:id,name,department')
            ->latest()
            ->take(20)
            ->get();

        $totalRevenue = Lead::where('sales_id', $employee->id)->where('status', 'converted')->sum('paid_amount');

        return response()->json([
            'success' => true,
            'employee' => $employee,
            'total_revenue_collected' => (float)$totalRevenue,
            'recent_leads' => $leads,
        ]);
    }

    /**
     * Admin: Update Sales Representative
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('employees')->ignore($employee->id)],
            'phone' => 'sometimes|required|string|max:20',
            'employee_code' => ['nullable', 'string', Rule::unique('employees')->ignore($employee->id)],
            'designation' => 'nullable|string|max:255',
            'basic_salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Sales Executive {$employee->name} updated successfully!",
            'employee' => $employee->fresh(),
        ]);
    }

    /**
     * Admin: Delete or Deactivate Sales Representative
     */
    public function destroy(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $name = $employee->name;

        $leadsCount = Lead::where('sales_id', $employee->id)->count();

        if ($leadsCount > 0) {
            $employee->update(['is_active' => false]);
            return response()->json([
                'success' => true,
                'action' => 'deactivated',
                'message' => "{$name} has {$leadsCount} assigned leads in CRM. Account was deactivated to protect lead attribution history.",
            ]);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'action' => 'deleted',
            'message' => "Sales Executive {$name} permanently deleted.",
        ]);
    }

    /**
     * Admin: Toggle Active / Inactive Status
     */
    public function toggleStatus(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update(['is_active' => !$employee->is_active]);

        $statusText = $employee->is_active ? 'Activated' : 'Deactivated';

        return response()->json([
            'success' => true,
            'message' => "Sales Executive {$employee->name} account {$statusText}.",
            'is_active' => (bool)$employee->is_active,
        ]);
    }
}
