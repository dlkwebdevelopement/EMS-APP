<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show Sales Team login form
     */
    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->isSales() || $user->is_admin) {
                return redirect()->route('sales.dashboard');
            }
            return redirect()->route('employee.dashboard');
        }
        return view('sales.auth.login');
    }

    /**
     * Process Sales Team login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $employee = Employee::where('email', $request->email)->first();

        if ($employee && Hash::check($request->password, $employee->password)) {
            if (!$employee->is_active) {
                return back()->withErrors(['email' => 'Your Sales account is currently inactive. Please contact the administrator.'])->withInput($request->only('email'));
            }

            Auth::guard('web')->login($employee, $request->boolean('remember'));

            return redirect()->route('sales.dashboard')->with('success', "Welcome back, {$employee->name}!");
        }

        return back()->withErrors(['email' => 'Invalid sales login credentials.'])->withInput($request->only('email'));
    }

    /**
     * Show Sales Team registration form - Disabled (Admin Only)
     */
    public function showRegisterForm()
    {
        return redirect()->route('sales.login')->withErrors(['email' => 'Public registration is disabled. Sales accounts are created exclusively by the Administrator in the Admin Panel.']);
    }

    /**
     * Process Sales Team registration - Disabled (Admin Only)
     */
    public function register(Request $request)
    {
        return redirect()->route('sales.login')->withErrors(['email' => 'Public registration is disabled. Sales accounts are created exclusively by the Administrator in the Admin Panel.']);
    }

    /**
     * Sales Team logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sales.login')->with('success', 'You have been logged out safely.');
    }
}
