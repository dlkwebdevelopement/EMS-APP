<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UnifiedAuthController extends Controller
{
    /**
     * Show Unified Login Form / Gateway
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->role === 'sales') {
                return redirect()->route('sales.dashboard');
            }
            if ($user->is_team_leader) {
                return redirect()->route('tl.dashboard');
            }
            return redirect()->route('employee.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle Unified Login Submission
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // 1. Try Admin Guard First
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $admin = Auth::guard('admin')->user();
            $admin->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
            return redirect()->intended(route('admin.dashboard'));
        }

        // 2. Try Employee Guard (Employees, TLs, Sales)
        $employee = Employee::where('email', $request->email)->first();

        if ($employee && Hash::check($request->password, $employee->password)) {
            if (!$employee->is_active) {
                return back()->withErrors([
                    'email' => 'Your account is deactivated. Please contact the administrator.',
                ])->withInput($request->only('email'));
            }

            Auth::guard('web')->login($employee, $remember);
            $request->session()->regenerate();

            // Route dynamically based on user role
            if ($employee->role === 'sales') {
                return redirect()->intended(route('sales.dashboard'));
            }
            if ($employee->is_team_leader) {
                return redirect()->intended(route('tl.dashboard'));
            }
            if ($employee->is_admin || $employee->role === 'admin') {
                // If employee also has admin privileges
                Auth::guard('admin')->loginUsingId($employee->id);
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('employee.dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Unified Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out successfully.');
    }
}
