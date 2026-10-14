<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeadFollowupController extends Controller
{
    /**
     * Follow-up Management Screen
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // 1. Today's Scheduled Follow-ups
        $todayLeads = Lead::with(['assignedStaff', 'followups'])
            ->whereDate('next_followup_date', $today)
            ->whereIn('status', ['new', 'contacted', 'interested', 'follow_up'])
            ->orderBy('next_followup_date', 'asc')
            ->get();

        // 2. Overdue / Missed Follow-ups
        $overdueLeads = Lead::with(['assignedStaff', 'followups'])
            ->where('next_followup_date', '<', $now)
            ->whereDate('next_followup_date', '<', $today)
            ->whereIn('status', ['new', 'contacted', 'interested', 'follow_up'])
            ->orderBy('next_followup_date', 'asc')
            ->get();

        // 3. Upcoming Follow-ups (Next 7 days)
        $upcomingLeads = Lead::with(['assignedStaff', 'followups'])
            ->whereDate('next_followup_date', '>', $today)
            ->whereDate('next_followup_date', '<=', $today->copy()->addDays(7))
            ->whereIn('status', ['new', 'contacted', 'interested', 'follow_up'])
            ->orderBy('next_followup_date', 'asc')
            ->get();

        // 4. Recent Completed Interactions
        $completedFollowups = LeadFollowup::with(['lead', 'staff'])
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(20)
            ->get();

        $employees = Employee::orderBy('name')->get();

        return view('admin.leads.followups', compact(
            'todayLeads',
            'overdueLeads',
            'upcomingLeads',
            'completedFollowups',
            'employees'
        ));
    }
}
