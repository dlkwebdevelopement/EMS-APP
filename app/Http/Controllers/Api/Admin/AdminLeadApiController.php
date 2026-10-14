<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminLeadApiController extends Controller
{
    /**
     * Admin: List all leads with multi-tier attribution & filtering
     */
    public function index(Request $request)
    {
        $query = Lead::with([
            'salesAgent:id,name,phone',
            'teamLeader:id,name,phone,department',
            'assignedStaff:id,name',
            'leadWorkers.worker:id,name,department,designation'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }
        if ($request->filled('tl_id')) {
            $query->where('tl_id', $request->tl_id);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('mobile', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('college_company', 'like', "%{$term}%");
            });
        }

        $leads = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $leads,
        ]);
    }

    /**
     * Admin: Create a new lead
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'college_company' => 'nullable|string|max:255',
            'course_department' => 'nullable|string|max:255',
            'enquiry_type' => 'required|string',
            'domain' => 'nullable|string|max:255',
            'service_needed' => 'nullable|string|max:255',
            'requirement' => 'nullable|string',
            'source' => 'nullable|string',
            'priority' => 'required|in:hot,warm,cold',
            'status' => 'required|in:new,contacted,interested,follow_up,converted,lost',
            'sales_id' => 'nullable|exists:employees,id',
            'tl_id' => 'nullable|exists:employees,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'deal_amount' => 'nullable|numeric|min:0',
            'next_followup_date' => 'nullable|date',
        ]);

        if (!empty($validated['tl_id'])) {
            $validated['assigned_to_tl_at'] = Carbon::now();
        }

        $validated['source'] = $validated['source'] ?? 'Admin Mobile App';
        $lead = Lead::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully!',
            'lead' => $lead->load(['salesAgent', 'teamLeader']),
        ], 201);
    }

    /**
     * Admin: Get lead details
     */
    public function show(Request $request, $id)
    {
        $lead = Lead::with([
            'salesAgent',
            'teamLeader',
            'assignedStaff',
            'leadWorkers.worker',
            'leadWorkers.assignedBy',
            'followups.staff'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'lead' => $lead,
        ]);
    }

    /**
     * Admin: Update lead details
     */
    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'mobile' => 'sometimes|required|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'college_company' => 'nullable|string|max:255',
            'course_department' => 'nullable|string|max:255',
            'enquiry_type' => 'nullable|string',
            'domain' => 'nullable|string|max:255',
            'service_needed' => 'nullable|string|max:255',
            'requirement' => 'nullable|string',
            'priority' => 'nullable|in:hot,warm,cold',
            'status' => 'nullable|in:new,contacted,interested,follow_up,converted,lost',
            'sales_id' => 'nullable|exists:employees,id',
            'tl_id' => 'nullable|exists:employees,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'deal_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'next_followup_date' => 'nullable|date',
        ]);

        if (isset($validated['tl_id']) && $validated['tl_id'] != $lead->tl_id) {
            $validated['assigned_to_tl_at'] = Carbon::now();
        }

        $lead->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully!',
            'lead' => $lead->fresh(['salesAgent', 'teamLeader', 'leadWorkers.worker']),
        ]);
    }

    /**
     * Admin: Delete lead
     */
    public function destroy(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully.',
        ]);
    }

    /**
     * Admin: Assign Lead to Team Leader
     */
    public function assignTl(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'tl_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        $tl = Employee::findOrFail($validated['tl_id']);

        $lead->update([
            'tl_id' => $tl->id,
            'assigned_to_tl_at' => Carbon::now(),
        ]);

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => optional($request->user())->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Lead Assigned to TL by Admin',
            'notes' => "Admin assigned lead to Team Leader {$tl->name}. " . ($validated['notes'] ?? ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Lead assigned to Team Leader {$tl->name} successfully!",
            'lead' => $lead->load('teamLeader'),
        ]);
    }

    /**
     * Admin: Assign Multi-Workers with Timeline (Start to End Date)
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

        $assignedWorkers = [];

        foreach ($validated['employee_ids'] as $empId) {
            LeadWorker::updateOrCreate(
                [
                    'lead_id' => $lead->id,
                    'employee_id' => $empId,
                ],
                [
                    'assigned_by_tl_id' => $lead->tl_id,
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'instructions' => $validated['instructions'] ?? null,
                    'status' => 'assigned',
                ]
            );

            $emp = Employee::find($empId);
            if ($emp) {
                $assignedWorkers[] = $emp->name;
            }
        }

        $namesList = implode(', ', $assignedWorkers);
        $endDateFmt = Carbon::parse($validated['end_date'])->format('d M Y');

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => optional($request->user())->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Admin Assigned Project Workers',
            'notes' => "Assigned workers [{$namesList}] with target deadline {$endDateFmt}.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Assigned workers [{$namesList}] with deadline {$endDateFmt}!",
            'lead_workers' => $lead->leadWorkers()->with('worker:id,name,department,designation')->get(),
        ]);
    }
}
