<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        set_time_limit(120);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $employee = Employee::with(['experiences', 'reportingManager', 'documents'])
            ->where('email', $request->email)
            ->first();

        if (! $employee || ! Hash::check($request->password, $employee->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$employee->is_active) {
            return response()->json([
                'message' => 'Your account is deactivated. Please contact HR.'
            ], 403);
        }

        $token = $employee->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'token' => $token,
            'employee' => $this->formatEmployee($employee)
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function updatePushToken(Request $request)
    {
        $request->validate([
            'push_token' => 'required|string',
        ]);

        $employee = $request->user();
        if ($employee) {
            $employee->push_token = $request->push_token;
            $employee->save();
            return response()->json(['message' => 'Push token updated successfully']);
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    public function testPush(Request $request)
    {
        $employee = $request->user();
        
        if (!$employee || empty($employee->push_token)) {
            return response()->json(['message' => 'No push token available'], 400);
        }

        $sent = \App\Services\PushService::send(
            $employee->push_token,
            'Test Notification 🚀',
            'This is a test notification from DLK Technologies!'
        );

        if ($sent) {
            return response()->json(['message' => 'Test notification sent successfully']);
        } else {
            return response()->json(['message' => 'Failed to send notification'], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $employee = Employee::with(['reportingManager', 'documents'])->find($request->user()->id);
            return response()->json($this->formatEmployee($employee));
        } catch (\Exception $e) {
            return response()->json($request->user());
        }
    }

    public function updateProfile(Request $request)
    {
        $employee = $request->user();

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'permanent_address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'joining_date' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'hobbies' => 'nullable|string|max:255',
            'favorite_quote' => 'nullable|string|max:500',
            'is_fresher' => 'nullable|boolean',
            'total_experience_years' => 'nullable|integer|min:0',
            'total_experience_months' => 'nullable|integer|min:0|max:11',
            'previous_organization' => 'nullable|string|max:255',
            'previous_designation' => 'nullable|string|max:255',
            'internship_details' => 'nullable|string|max:1000',
            'education_qualification' => 'nullable|string|max:255',
            'education_institution' => 'nullable|string|max:255',
            'education_passout_year' => 'nullable|string|max:10',
            'education_score' => 'nullable|string|max:50',
        ]);

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'employee' => $this->formatEmployee($employee->fresh())
        ]);
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $employee = $request->user();

        if ($employee->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->profile_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->profile_photo);
        }

        $path = $request->file('profile_photo')->store('employee_photos', 'public');
        $employee->update(['profile_photo' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully!',
            'profile_photo_url' => asset('storage/' . $path),
            'employee' => $this->formatEmployee($employee->fresh())
        ]);
    }

    public function sendPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $employee = Employee::where('email', $request->email)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'No employee account found with this office email address.'
            ], 404);
        }

        $otp = sprintf("%06d", mt_rand(100000, 999999));
        $employee->update([
            'password_reset_otp' => $otp,
            'password_reset_otp_expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::send('emails.password-reset-otp', ['employee' => $employee, 'otp' => $otp], function ($message) use ($employee) {
                $message->to($employee->email)
                        ->subject('Your Password Reset Verification OTP Code');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "A 6-digit OTP verification code has been sent to your office email ({$employee->email}).",
            'otp_demo' => config('app.debug') ? $otp : null // included for testing if mail server is unconfigured
        ]);
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $employee = Employee::where('email', $request->email)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee account not found.'], 404);
        }

        if (!$employee->password_reset_otp || $employee->password_reset_otp !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP verification code.'], 400);
        }

        if (now()->gt($employee->password_reset_otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'OTP has expired. Please request a new OTP.'], 400);
        }

        $employee->update([
            'password' => Hash::make($request->password),
            'password_reset_otp' => null,
            'password_reset_otp_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully! You can now log in with your new password.'
        ]);
    }

    protected function formatEmployee(Employee $employee)
    {
        $manager = null;
        try {
            if ($employee->reporting_manager_id && $employee->reportingManager) {
                $m = $employee->reportingManager;
                $manager = [
                    'id' => $m->id,
                    'name' => $m->name,
                    'email' => $m->email,
                    'designation' => $m->designation,
                    'department' => $m->department,
                    'phone' => $m->phone,
                    'employee_code' => $m->employee_code,
                    'profile_photo_url' => $m->profile_photo ? asset('storage/' . $m->profile_photo) : null,
                ];
            }
        } catch (\Exception $e) {}

        $docsSummary = ['total' => 0, 'verified' => 0, 'pending' => 0];
        try {
            if ($employee->documents) {
                $docsSummary = [
                    'total' => $employee->documents->count(),
                    'verified' => $employee->documents->where('status', 'verified')->count(),
                    'pending' => $employee->documents->where('status', 'pending')->count(),
                ];
            }
        } catch (\Exception $e) {}

        return array_merge($employee->toArray(), [
            'profile_photo_url' => $employee->profile_photo ? asset('storage/' . $employee->profile_photo) : null,
            'employment_status' => $employee->employment_status ?? 'active',
            'formatted_experience' => $employee->formatted_experience,
            'reporting_manager' => $manager,
            'documents_summary' => $docsSummary,
        ]);
    }
}
