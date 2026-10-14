<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        $employees = $query->orderBy('name', 'asc')->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $employees
        ]);
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:employees,email',
            'phone'           => 'required|string|max:20',
            'department'      => 'required|string|max:100',
            'designation'     => 'required|string|max:100',
            'joining_date'    => 'required|date',
            'basic_salary'    => 'required|numeric',
            'employee_code'   => 'nullable|string|unique:employees,employee_code',
            'employment_type' => 'nullable|string|in:full_time,part_time,contract,intern',
            'profile_photo'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            
            // Password generation
            $password = Str::random(8);
            $data['password'] = Hash::make($password);
            
            // Handle file upload
            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('employee_photos', 'public');
            }

            // Generate employee code if not provided
            if (!$request->filled('employee_code')) {
                $data['employee_code'] = Employee::generateNextEmployeeCode($data['joining_date'] ?? null);
            }

            $employee = Employee::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Employee created successfully',
                'data' => $employee,
                'temp_password' => $password // Sending for UI convenience in this context
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating employee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified employee.
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:employees,email,' . $id,
            'phone'           => 'required|string|max:20',
            'department'      => 'required|string|max:100',
            'designation'     => 'required|string|max:100',
            'joining_date'    => 'required|date',
            'basic_salary'    => 'required|numeric',
            'employee_code'   => 'required|string|unique:employees,employee_code,' . $id,
            'is_active'       => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            if ($request->hasFile('profile_photo')) {
                if ($employee->profile_photo) {
                    Storage::disk('public')->delete($employee->profile_photo);
                }
                $data['profile_photo'] = $request->file('profile_photo')->store('employee_photos', 'public');
            }

            $employee->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully',
                'data' => $employee
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating employee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        try {
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            $employee->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting employee'
            ], 500);
        }
    }
}
