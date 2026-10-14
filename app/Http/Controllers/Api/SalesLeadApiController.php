<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\LeadFollowup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SalesLeadApiController extends Controller
{
    /**
     * Mobile App Sales Team Registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'device_name' => 'nullable|string',
        ]);

        $salesCode = $request->filled('employee_code') 
            ? $request->employee_code 
            : Employee::generateNextEmployeeCode();

        $employee = Employee::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'employee_code' => $salesCode,
            'department' => 'Sales & Business Development',
            'designation' => 'Sales Executive',
            'role' => 'sales',
            'is_team_leader' => false,
            'is_admin' => false,
            'is_active' => true,
            'joining_date' => now()->toDateString(),
            'basic_salary' => 0.00,
            'employment_type' => 'full_time',
        ]);

        $deviceName = $request->input('device_name', 'Mobile App');
        $token = $employee->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Sales account registered successfully!',
            'token' => $token,
            'user' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'role' => $employee->role,
                'is_team_leader' => $employee->is_team_leader,
                'department' => $employee->department,
                'designation' => $employee->designation,
                'employee_code' => $employee->employee_code,
            ]
        ], 201);
    }

    /**
     * Mobile App Sales Team Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        $employee = Employee::where('email', $request->email)->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials provided.',
            ], 401);
        }

        if (!$employee->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is deactivated. Please contact administrator.',
            ], 403);
        }

        $deviceName = $request->input('device_name', 'Mobile App');
        $token = $employee->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Sales login successful!',
            'token' => $token,
            'user' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'role' => $employee->role,
                'is_team_leader' => (bool)$employee->is_team_leader,
                'department' => $employee->department,
                'designation' => $employee->designation,
                'employee_code' => $employee->employee_code,
            ]
        ]);
    }

    /**
     * Mobile App Sales Dashboard KPI Metrics
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $query = Lead::where(function ($q) use ($user) {
            $q->where('sales_id', $user->id)
              ->orWhere('assigned_to', $user->id);
        });

        $totalLeads = (clone $query)->count();
        $newLeads = (clone $query)->where('status', 'new')->count();
        $contactedLeads = (clone $query)->where('status', 'contacted')->count();
        $interestedLeads = (clone $query)->where('status', 'interested')->count();
        $convertedLeads = (clone $query)->where('status', 'converted')->count();
        $assignedToTl = (clone $query)->whereNotNull('tl_id')->count();
        $totalRevenue = (clone $query)->where('status', 'converted')->sum('paid_amount');

        $todayFollowups = (clone $query)
            ->whereDate('next_followup_date', Carbon::today())
            ->whereIn('status', ['new', 'contacted', 'interested', 'follow_up'])
            ->count();

        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        $recentLeads = (clone $query)
            ->with(['teamLeader:id,name,department', 'assignedStaff:id,name'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'metrics' => [
                'total_leads' => $totalLeads,
                'new_leads' => $newLeads,
                'contacted_leads' => $contactedLeads,
                'interested_leads' => $interestedLeads,
                'converted_leads' => $convertedLeads,
                'assigned_to_tl' => $assignedToTl,
                'today_followups' => $todayFollowups,
                'conversion_rate_percentage' => $conversionRate,
                'total_revenue_collected' => (float)$totalRevenue,
            ],
            'recent_leads' => $recentLeads,
        ]);
    }

    /**
     * List Sales Leads with Filters & Search
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Lead::where(function ($q) use ($user) {
            $q->where('sales_id', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->with(['teamLeader:id,name,designation', 'leadWorkers.worker:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
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
     * Create / Capture New Sales Lead
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
            'message' => 'nullable|string',
            'source' => 'nullable|string',
            'priority' => 'required|in:hot,warm,cold',
            'status' => 'required|in:new,contacted,interested,follow_up,converted,lost',
            'tl_id' => 'nullable|exists:employees,id',
            'next_followup_date' => 'nullable|date',
            'deal_amount' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();
        $validated['sales_id'] = $user->id;
        $validated['assigned_to'] = $user->id;
        $validated['assigned_at'] = Carbon::now();
        $validated['source'] = $validated['source'] ?? 'Sales Rep Mobile App';

        if (!empty($validated['tl_id'])) {
            $validated['assigned_to_tl_at'] = Carbon::now();
        }

        $lead = Lead::create($validated);

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Lead Created via Mobile App',
            'notes' => "Lead created by Sales Executive {$user->name}.",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully!',
            'lead' => $lead->load('teamLeader'),
        ], 201);
    }

    /**
     * Get Single Lead Details
     */
    public function show(Request $request, $id)
    {
        $lead = Lead::with([
            'teamLeader:id,name,department,designation,phone,email',
            'salesAgent:id,name,phone,email',
            'leadWorkers.worker:id,name,department,designation,phone',
            'followups.staff:id,name'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'lead' => $lead,
        ]);
    }

    /**
     * Update Lead Details
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
            'tl_id' => 'nullable|exists:employees,id',
            'next_followup_date' => 'nullable|date',
            'deal_amount' => 'nullable|numeric|min:0',
        ]);

        if (isset($validated['tl_id']) && $validated['tl_id'] != $lead->tl_id) {
            $validated['assigned_to_tl_at'] = Carbon::now();
        }

        $lead->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully!',
            'lead' => $lead->fresh(['teamLeader', 'leadWorkers.worker']),
        ]);
    }

    /**
     * Assign Lead to Team Leader (TL)
     */
    public function assignToTl(Request $request, $id)
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

        $user = $request->user();
        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Assigned to TL',
            'notes' => "Lead assigned to Team Leader {$tl->name} by {$user->name}. " . ($validated['notes'] ?? ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Lead successfully assigned to Team Leader {$tl->name}!",
            'lead' => $lead->load('teamLeader'),
        ]);
    }

    /**
     * Sales Performance Report API
     */
    public function reports(Request $request)
    {
        $user = $request->user();
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        $query = Lead::where(function ($q) use ($user) {
            $q->where('sales_id', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->whereBetween('created_at', [
            Carbon::parse($dateFrom)->startOfDay(),
            Carbon::parse($dateTo)->endOfDay(),
        ]);

        $totalLeads = (clone $query)->count();
        $converted = (clone $query)->where('status', 'converted')->count();
        $totalRevenue = (clone $query)->where('status', 'converted')->sum('paid_amount');
        $assignedToTl = (clone $query)->whereNotNull('tl_id')->count();

        $leadsByStatus = (clone $query)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $leads = $query->with('teamLeader:id,name')->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'summary' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'total_leads' => $totalLeads,
                'converted' => $converted,
                'assigned_to_tl' => $assignedToTl,
                'total_revenue' => (float)$totalRevenue,
                'status_breakdown' => $leadsByStatus,
            ],
            'leads' => $leads,
        ]);
    }
}
