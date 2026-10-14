<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadWorker;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Team Leader Command Dashboard
     */
    public function index(Request $request)
    {
        $tlUser = Auth::guard('web')->user() ?? Auth::guard('admin')->user();
        $isGlobal = Auth::guard('admin')->check() || (optional($tlUser)->is_admin || optional($tlUser)->role === 'manager' || optional($tlUser)->role === 'admin');

        // Base query for leads assigned to this TL
        $leadsQuery = Lead::query();
        if (!$isGlobal && $tlUser) {
            $leadsQuery->where('tl_id', $tlUser->id);
        } else {
            $leadsQuery->whereNotNull('tl_id');
        }

        $totalAssignedLeads = (clone $leadsQuery)->count();
        $assignedLeadIds = (clone $leadsQuery)->pluck('id');

        // Worker Task Statistics across TL's leads
        $totalWorkerTasks = LeadWorker::whereIn('lead_id', $assignedLeadIds)->count();
        $inProgressTasks = LeadWorker::whereIn('lead_id', $assignedLeadIds)->where('status', 'in_progress')->count();
        $submittedTasks = LeadWorker::whereIn('lead_id', $assignedLeadIds)->where('status', 'submitted')->count();
        $completedTasks = LeadWorker::whereIn('lead_id', $assignedLeadIds)->where('status', 'completed')->count();
        
        $overdueTasks = LeadWorker::whereIn('lead_id', $assignedLeadIds)
            ->where('end_date', '<', Carbon::now()->startOfDay())
            ->whereNotIn('status', ['completed', 'submitted'])
            ->count();

        // Leads needing worker assignment (no workers assigned yet)
        $unassignedWorkersCount = (clone $leadsQuery)
            ->whereDoesntHave('leadWorkers')
            ->count();

        // Recent Assigned Leads
        $recentLeads = (clone $leadsQuery)
            ->with(['salesAgent', 'assignedStaff', 'leadWorkers.worker'])
            ->latest()
            ->take(10)
            ->get();

        // Submissions awaiting review
        $pendingSubmissions = LeadWorker::whereIn('lead_id', $assignedLeadIds)
            ->where('status', 'submitted')
            ->with(['lead', 'worker'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // All active employees available for worker assignment
        $empQuery = Employee::where('is_active', true);
        if ($tlUser) {
            $empQuery->where('id', '!=', $tlUser->id);
        }
        $employees = $empQuery->orderBy('name')->get();

        return view('tl.dashboard', compact(
            'tlUser',
            'totalAssignedLeads',
            'totalWorkerTasks',
            'inProgressTasks',
            'submittedTasks',
            'completedTasks',
            'overdueTasks',
            'unassignedWorkersCount',
            'recentLeads',
            'pendingSubmissions',
            'employees'
        ));
    }
}
