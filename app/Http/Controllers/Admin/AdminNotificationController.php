<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Display a listing of all system & admin notifications.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        
        $query = Notification::with('employee')->latest();

        if ($type) {
            $query->where('type', $type);
        }

        $notifications = $query->paginate(20);

        $summary = [
            'total'     => Notification::count(),
            'unread'    => Notification::where('is_read', false)->count(),
            'leaves'    => Notification::where('type', 'leave_request')->count(),
            'assets'    => Notification::where('type', 'asset_issue')->count(),
            'payroll'   => Notification::where('type', 'payroll')->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'summary', 'type'));
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Notification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Clear / Delete all notifications.
     */
    public function clearAll()
    {
        Notification::truncate();
        return redirect()->back()->with('success', 'All notifications cleared successfully.');
    }
}
