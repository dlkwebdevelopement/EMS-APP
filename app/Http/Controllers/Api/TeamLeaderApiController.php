<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeamLeaderApiController extends Controller
{
    /**
     * Team Leader Command Dashboard
     */
    public function dashboard(Request $request)
    {
        $tl = $request->user();

        $leadsQuery = Lead::where('tl_id', $tl->id);
        $totalAssignedLeads = (clone $leadsQuery)->count();
        $inExecutionLeads = (clone $leadsQuery)->whereIn('status', ['interested', 'follow_up', 'converted'])->count();

        $workerQuery = LeadWorker::whereIn('lead_id', (clone $leadsQuery)->pluck('id'));
        $totalWorkerAssignments = (clone $workerQuery)->count();
        $pendingSubmissions = (clone $workerQuery)->where('status', 'submitted')->count();
        $completedAssignments = (clone $workerQuery)->whereIn('status', ['completed', 'approved'])->count();
        $overdueAssignments = (clone $workerQuery)
            ->where('end_date', '<', Carbon::today())
            ->whereNotIn('status', ['completed', 'approved', 'submitted'])
            ->count();

        $recentAssignments = (clone $workerQuery)
            ->with(['worker:id,name,department,designation,phone', 'lead:id,name,enquiry_type,domain'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'metrics' => [
                'total_assigned_leads' => $totalAssignedLeads,
                'in_execution_leads' => $inExecutionLeads,
                'total_worker_assignments' => $totalWorkerAssignments,
                'pending_submissions' => $pendingSubmissions,
                'completed_deliverables' => $completedAssignments,
                'overdue_deadlines' => $overdueAssignments,
            ],
            'recent_assignments' => $recentAssignments,
        ]);
    }

    /**
     * List Leads Assigned to This Team Leader
     */
    public function leads(Request $request)
    {
        $tl = $request->user();

        $query = Lead::where('tl_id', $tl->id)
            ->with(['salesAgent:id,name,phone', 'leadWorkers.worker:id,name,department,designation']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('mobile', 'like', "%{$term}%")
                  ->orWhere('college_company', 'like', "%{$term}%")
                  ->orWhere('domain', 'like', "%{$term}%");
            });
        }

        $leads = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'leads' => $leads,
        ]);
    }

    /**
     * Get Single Lead Details with Worker Execution Timeline
     */
    public function leadDetails(Request $request, $id)
    {
        $lead = Lead::with([
            'salesAgent:id,name,phone,email',
            'teamLeader:id,name,department',
            'leadWorkers.worker:id,name,department,designation,phone,email',
            'leadWorkers.assignedBy:id,name',
            'followups.staff:id,name'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'lead' => $lead,
        ]);
    }

    /**
     * TL Assigns Multiple Workers to Lead with Timeline (Start to End Date)
     */
    public function assignWorkers(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'instructions' => 'nullable|string',
        ]);

        $tl = $request->user();
        $assignedWorkers = [];

        foreach ($validated['employee_ids'] as $empId) {
            $assignment = LeadWorker::updateOrCreate(
                [
                    'lead_id' => $lead->id,
                    'employee_id' => $empId,
                ],
                [
                    'assigned_by_tl_id' => $tl->id,
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'instructions' => $validated['instructions'] ?? null,
                    'status' => 'assigned',
                ]
            );

            $worker = Employee::find($empId);
            if ($worker) {
                $assignedWorkers[] = $worker->name;
            }
        }

        $namesList = implode(', ', $assignedWorkers);
        $endDateFmt = Carbon::parse($validated['end_date'])->format('d M Y');

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $tl->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Workers Assigned by TL',
            'notes' => "Assigned workers [{$namesList}] with project deadline {$endDateFmt}.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully assigned workers [{$namesList}] with deadline {$endDateFmt}!",
            'lead_workers' => $lead->leadWorkers()->with('worker:id,name,department,designation')->get(),
        ]);
    }

    /**
     * TL Approves Worker Project Deliverable Submission
     */
    public function approveWorker(Request $request, $id)
    {
        $assignment = LeadWorker::with(['worker', 'lead'])->findOrFail($id);

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
            'status' => 'nullable|in:approved,completed,revision_needed',
        ]);

        $newStatus = $validated['status'] ?? 'approved';

        $assignment->update([
            'status' => $newStatus,
            'instructions' => ($assignment->instructions ? $assignment->instructions . "\n\n" : '') . 
                "TL Review (" . now()->format('d M Y') . "): " . ($validated['approval_notes'] ?? 'Approved by Team Leader.'),
        ]);

        $tl = $request->user();
        LeadFollowup::create([
            'lead_id' => $assignment->lead_id,
            'user_id' => $tl->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Worker Deliverable Reviewed',
            'notes' => "Worker {$assignment->worker->name} submission marked as [{$newStatus}] by TL {$tl->name}.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Worker submission marked as {$newStatus}!",
            'assignment' => $assignment->fresh(['worker', 'lead']),
        ]);
    }

    /**
     * List Active Team Leaders for Dropdowns / Selectors
     */
    public function teamLeaders(Request $request)
    {
        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)
              ->orWhere('role', 'team_leader')
              ->orWhere('role', 'manager');
        })->where('is_active', true)
          ->select('id', 'name', 'email', 'phone', 'department', 'designation', 'is_team_leader', 'role')
          ->orderBy('name')
          ->get();

        return response()->json([
            'success' => true,
            'team_leaders' => $teamLeaders,
        ]);
    }

    /**
     * Team Leader Execution Report API
     */
    public function reports(Request $request)
    {
        $tl = $request->user();
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        $leadsQuery = Lead::where('tl_id', $tl->id)->whereBetween('created_at', [
            Carbon::parse($dateFrom)->startOfDay(),
            Carbon::parse($dateTo)->endOfDay(),
        ]);

        $leadIds = (clone $leadsQuery)->pluck('id');
        $totalAssignedLeads = (clone $leadsQuery)->count();

        $workers = LeadWorker::whereIn('lead_id', $leadIds)
            ->with(['worker:id,name,department,designation', 'lead:id,name,domain'])
            ->latest()
            ->get();

        $completed = $workers->whereIn('status', ['completed', 'approved'])->count();
        $submitted = $workers->where('status', 'submitted')->count();
        $overdue = $workers->filter(fn($w) => $w->is_overdue)->count();

        return response()->json([
            'success' => true,
            'summary' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'total_leads' => $totalAssignedLeads,
                'total_workers_assigned' => $workers->count(),
                'completed_deliverables' => $completed,
                'pending_reviews' => $submitted,
                'overdue_projects' => $overdue,
            ],
            'assignments' => $workers,
        ]);
    }
}
