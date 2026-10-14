<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display the paginated activity log with filters.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user type
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs     = $query->paginate(30)->withQueryString();

        // Distinct action types for filter dropdown
        $actionTypes = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Stats for summary cards
        $stats = [
            'total_today'  => AuditLog::whereDate('created_at', now()->toDateString())->count(),
            'total_week'   => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_month'  => AuditLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'admin_logs'   => AuditLog::where('user_type', 'admin')->count(),
        ];

        return view('admin.activity-log.index', compact('logs', 'actionTypes', 'stats'));
    }
}
