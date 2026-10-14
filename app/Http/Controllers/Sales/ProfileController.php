<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Resolve sales user
     */
    protected function getSalesUser()
    {
        return Auth::guard('web')->user() ?? Auth::guard('admin')->user();
    }

    /**
     * Display Sales Profile
     */
    public function index()
    {
        $salesUser = $this->getSalesUser();
        if (!$salesUser) {
            return redirect()->route('sales.login');
        }

        return view('sales.profile', compact('salesUser'));
    }

    /**
     * Update Profile details
     */
    public function update(Request $request)
    {
        $salesUser = $this->getSalesUser();
        if (!$salesUser) {
            return redirect()->route('sales.login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $salesUser->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $salesUser->update($request->only('name', 'email', 'phone'));

        return redirect()->back()->with('success', 'Profile details updated successfully.');
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        $salesUser = $this->getSalesUser();
        if (!$salesUser) {
            return redirect()->route('sales.login');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $salesUser->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password entered is incorrect.']);
        }

        $salesUser->password = Hash::make($request->new_password);
        $salesUser->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}
