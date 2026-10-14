<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeePermission;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Get permission history for the authenticated employee.
     */
    public function history()
    {
        $employee = Auth::user();

        $permissions = EmployeePermission::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $permissions
        ]);
    }

    /**
     * Request a new 2-hour permission.
     */
    public function request(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        $employee = Auth::user();

        // Check if shift allows 2-hour permissions
        $shift = $employee->workShift;
        if (!$shift || !$shift->is_2hour_permission_enabled) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your current work shift does not allow 2-hour permissions.'
            ], 403);
        }

        // Ensure no duplicate request on the same date
        $existing = EmployeePermission::where('employee_id', $employee->id)
            ->where('date', $request->date)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already requested a permission for this date.'
            ], 422);
        }

        // Calculate hours and enforce max 2 hours
        $hours = 2;
        if ($request->start_time && $request->end_time) {
            $start = \Carbon\Carbon::parse($request->start_time);
            $end = \Carbon\Carbon::parse($request->end_time);
            if ($end->lt($start)) {
                $end->addDay();
            }
            $diffMinutes = $start->diffInMinutes($end);
            $hours = round($diffMinutes / 60, 2);
            
            if ($hours > 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Permission duration cannot exceed 2 hours.'
                ], 422);
            }
        }

        // Limit to max permissions per month warning? We can just let them request and deduct half day.
        $permission = EmployeePermission::create([
            'employee_id' => $employee->id,
            'date' => $request->date,
            'reason' => $request->reason,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'hours' => $hours,
            'status' => 'pending' // Admin can approve, or we can auto approve for now
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permission requested successfully.',
            'data' => $permission
        ]);
    }
}
