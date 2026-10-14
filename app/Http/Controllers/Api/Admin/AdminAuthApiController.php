<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthApiController extends Controller
{
    /**
     * Admin Mobile App Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        $deviceName = $request->input('device_name', 'Admin Mobile App');

        // Check in admins table first
        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            $token = $admin->createToken($deviceName)->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Admin login successful!',
                'token' => $token,
                'user' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'role' => 'admin',
                    'is_super_admin' => (bool)$admin->is_super_admin,
                ]
            ]);
        }

        // Fallback: check employees table for admin flag
        $employee = Employee::where('email', $request->email)->first();

        if ($employee && Hash::check($request->password, $employee->password)) {
            if ($employee->is_admin || $employee->role === 'admin' || $employee->role === 'manager') {
                $token = $employee->createToken($deviceName)->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Admin login successful!',
                    'token' => $token,
                    'user' => [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'role' => 'admin',
                        'is_team_leader' => (bool)$employee->is_team_leader,
                        'department' => $employee->department,
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid administrator credentials.',
        ], 401);
    }
}
