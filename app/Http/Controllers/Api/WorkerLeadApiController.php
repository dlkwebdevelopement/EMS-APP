<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadFollowup;
use App\Models\LeadWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkerLeadApiController extends Controller
{
    /**
     * List Lead Projects Assigned to Authenticated Employee
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = LeadWorker::where('employee_id', $user->id)
            ->with([
                'lead:id,name,mobile,email,college_company,domain,service_needed,requirement,priority,status,tl_id',
                'lead.teamLeader:id,name,phone,email,department',
                'assignedBy:id,name,designation'
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assignments = $query->latest('end_date')->paginate($request->input('per_page', 15));

        // Append computed fields for convenient mobile consumption
        $assignments->getCollection()->transform(function ($item) {
            $item->is_overdue = $item->is_overdue;
            $item->days_left = $item->days_left;
            $item->status_badge = $item->status_badge;
            return $item;
        });

        $activeCount = LeadWorker::where('employee_id', $user->id)->whereIn('status', ['assigned', 'in_progress'])->count();
        $submittedCount = LeadWorker::where('employee_id', $user->id)->where('status', 'submitted')->count();
        $completedCount = LeadWorker::where('employee_id', $user->id)->whereIn('status', ['completed', 'approved'])->count();

        return response()->json([
            'success' => true,
            'counts' => [
                'active' => $activeCount,
                'submitted' => $submittedCount,
                'completed' => $completedCount,
            ],
            'assignments' => $assignments,
        ]);
    }

    /**
     * Show Details of an Assigned Lead Project
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $assignment = LeadWorker::where('employee_id', $user->id)
            ->with([
                'lead',
                'lead.teamLeader:id,name,phone,email,department',
                'assignedBy:id,name,designation'
            ])
            ->findOrFail($id);

        $assignment->is_overdue = $assignment->is_overdue;
        $assignment->days_left = $assignment->days_left;
        $assignment->status_badge = $assignment->status_badge;

        return response()->json([
            'success' => true,
            'assignment' => $assignment,
        ]);
    }

    /**
     * Employee Starts Working on Assigned Project
     */
    public function start(Request $request, $id)
    {
        $user = $request->user();

        $assignment = LeadWorker::where('employee_id', $user->id)->findOrFail($id);

        if ($assignment->status === 'assigned') {
            $assignment->update([
                'status' => 'in_progress',
            ]);

            LeadFollowup::create([
                'lead_id' => $assignment->lead_id,
                'user_id' => $user->id,
                'followup_type' => 'note',
                'completed_at' => Carbon::now(),
                'status' => 'completed',
                'outcome' => 'Worker Started Execution',
                'notes' => "Worker {$user->name} began active development / execution.",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Project marked as In Progress!',
            'assignment' => $assignment->fresh(),
        ]);
    }

    /**
     * Employee Submits Deliverables for TL Review
     */
    public function submit(Request $request, $id)
    {
        $user = $request->user();

        $assignment = LeadWorker::where('employee_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'submission_notes' => 'required|string|min:5',
        ]);

        $assignment->update([
            'submission_notes' => $validated['submission_notes'],
            'status' => 'submitted',
            'submitted_at' => Carbon::now(),
        ]);

        LeadFollowup::create([
            'lead_id' => $assignment->lead_id,
            'user_id' => $user->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Project Deliverable Submitted',
            'notes' => "Worker {$user->name} submitted deliverables: " . $validated['submission_notes'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project deliverables submitted successfully for Team Leader review!',
            'assignment' => $assignment->fresh(),
        ]);
    }
}
