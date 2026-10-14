<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadSetting;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    /**
     * Resolve currently authenticated user across guards
     */
    protected function getSalesUser()
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
     * Sales Leads Pipeline View
     */
    public function index(Request $request)
    {
        $salesUser = $this->getSalesUser();
        $isGlobal = $this->isGlobalUser($salesUser);

        $query = Lead::with(['teamLeader', 'assignedStaff', 'leadWorkers.worker']);

        if (!$isGlobal && $salesUser) {
            $query->where(function ($q) use ($salesUser) {
                $q->where('sales_id', $salesUser->id)
                  ->orWhere('assigned_to', $salesUser->id)
                  ->orWhereNull('sales_id');
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('priority')) {
            $query->priority($request->priority);
        }
        if ($request->filled('tl_id')) {
            $query->where('tl_id', $request->tl_id);
        }
        if ($request->filled('tl_assignment')) {
            if ($request->tl_assignment === 'assigned') {
                $query->whereNotNull('tl_id');
            } elseif ($request->tl_assignment === 'unassigned') {
                $query->whereNull('tl_id');
            }
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $leads = $query->latest()->paginate(15)->withQueryString();

        // Team leaders for assignment dropdown
        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)
              ->orWhere('role', 'team_leader')
              ->orWhere('role', 'manager');
        })->where('is_active', true)->orderBy('name')->get();

        $counts = [
            'all' => (clone $query)->count(),
            'assigned_tl' => Lead::whereNotNull('tl_id')->count(),
            'unassigned_tl' => Lead::whereNull('tl_id')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
        ];

        return view('sales.leads.index', compact('leads', 'teamLeaders', 'counts'));
    }

    /**
     * Show Lead Creation Form
     */
    public function create()
    {
        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)
              ->orWhere('role', 'team_leader')
              ->orWhere('role', 'manager');
        })->where('is_active', true)->orderBy('name')->get();

        $sources = LeadSetting::get('lead_sources', []);
        $courses = LeadSetting::get('courses_list', []);
        $domains = LeadSetting::get('domains_list', []);
        $services = LeadSetting::get('services_list', []);

        return view('sales.leads.create', compact('teamLeaders', 'sources', 'courses', 'domains', 'services'));
    }

    /**
     * Store Lead
     */
    public function store(Request $request)
    {
        $salesUser = $this->getSalesUser();
        $salesUserId = $salesUser ? $salesUser->id : null;
        $salesUserName = $salesUser ? $salesUser->name : 'Sales Staff';

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
            'source' => 'required|string',
            'priority' => 'required|in:hot,warm,cold',
            'status' => 'required|in:new,contacted,interested,follow_up,not_interested,converted,lost',
            'tl_id' => 'nullable|exists:employees,id',
            'deal_amount' => 'nullable|numeric|min:0',
            'next_followup_date' => 'nullable|date',
        ]);

        $validated['lead_code'] = Lead::generateLeadCode();
        $validated['sales_id'] = $salesUserId;
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        if (!empty($validated['tl_id'])) {
            $validated['assigned_to_tl_at'] = Carbon::now();
        }

        $lead = Lead::create($validated);

        // Activity log
        $tlNote = "";
        if ($lead->tl_id && $lead->teamLeader) {
            $tlNote = " Assigned to Team Leader {$lead->teamLeader->name}.";
        }

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $salesUserId,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Lead Created by Sales',
            'notes' => "Lead created by Sales Executive {$salesUserName}.{$tlNote}",
        ]);

        return redirect()->route('sales.leads.index')->with('success', "Lead {$lead->lead_code} created successfully!");
    }

    /**
     * Show Lead Edit Form
     */
    public function edit(Lead $lead)
    {
        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)
              ->orWhere('role', 'team_leader')
              ->orWhere('role', 'manager');
        })->where('is_active', true)->orderBy('name')->get();

        $sources = LeadSetting::get('lead_sources', []);
        $courses = LeadSetting::get('courses_list', []);
        $domains = LeadSetting::get('domains_list', []);
        $services = LeadSetting::get('services_list', []);

        return view('sales.leads.edit', compact('lead', 'teamLeaders', 'sources', 'courses', 'domains', 'services'));
    }

    /**
     * Update Lead
     */
    public function update(Request $request, Lead $lead)
    {
        $salesUser = $this->getSalesUser();
        $salesUserId = $salesUser ? $salesUser->id : null;
        $salesUserName = $salesUser ? $salesUser->name : 'Staff';

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
            'source' => 'required|string',
            'priority' => 'required|in:hot,warm,cold',
            'status' => 'required|in:new,contacted,interested,follow_up,not_interested,converted,lost',
            'tl_id' => 'nullable|exists:employees,id',
            'deal_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'next_followup_date' => 'nullable|date',
            'conversion_notes' => 'nullable|string',
        ]);

        if (!empty($validated['tl_id']) && $lead->tl_id != $validated['tl_id']) {
            $validated['assigned_to_tl_at'] = Carbon::now();
        }

        $lead->update($validated);

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $salesUserId,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Lead Updated by Sales',
            'notes' => "Lead updated by Sales Executive {$salesUserName}.",
        ]);

        return redirect()->route('sales.leads.index')->with('success', "Lead {$lead->lead_code} updated successfully!");
    }

    /**
     * Dedicated Forward to Team Leader (TL) Screen
     */
    public function forwardTlScreen(Lead $lead)
    {
        $lead->load(['teamLeader', 'salesAgent', 'assignedStaff', 'leadWorkers.worker']);

        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)
              ->orWhere('role', 'team_leader')
              ->orWhere('role', 'manager');
        })->where('is_active', true)
          ->withCount(['assignedLeadsAsTl' => function($q) {
              $q->whereNotIn('status', ['converted', 'lost']);
          }])
          ->orderBy('name')
          ->get();

        return view('sales.leads.forward-tl', compact('lead', 'teamLeaders'));
    }

    /**
     * Assign Lead to Team Leader (TL)
     */
    public function assignToTl(Request $request, Lead $lead)
    {
        $salesUser = $this->getSalesUser();
        $salesUserId = $salesUser ? $salesUser->id : null;

        $validated = $request->validate([
            'tl_id' => 'required|exists:employees,id',
            'assignment_notes' => 'nullable|string',
            'priority' => 'nullable|in:hot,warm,cold',
            'deal_amount' => 'nullable|numeric|min:0',
        ]);

        $tl = Employee::findOrFail($validated['tl_id']);

        $lead->tl_id = $tl->id;
        $lead->assigned_to_tl_at = Carbon::now();

        if ($request->filled('priority')) {
            $lead->priority = $validated['priority'];
        }
        if ($request->filled('deal_amount')) {
            $lead->deal_amount = $validated['deal_amount'];
        }

        $lead->save();

        $noteText = "Lead forwarded to Team Leader {$tl->name} for project execution oversight.";
        if (!empty($validated['assignment_notes'])) {
            $noteText .= " Instructions: " . $validated['assignment_notes'];
        }

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $salesUserId,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Forwarded to TL {$tl->name}",
            'notes' => $noteText,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Lead {$lead->lead_code} successfully forwarded to Team Leader {$tl->name}!",
                'tl_name' => $tl->name,
                'tl_id' => $tl->id,
            ]);
        }

        return redirect()->route('sales.leads.index')->with('success', "Lead {$lead->lead_code} successfully assigned and forwarded to Team Leader {$tl->name}!");
    }

    /**
     * Sales Lead Reports
     */
    public function reports(Request $request)
    {
        $salesUser = $this->getSalesUser();
        $isGlobal = $this->isGlobalUser($salesUser);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        $query = Lead::whereBetween('created_at', [
            Carbon::parse($dateFrom)->startOfDay(),
            Carbon::parse($dateTo)->endOfDay(),
        ]);

        if (!$isGlobal && $salesUser) {
            $query->where(function ($q) use ($salesUser) {
                $q->where('sales_id', $salesUser->id)->orWhereNull('sales_id');
            });
        }

        $totalLeads = (clone $query)->count();
        $assignedToTl = (clone $query)->whereNotNull('tl_id')->count();
        $converted = (clone $query)->where('status', 'converted')->count();
        $totalRevenue = (clone $query)->where('status', 'converted')->sum('paid_amount');

        $leads = $query->with(['teamLeader', 'leadWorkers.worker'])->latest()->paginate(20)->withQueryString();

        return view('sales.reports', compact('leads', 'totalLeads', 'assignedToTl', 'converted', 'totalRevenue', 'dateFrom', 'dateTo'));
    }

    /**
     * Display Lead details for Sales Executive
     */
    public function show(Lead $lead)
    {
        $salesUser = $this->getSalesUser();
        $isGlobal = $this->isGlobalUser($salesUser);

        if (!$isGlobal && $salesUser && $lead->sales_id && $lead->sales_id !== $salesUser->id && $lead->assigned_to !== $salesUser->id) {
            // Permitted to view, but warn or scope
        }

        $lead->load(['teamLeader', 'assignedStaff', 'leadWorkers.worker', 'followups.user']);

        return view('sales.leads.show', compact('lead', 'salesUser'));
    }
}
