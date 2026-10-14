<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadTemplate;
use App\Models\LeadSetting;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    /**
     * Lead Management Dashboard
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $thisMonthStart = Carbon::now()->startOfMonth();

        // High Level Metrics
        $totalLeads = Lead::count();
        $todayLeads = Lead::whereDate('created_at', $today)->count();
        $newLeads = Lead::where('status', 'new')->count();
        $followupLeads = Lead::where('status', 'follow_up')->count();
        $convertedLeads = Lead::where('status', 'converted')->count();
        $lostLeads = Lead::where('status', 'lost')->count();
        
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        // Today's Follow-ups & Overdue Follow-ups
        $todayFollowups = Lead::whereDate('next_followup_date', $today)
            ->whereIn('status', ['new', 'contacted', 'interested', 'follow_up'])
            ->with('assignedStaff')
            ->orderBy('next_followup_date', 'asc')
            ->get();

        $overdueFollowups = Lead::where('next_followup_date', '<', Carbon::now())
            ->whereIn('status', ['new', 'contacted', 'interested', 'follow_up'])
            ->with('assignedStaff')
            ->orderBy('next_followup_date', 'asc')
            ->take(10)
            ->get();

        // Source-wise Leads
        $leadsBySource = Lead::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get();

        // Page-wise Leads
        $leadsByPage = Lead::select(DB::raw('COALESCE(source_page, "Direct / Unknown") as page'), DB::raw('count(*) as count'))
            ->groupBy('page')
            ->orderBy('count', 'desc')
            ->take(8)
            ->get();

        // Enquiry Type Breakdown
        $leadsByType = Lead::select('enquiry_type', DB::raw('count(*) as count'))
            ->groupBy('enquiry_type')
            ->orderBy('count', 'desc')
            ->get();

        // Monthly Conversion Trend (Last 6 Months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $leadsCount = Lead::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $convertedCount = Lead::whereBetween('converted_at', [$monthStart, $monthEnd])->count();

            $monthlyTrend[] = [
                'month' => $month->format('M Y'),
                'leads' => $leadsCount,
                'converted' => $convertedCount,
            ];
        }

        // Daily Leads (Last 7 Days)
        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyTrend[] = [
                'day' => $date->format('D, d M'),
                'leads' => Lead::whereDate('created_at', $date)->count(),
                'converted' => Lead::whereDate('converted_at', $date)->count(),
            ];
        }

        // Recent Incoming Leads
        $recentLeads = Lead::with('assignedStaff')
            ->latest()
            ->take(8)
            ->get();

        // Total Deal Value & Revenue Collected
        $totalDealValue = Lead::where('status', 'converted')->sum('deal_amount');
        $totalRevenueCollected = Lead::where('status', 'converted')->sum('paid_amount');
        $totalPendingBalance = Lead::where('status', 'converted')->sum('balance_amount');

        return view('admin.leads.dashboard', compact(
            'totalLeads',
            'todayLeads',
            'newLeads',
            'followupLeads',
            'convertedLeads',
            'lostLeads',
            'conversionRate',
            'todayFollowups',
            'overdueFollowups',
            'leadsBySource',
            'leadsByPage',
            'leadsByType',
            'monthlyTrend',
            'dailyTrend',
            'recentLeads',
            'totalDealValue',
            'totalRevenueCollected',
            'totalPendingBalance'
        ));
    }

    /**
     * Leads Listing & Filtering
     */
    public function index(Request $request)
    {
        $query = Lead::with(['assignedStaff', 'duplicateOf']);

        // Filters
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        if ($request->filled('priority')) {
            $query->priority($request->priority);
        }

        if ($request->filled('enquiry_type')) {
            $query->enquiryType($request->enquiry_type);
        }

        if ($request->filled('source')) {
            $query->source($request->source);
        }

        if ($request->filled('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        if ($request->filled('domain')) {
            $query->where('domain', $request->domain);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->boolean('duplicates_only')) {
            $query->where('is_duplicate', true);
        }

        // Quick Tab Filtering
        $currentTab = $request->get('tab', 'all');
        if ($currentTab === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($currentTab === 'followup_today') {
            $query->whereDate('next_followup_date', Carbon::today());
        } elseif ($currentTab === 'overdue') {
            $query->where('next_followup_date', '<', Carbon::now())
                  ->whereIn('status', ['new', 'contacted', 'interested', 'follow_up']);
        } elseif ($currentTab === 'unassigned') {
            $query->whereNull('assigned_to');
        }

        $leads = $query->latest()->paginate(15)->withQueryString();

        // Counters for top filter pills
        $counts = [
            'all' => Lead::count(),
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'interested' => Lead::where('status', 'interested')->count(),
            'follow_up' => Lead::where('status', 'follow_up')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
            'lost' => Lead::where('status', 'lost')->count(),
            'today' => Lead::whereDate('created_at', Carbon::today())->count(),
            'unassigned' => Lead::whereNull('assigned_to')->count(),
            'overdue' => Lead::where('next_followup_date', '<', Carbon::now())
                ->whereIn('status', ['new', 'contacted', 'interested', 'follow_up'])->count(),
        ];

        // Master catalogs for filter dropdowns
        $employees = Employee::orderBy('name')->get();
        $sources = LeadSetting::get('lead_sources', []);
        $courses = LeadSetting::get('courses_list', []);
        $domains = LeadSetting::get('domains_list', []);

        return view('admin.leads.index', compact('leads', 'counts', 'employees', 'sources', 'courses', 'domains', 'currentTab'));
    }

    /**
     * Create Lead View
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $sources = LeadSetting::get('lead_sources', []);
        $courses = LeadSetting::get('courses_list', []);
        $domains = LeadSetting::get('domains_list', []);
        $services = LeadSetting::get('services_list', []);

        return view('admin.leads.create', compact('employees', 'sources', 'courses', 'domains', 'services'));
    }

    /**
     * Store Lead
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
            'source' => 'required|string',
            'source_page' => 'nullable|string|max:255',
            'priority' => 'required|in:hot,warm,cold',
            'status' => 'required|in:new,contacted,interested,follow_up,not_interested,converted,lost',
            'assigned_to' => 'nullable|exists:employees,id',
            'next_followup_date' => 'nullable|date',
            'deal_amount' => 'nullable|numeric|min:0',
        ]);

        // Duplicate Lead Detection
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['mobile']);
        $existingLead = Lead::where(function ($q) use ($cleanPhone, $validated) {
            $q->where('mobile', 'like', "%{$cleanPhone}%");
            if (!empty($validated['email'])) {
                $q->orWhere('email', $validated['email']);
            }
        })->where('created_at', '>=', Carbon::now()->subDays(60))->first();

        $validated['lead_code'] = Lead::generateLeadCode();
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        if ($existingLead) {
            $validated['is_duplicate'] = true;
            $validated['duplicate_of_id'] = $existingLead->id;
        }

        if (!empty($validated['assigned_to'])) {
            $validated['assigned_at'] = Carbon::now();
        }

        $lead = Lead::create($validated);

        // Record initial interaction / creation note
        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $lead->assigned_to,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Lead Created Manually',
            'notes' => 'Lead registered in CRM via Admin Panel.' . ($existingLead ? " [Duplicate detected: matches {$existingLead->lead_code}]" : ""),
        ]);

        return redirect()->route('admin.leads.show', $lead)->with('success', "Lead {$lead->lead_code} created successfully!");
    }

    /**
     * Show 360° Lead View
     */
    public function show(Lead $lead)
    {
        $lead->load(['assignedStaff', 'duplicateOf', 'duplicates', 'followups.staff', 'teamLeader', 'salesAgent', 'leadWorkers.worker', 'leadWorkers.assignedBy']);

        // Message Templates with tags replaced
        $templates = LeadTemplate::where('is_active', true)->get()->map(function ($tmpl) use ($lead) {
            $tmpl->rendered_body = $tmpl->renderForLead($lead);
            // Clean phone number for WhatsApp wa.me link
            $phone = preg_replace('/[^0-9]/', '', $lead->whatsapp ?: $lead->mobile);
            if (strlen($phone) === 10) {
                $phone = '91' . $phone; // default India prefix
            }
            $tmpl->whatsapp_url = "https://wa.me/{$phone}?text=" . rawurlencode($tmpl->rendered_body);
            return $tmpl;
        });

        // Staff list with active lead count for assignment
        $employees = Employee::select('id', 'name', 'department', 'designation')
            ->withCount(['leads as active_leads_count' => function ($q) {
                $q->whereIn('status', ['new', 'contacted', 'interested', 'follow_up']);
            }])
            ->orderBy('name')
            ->get();

        // Team leaders for TL assignment
        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)
              ->orWhere('role', 'team_leader')
              ->orWhere('role', 'manager');
        })->where('is_active', true)->orderBy('name')->get();

        return view('admin.leads.show', compact('lead', 'templates', 'employees', 'teamLeaders'));
    }

    /**
     * Edit Lead View
     */
    public function edit(Lead $lead)
    {
        $employees = Employee::orderBy('name')->get();
        $sources = LeadSetting::get('lead_sources', []);
        $courses = LeadSetting::get('courses_list', []);
        $domains = LeadSetting::get('domains_list', []);
        $services = LeadSetting::get('services_list', []);

        return view('admin.leads.edit', compact('lead', 'employees', 'sources', 'courses', 'domains', 'services'));
    }

    /**
     * Update Lead
     */
    public function update(Request $request, Lead $lead)
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
            'source' => 'required|string',
            'priority' => 'required|in:hot,warm,cold',
            'status' => 'required|in:new,contacted,interested,follow_up,not_interested,converted,lost',
            'assigned_to' => 'nullable|exists:employees,id',
            'next_followup_date' => 'nullable|date',
            'deal_amount' => 'nullable|numeric|min:0',
            'lost_reason' => 'nullable|string',
        ]);

        if ($validated['assigned_to'] != $lead->assigned_to) {
            $validated['assigned_at'] = Carbon::now();
        }

        $lead->update($validated);

        return redirect()->route('admin.leads.show', $lead)->with('success', 'Lead details updated successfully.');
    }

    /**
     * Soft Delete Lead
     */
    public function destroy(Lead $lead)
    {
        $leadCode = $lead->lead_code;
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', "Lead {$leadCode} deleted successfully.");
    }

    /**
     * Quick Status Update (AJAX / POST)
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,interested,follow_up,not_interested,converted,lost',
            'lost_reason' => 'nullable|string',
        ]);

        $oldStatus = $lead->status;
        $lead->status = $validated['status'];

        if ($validated['status'] === 'contacted' && !$lead->last_contacted_at) {
            $lead->last_contacted_at = Carbon::now();
        }

        if ($validated['status'] === 'lost') {
            $lead->lost_reason = $validated['lost_reason'] ?? $lead->lost_reason;
        }

        $lead->save();

        // Record note
        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => auth('admin')->id(),
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Status Changed: " . ucfirst($oldStatus) . " → " . ucfirst($lead->status),
            'notes' => $validated['lost_reason'] ? "Lost Reason: " . $validated['lost_reason'] : "Status updated from admin interface.",
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $lead->status,
                'badge' => $lead->status_badge,
            ]);
        }

        return redirect()->back()->with('success', "Status updated to " . ucfirst($lead->status));
    }

    /**
     * Quick Staff Assignment
     */
    public function assignStaff(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        $lead->assigned_to = $validated['assigned_to'];
        $lead->assigned_at = $validated['assigned_to'] ? Carbon::now() : null;
        $lead->save();

        $staffName = $lead->assignedStaff ? $lead->assignedStaff->name : 'Unassigned';

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $lead->assigned_to,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Assigned to {$staffName}",
            'notes' => "Lead assigned to staff member {$staffName}.",
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Assigned to {$staffName}",
                'staff_name' => $staffName,
            ]);
        }

        return redirect()->back()->with('success', "Lead assigned to {$staffName}");
    }

    /**
     * Auto-Assign Unassigned Leads
     */
    public function autoAssign(Request $request)
    {
        $unassignedLeads = Lead::whereNull('assigned_to')->get();

        if ($unassignedLeads->isEmpty()) {
            return redirect()->back()->with('info', 'No unassigned leads found.');
        }

        $routingRules = LeadSetting::get('routing_rules', []);
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'No employees available for assignment.');
        }

        $assignedCount = 0;
        $staffIndex = 0;

        foreach ($unassignedLeads as $lead) {
            $assignedEmployee = null;

            // Check department routing
            $targetDept = $routingRules[$lead->enquiry_type] ?? null;
            if ($targetDept) {
                $deptStaff = $employees->filter(function ($emp) use ($targetDept) {
                    return stripos($emp->department, $targetDept) !== false;
                });

                if ($deptStaff->isNotEmpty()) {
                    $assignedEmployee = $deptStaff->random();
                }
            }

            // Fallback: Round-robin through all employees
            if (!$assignedEmployee) {
                $assignedEmployee = $employees[$staffIndex % $employees->count()];
                $staffIndex++;
            }

            $lead->assigned_to = $assignedEmployee->id;
            $lead->assigned_at = Carbon::now();
            $lead->save();

            $assignedCount++;
        }

        return redirect()->back()->with('success', "Successfully auto-assigned {$assignedCount} leads to staff.");
    }

    /**
     * Convert Lead to Customer
     */
    public function convert(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'conversion_type' => 'required|string|in:training_registration,internship_registration,project_registration,service_customer',
            'deal_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:advance_paid,full_paid,balance_payment',
            'conversion_notes' => 'nullable|string',
        ]);

        $dealAmount = floatval($validated['deal_amount']);
        $paidAmount = floatval($validated['paid_amount']);
        $balanceAmount = max(0, $dealAmount - $paidAmount);

        $lead->status = 'converted';
        $lead->converted_at = Carbon::now();
        $lead->conversion_type = $validated['conversion_type'];
        $lead->deal_amount = $dealAmount;
        $lead->paid_amount = $paidAmount;
        $lead->balance_amount = $balanceAmount;
        $lead->payment_status = $validated['payment_status'];
        $lead->conversion_notes = $validated['conversion_notes'];
        $lead->save();

        // Record follow-up conversion activity
        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $lead->assigned_to,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'CONVERTED TO CUSTOMER',
            'notes' => "Program: " . ucwords(str_replace('_', ' ', $validated['conversion_type'])) . 
                       " | Deal Value: ₹" . number_format($dealAmount, 2) . 
                       " | Paid: ₹" . number_format($paidAmount, 2) . 
                       " | Balance: ₹" . number_format($balanceAmount, 2) . 
                       ($validated['conversion_notes'] ? " | Notes: " . $validated['conversion_notes'] : ""),
        ]);

        return redirect()->back()->with('success', "Lead {$lead->lead_code} successfully converted to customer!");
    }

    /**
     * Add Follow-up Note / Schedule Next Follow-up
     */
    public function addFollowup(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'followup_type' => 'required|in:call,whatsapp,email,meeting,note',
            'notes' => 'required|string',
            'outcome' => 'nullable|string|max:255',
            'next_followup_date' => 'nullable|date',
            'update_status' => 'nullable|in:contacted,interested,follow_up,not_interested,converted,lost',
        ]);

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $lead->assigned_to ?: auth('admin')->id(),
            'followup_type' => $validated['followup_type'],
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => $validated['outcome'] ?? 'Completed interaction',
            'notes' => $validated['notes'],
        ]);

        $lead->last_contacted_at = Carbon::now();

        if (!empty($validated['next_followup_date'])) {
            $lead->next_followup_date = $validated['next_followup_date'];
            $lead->followup_status = 'pending';
        }

        if (!empty($validated['update_status'])) {
            $lead->status = $validated['update_status'];
        } elseif ($lead->status === 'new') {
            $lead->status = 'contacted';
        }

        $lead->save();

        return redirect()->back()->with('success', 'Follow-up interaction recorded successfully!');
    }

    /**
     * Complete Follow-up
     */
    public function completeFollowup(Request $request, LeadFollowup $followup)
    {
        $followup->status = 'completed';
        $followup->completed_at = Carbon::now();
        if ($request->filled('outcome')) {
            $followup->outcome = $request->outcome;
        }
        $followup->save();

        return redirect()->back()->with('success', 'Follow-up marked as completed.');
    }

    /**
     * Export Leads to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Lead::with('assignedStaff');

        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('enquiry_type')) {
            $query->enquiryType($request->enquiry_type);
        }
        if ($request->filled('source')) {
            $query->source($request->source);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->orderBy('id', 'desc')->get();
        $fileName = 'website_leads_' . date('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($leads) {
            $handle = fopen('php://output', 'w');
            
            // Header Row
            fputcsv($handle, [
                'Lead ID',
                'Date & Time',
                'Name',
                'Mobile',
                'WhatsApp',
                'Email',
                'College / Company',
                'Course / Department',
                'Enquiry Type',
                'Domain',
                'Service Needed',
                'Requirement',
                'Message',
                'Source',
                'Source Page',
                'Landing Page',
                'Campaign Name',
                'UTM Source',
                'UTM Medium',
                'UTM Campaign',
                'Status',
                'Priority',
                'Assigned Staff',
                'Last Contact Date',
                'Next Follow-up Date',
                'Deal Value (INR)',
                'Paid Amount (INR)',
                'Balance Amount (INR)',
                'Payment Status',
                'Conversion Type',
                'Lost Reason'
            ]);

            foreach ($leads as $l) {
                fputcsv($handle, [
                    $l->lead_code,
                    $l->created_at->format('Y-m-d H:i:s'),
                    $l->name,
                    $l->mobile,
                    $l->whatsapp,
                    $l->email,
                    $l->college_company,
                    $l->course_department,
                    $l->enquiry_type_label,
                    $l->domain,
                    $l->service_needed,
                    $l->requirement,
                    $l->message,
                    $l->source_label,
                    $l->source_page,
                    $l->landing_page,
                    $l->campaign_name,
                    $l->utm_source,
                    $l->utm_medium,
                    $l->utm_campaign,
                    ucfirst($l->status),
                    ucfirst($l->priority),
                    $l->assignedStaff ? $l->assignedStaff->name : 'Unassigned',
                    $l->last_contacted_at ? $l->last_contacted_at->format('Y-m-d H:i') : '',
                    $l->next_followup_date ? $l->next_followup_date->format('Y-m-d H:i') : '',
                    $l->deal_amount,
                    $l->paid_amount,
                    $l->balance_amount,
                    $l->payment_status,
                    $l->conversion_type,
                    $l->lost_reason
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Dedicated Admin Forward to Team Leader (TL) Screen
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

        return view('admin.leads.forward-tl', compact('lead', 'teamLeaders'));
    }

    /**
     * Admin Assign Lead to Team Leader (TL)
     */
    public function assignTl(Request $request, Lead $lead)
    {
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

        $noteText = "Lead assigned to Team Leader {$tl->name} for execution oversight.";
        if (!empty($validated['assignment_notes'])) {
            $noteText .= " Notes: {$validated['assignment_notes']}";
        }

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => auth('admin')->id(),
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Assigned to Team Leader {$tl->name}",
            'notes' => $noteText,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Lead {$lead->lead_code} assigned to Team Leader {$tl->name} successfully!",
                'tl_name' => $tl->name,
                'tl_id' => $tl->id,
            ]);
        }

        return redirect()->route('admin.leads.show', $lead)->with('success', "Lead {$lead->lead_code} successfully assigned and forwarded to Team Leader {$tl->name}!");
    }

    /**
     * Admin Assign Multiple Workers with Start & End Dates
     */
    public function assignWorkers(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'instructions' => 'nullable|string',
        ]);

        $existingWorkers = \App\Models\LeadWorker::where('lead_id', $lead->id)->get()->keyBy('employee_id');
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

            \App\Models\LeadWorker::updateOrCreate(
                [
                    'lead_id' => $lead->id,
                    'employee_id' => $empId,
                ],
                [
                    'assigned_by_tl_id' => $lead->tl_id ?? null,
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

        $namesList = implode(', ', $assignedNames);
        $endDateFmt = Carbon::parse($validated['end_date'])->format('d M Y');

        $noteText = "Assigned/updated workers [{$namesList}] with completion target {$endDateFmt}.";
        if (!empty($removedNames)) {
            $noteText .= " Unassigned: [" . implode(', ', $removedNames) . "].";
        }

        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => auth('admin')->id(),
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Updated Project Workers',
            'notes' => $noteText,
        ]);

        $successMsg = "Assigned workers [{$namesList}] with completion target {$endDateFmt}!";
        if (!empty($removedNames)) {
            $successMsg .= " (Unassigned: " . implode(', ', $removedNames) . ")";
        }

        return redirect()->back()->with('success', $successMsg);
    }

    /**
     * Update individual worker assignment (Admin)
     */
    public function updateWorkerAssignment(Request $request, \App\Models\LeadWorker $workerAssignment)
    {
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
            'user_id' => auth('admin')->id(),
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Admin Updated Worker: {$workerName}",
            'notes' => "Admin updated assignment for {$workerName} (Status: " . ucfirst(str_replace('_', ' ', $validated['status'])) . ", Deadline: {$endDateFmt}).",
        ]);

        return redirect()->back()->with('success', "Worker assignment for {$workerName} updated successfully!");
    }

    /**
     * Delete/Unassign individual worker assignment (Admin)
     */
    public function destroyWorkerAssignment(Request $request, \App\Models\LeadWorker $workerAssignment)
    {
        $workerName = $workerAssignment->worker ? $workerAssignment->worker->name : 'Worker';
        $leadId = $workerAssignment->lead_id;

        $workerAssignment->delete();

        LeadFollowup::create([
            'lead_id' => $leadId,
            'user_id' => auth('admin')->id(),
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Admin Unassigned Worker: {$workerName}",
            'notes' => "Admin unassigned {$workerName} from this project.",
        ]);

        return redirect()->back()->with('success', "Worker {$workerName} unassigned from this project.");
    }

    /**
     * Enterprise Master Lead & Project Execution Report
     */
    public function reports(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        $query = Lead::whereBetween('created_at', [
            Carbon::parse($dateFrom)->startOfDay(),
            Carbon::parse($dateTo)->endOfDay(),
        ]);

        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }
        if ($request->filled('tl_id')) {
            $query->where('tl_id', $request->tl_id);
        }
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        $totalLeads = (clone $query)->count();
        $assignedToTl = (clone $query)->whereNotNull('tl_id')->count();
        $converted = (clone $query)->where('status', 'converted')->count();
        $totalRevenue = (clone $query)->where('status', 'converted')->sum('paid_amount');

        $leads = $query->with(['teamLeader', 'salesAgent', 'assignedStaff', 'leadWorkers.worker'])->latest()->paginate(20)->withQueryString();

        $salesAgents = Employee::where(function ($q) {
            $q->where('role', 'sales')->orWhere('department', 'like', '%sales%');
        })->get();

        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)->orWhere('role', 'team_leader');
        })->get();

        return view('admin.leads.reports', compact('leads', 'totalLeads', 'assignedToTl', 'converted', 'totalRevenue', 'dateFrom', 'dateTo', 'salesAgents', 'teamLeaders'));
    }
}
