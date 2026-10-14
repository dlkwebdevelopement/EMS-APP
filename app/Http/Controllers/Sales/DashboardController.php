<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Sales Team Dashboard
     */
    public function index(Request $request)
    {
        $salesUser = Auth::guard('web')->user() ?? Auth::guard('admin')->user();
        $isGlobal = Auth::guard('admin')->check() || (optional($salesUser)->is_admin || optional($salesUser)->role === 'manager' || optional($salesUser)->role === 'admin');

        // Base query scoped to sales user unless admin
        $leadsQuery = Lead::query();
        if (!$isGlobal && $salesUser) {
            $leadsQuery->where(function ($q) use ($salesUser) {
                $q->where('sales_id', $salesUser->id)
                  ->orWhere('assigned_to', $salesUser->id)
                  ->orWhereNull('sales_id');
            });
        }

        $totalLeads = (clone $leadsQuery)->count();
        $assignedToTlCount = (clone $leadsQuery)->whereNotNull('tl_id')->count();
        $pendingTlCount = (clone $leadsQuery)->whereNull('tl_id')->count();
        $convertedCount = (clone $leadsQuery)->where('status', 'converted')->count();
        $totalDealValue = (clone $leadsQuery)->where('status', 'converted')->sum('deal_amount');
        $todayLeadsCount = (clone $leadsQuery)->whereDate('created_at', Carbon::today())->count();

        // Conversion Rate
        $conversionRate = $totalLeads > 0 ? round(($convertedCount / $totalLeads) * 100, 1) : 0;

        // Recent Leads
        $recentLeads = (clone $leadsQuery)
            ->with(['teamLeader', 'assignedStaff', 'leadWorkers.worker'])
            ->latest()
            ->take(10)
            ->get();

        // Active Team Leaders for assignment dropdowns
        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)
              ->orWhere('role', 'team_leader')
              ->orWhere('role', 'manager');
        })->where('is_active', true)->orderBy('name')->get();

        return view('sales.dashboard', compact(
            'salesUser',
            'totalLeads',
            'assignedToTlCount',
            'pendingTlCount',
            'convertedCount',
            'totalDealValue',
            'todayLeadsCount',
            'conversionRate',
            'recentLeads',
            'teamLeaders'
        ));
    }
}
