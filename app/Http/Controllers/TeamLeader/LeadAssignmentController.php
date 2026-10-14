<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadWorker;
use App\Models\LeadFollowup;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeadAssignmentController extends Controller
{
    /**
     * Resolve currently authenticated user across guards
     */
    protected function getTlUser()
    {
        return Auth::guard('web')->user() ?? Auth::guard('admin')->user();
    }

    /**
     * Check if current user has administrator or global manager oversight
     */
    protected function isGlobalUser($user)
    {
        if (Auth::guard('admin')->check()) {
            return true;
        }
        if (!$user) {
            return false;
        }
        return (bool)($user->is_admin ?? false) || in_array($user->role ?? '', ['admin', 'manager']);
    }

    /**
     * Display listing of leads assigned to TL
     */
    public function index(Request $request)
    {
        $tlUser = $this->getTlUser();
        $isGlobal = $this->isGlobalUser($tlUser);

        $query = Lead::with(['salesAgent', 'teamLeader', 'leadWorkers.worker']);

        if (!$isGlobal && $tlUser) {
            $query->where('tl_id', $tlUser->id);
        } else {
            $query->whereNotNull('tl_id');
        }

        // Filters
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('worker_status')) {
            if ($request->worker_status === 'has_workers') {
                $query->has('leadWorkers');
            } elseif ($request->worker_status === 'no_workers') {
                $query->doesntHave('leadWorkers');
            }
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $leads = $query->latest()->paginate(15)->withQueryString();

        // Available employees for worker assignment
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        $counts = [
            'all' => (clone $query)->count(),
            'with_workers' => (clone $query)->has('leadWorkers')->count(),
            'no_workers' => (clone $query)->doesntHave('leadWorkers')->count(),
        ];

        return view('tl.leads.index', compact('leads', 'employees', 'counts'));
    }

    /**
     * Show 360° Lead View for TL with Worker Management
     */
    public function show(Lead $lead)
    {
        $tlUser = $this->getTlUser();
        $isGlobal = $this->isGlobalUser($tlUser);

        // Authorization check
        if (!$isGlobal && $tlUser && $lead->tl_id != $tlUser->id) {
            return redirect()->route('tl.leads.index')->with('error', 'You are not authorized to manage this lead.');
        }

        $lead->load(['salesAgent', 'teamLeader', 'leadWorkers.worker', 'leadWorkers.assignedBy', 'followups.staff']);

        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        return view('tl.leads.show', compact('lead', 'employees'));
    }

    /**
     * Assign / Update Multiple Employees (Workers) with Project Timeline (Start Date & End Date)
     */
    public function assignWorkers(Request $request, Lead $lead)
    {
        $tlUser = $this->getTlUser();
        $tlUserId = $tlUser ? $tlUser->id : ($lead->tl_id ?? null);

        $validated = $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'instructions' => 'nullable|string',
        ]);

        $existingWorkers = LeadWorker::where('lead_id', $lead->id)->get()->keyBy('employee_id');
        $newEmployeeIds = $validated['employee_ids'];

        // Remove workers that were deselected
        $removedNames = [];
        foreach ($existingWorkers as $empId => $existingAssignment) {
            if (!in_array($empId, $newEmployeeIds)) {
                $removedEmp = Employee::find($empId);
                if ($removedEmp) {
                    $removedNames[] = $removedEmp->name;
                }
                $existingAssignment->delete();
            }
        }

        $assignedNames = [];

        foreach ($newEmployeeIds as $empId) {
            $existing = $existingWorkers->get($empId);

            // Find existing or create new assignment (preserve status if already in progress)
            $assignment = LeadWorker::updateOrCreate(
                [
                    'lead_id' => $lead->id,
                    'employee_id' => $empId,
                ],
                [
                    'assigned_by_tl_id' => $tlUserId,
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'instructions' => $validated['instructions'] ?? null,
                    'status' => $existing ? $existing->status : 'assigned',
                ]
            );

            $emp = Employee::find($empId);
            if ($emp) {
                $assignedNames[] = $emp->name;
            }
        }

        // If lead was in new/contacted status, promote to in_progress / interested
        if (in_array($lead->status, ['new', 'contacted'])) {
            $lead->status = 'interested';
            $lead->save();
        }

        $namesList = implode(', ', $assignedNames);
        $startDateFmt = Carbon::parse($validated['start_date'])->format('d M Y');
        $endDateFmt = Carbon::parse($validated['end_date'])->format('d M Y');

        $noteText = "Updated assigned workers [{$namesList}] with execution timeline {$startDateFmt} &rarr; {$endDateFmt}.";
        if (!empty($removedNames)) {
            $noteText .= " Unassigned: [" . implode(', ', $removedNames) . "].";
        }
        if ($validated['instructions']) {
            $noteText .= " Instructions: {$validated['instructions']}";
        }

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $tlUserId,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Updated Project Workers',
            'notes' => $noteText,
        ]);

        $successMsg = "Assigned workers [{$namesList}] successfully with completion deadline {$endDateFmt}!";
        if (!empty($removedNames)) {
            $successMsg .= " (Unassigned: " . implode(', ', $removedNames) . ")";
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'assigned_names' => $assignedNames,
                'removed_names' => $removedNames,
                'start_date' => $startDateFmt,
                'end_date' => $endDateFmt,
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }

    /**
     * Update an individual worker's assignment timeline, status, or instructions
     */
    public function updateWorker(Request $request, LeadWorker $workerAssignment)
    {
        $tlUser = $this->getTlUser();
        $tlUserId = $tlUser ? $tlUser->id : null;

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:assigned,in_progress,submitted,completed,overdue',
            'instructions' => 'nullable|string',
        ]);

        $workerAssignment->update([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'instructions' => $validated['instructions'] ?? null,
        ]);

        $workerName = $workerAssignment->worker ? $workerAssignment->worker->name : 'Worker';
        $endDateFmt = Carbon::parse($validated['end_date'])->format('d M Y');

        LeadFollowup::create([
            'lead_id' => $workerAssignment->lead_id,
            'user_id' => $tlUserId,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Updated Worker: {$workerName}",
            'notes' => "Updated assignment for {$workerName} (Status: " . ucfirst(str_replace('_', ' ', $validated['status'])) . ", Deadline: {$endDateFmt}).",
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Worker assignment for {$workerName} updated successfully!",
            ]);
        }

        return redirect()->back()->with('success', "Worker assignment for {$workerName} updated successfully!");
    }

    /**
     * Remove / unassign an individual worker from lead project
     */
    public function destroyWorker(Request $request, LeadWorker $workerAssignment)
    {
        $tlUser = $this->getTlUser();
        $tlUserId = $tlUser ? $tlUser->id : null;
        $workerName = $workerAssignment->worker ? $workerAssignment->worker->name : 'Worker';
        $leadId = $workerAssignment->lead_id;

        $workerAssignment->delete();

        LeadFollowup::create([
            'lead_id' => $leadId,
            'user_id' => $tlUserId,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Unassigned Worker: {$workerName}",
            'notes' => "Unassigned {$workerName} from this project.",
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Worker {$workerName} unassigned from this project.",
            ]);
        }

        return redirect()->back()->with('success', "Worker {$workerName} unassigned from this project.");
    }

    /**
     * Approve Worker's Submitted Project Deliverable
     */
    public function approveSubmission(Request $request, LeadWorker $workerAssignment)
    {
        $tlUser = $this->getTlUser();
        $tlUserId = $tlUser ? $tlUser->id : null;
        $tlUserName = $tlUser ? $tlUser->name : 'Team Leader';

        $workerAssignment->status = 'completed';
        $workerAssignment->save();

        $workerName = $workerAssignment->worker ? $workerAssignment->worker->name : 'Worker';

        LeadFollowup::create([
            'lead_id' => $workerAssignment->lead_id,
            'user_id' => $tlUserId,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Project Submission Approved for {$workerName}",
            'notes' => "Team Leader {$tlUserName} approved and finalized project work completed by {$workerName}.",
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Submission from {$workerName} has been approved and marked as completed!",
            ]);
        }

        return redirect()->back()->with('success', "Submission from {$workerName} marked as completed!");
    }

    /**
     * Team Leader Reports
     */
    public function reports(Request $request)
    {
        $tlUser = $this->getTlUser();
        $isGlobal = $this->isGlobalUser($tlUser);

        $leadsQuery = Lead::query();
        if (!$isGlobal && $tlUser) {
            $leadsQuery->where('tl_id', $tlUser->id);
        } else {
            $leadsQuery->whereNotNull('tl_id');
        }

        $assignedLeadIds = (clone $leadsQuery)->pluck('id');

        $workerTasks = LeadWorker::whereIn('lead_id', $assignedLeadIds)
            ->with(['lead', 'worker', 'assignedBy'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_tasks' => LeadWorker::whereIn('lead_id', $assignedLeadIds)->count(),
            'in_progress' => LeadWorker::whereIn('lead_id', $assignedLeadIds)->where('status', 'in_progress')->count(),
            'submitted' => LeadWorker::whereIn('lead_id', $assignedLeadIds)->where('status', 'submitted')->count(),
            'completed' => LeadWorker::whereIn('lead_id', $assignedLeadIds)->where('status', 'completed')->count(),
            'overdue' => LeadWorker::whereIn('lead_id', $assignedLeadIds)
                ->where('end_date', '<', Carbon::now()->startOfDay())
                ->whereNotIn('status', ['completed', 'submitted'])
                ->count(),
        ];

        return view('tl.reports', compact('workerTasks', 'stats', 'tlUser'));
    }
}
