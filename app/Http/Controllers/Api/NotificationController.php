<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Fetch all notifications for the authenticated employee.
     */
    public function index(Request $request)
    {
        $employee = Auth::user();

        $notifications = Notification::where(function ($q) use ($employee) {
            $q->where('employee_id', $employee->id)
              ->orWhereNull('employee_id');
        })
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();

        $unreadCount = Notification::where('employee_id', $employee->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'data' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($id)
    {
        $employee = Auth::user();
        $notification = Notification::where('id', $id)
            ->where(function ($q) use ($employee) {
                $q->where('employee_id', $employee->id)
                  ->orWhereNull('employee_id');
            })->first();

        if ($notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all notifications for the authenticated employee as read.
     */
    public function markAllAsRead()
    {
        $employee = Auth::user();

        Notification::where('employee_id', $employee->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Save/Update Expo Push Notification Token for Employee.
     */
    public function savePushToken(Request $request)
    {
        $request->validate([
            'push_token' => 'required|string',
        ]);

        $employee = Auth::user();
        $employee->update([
            'push_token' => $request->input('push_token'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Push token saved successfully.',
        ]);
    }
}
