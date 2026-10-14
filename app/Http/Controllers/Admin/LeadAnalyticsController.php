<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeadAnalyticsController extends Controller
{
    /**
     * Website Lead Analytics & Reports
     */
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

        $baseQuery = Lead::whereBetween('created_at', [
            Carbon::parse($dateFrom)->startOfDay(),
            Carbon::parse($dateTo)->endOfDay()
        ]);

        $totalLeads = (clone $baseQuery)->count();
        $convertedLeads = (clone $baseQuery)->where('status', 'converted')->count();
        $lostLeads = (clone $baseQuery)->where('status', 'lost')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;
        $totalRevenue = (clone $baseQuery)->where('status', 'converted')->sum('paid_amount');

        // 1. Leads by Page
        $leadsByPage = (clone $baseQuery)
            ->select(DB::raw('COALESCE(source_page, "Direct Landing") as page'), DB::raw('count(*) as total'), DB::raw('sum(case when status="converted" then 1 else 0 end) as converted'))
            ->groupBy('page')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        // 2. Leads by Source
        $leadsBySource = (clone $baseQuery)
            ->select('source', DB::raw('count(*) as total'), DB::raw('sum(case when status="converted" then 1 else 0 end) as converted'))
            ->groupBy('source')
            ->orderBy('total', 'desc')
            ->get();

        // 3. Leads by Campaign
        $leadsByCampaign = (clone $baseQuery)
            ->whereNotNull('campaign_name')
            ->select('campaign_name', 'utm_source', 'utm_medium', DB::raw('count(*) as total'), DB::raw('sum(case when status="converted" then 1 else 0 end) as converted'))
            ->groupBy('campaign_name', 'utm_source', 'utm_medium')
            ->orderBy('total', 'desc')
            ->take(8)
            ->get();

        // 4. Most Enquired Courses & Domains
        $topCourses = (clone $baseQuery)
            ->whereNotNull('domain')
            ->select('domain', DB::raw('count(*) as total'), DB::raw('sum(case when status="converted" then 1 else 0 end) as converted'))
            ->groupBy('domain')
            ->orderBy('total', 'desc')
            ->take(8)
            ->get();

        // 5. Most Enquired Services
        $topServices = (clone $baseQuery)
            ->whereNotNull('service_needed')
            ->select('service_needed', DB::raw('count(*) as total'), DB::raw('sum(case when status="converted" then 1 else 0 end) as converted'))
            ->groupBy('service_needed')
            ->orderBy('total', 'desc')
            ->take(8)
            ->get();

        // 6. Staff Performance Report
        $staffReport = Employee::select('employees.id', 'employees.name', 'employees.department')
            ->withCount([
                'leads as total_assigned' => function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
                },
                'leads as total_converted' => function ($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'converted')
                      ->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
                },
                'leads as total_lost' => function ($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'lost')
                      ->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
                }
            ])
            ->get()
            ->filter(fn($e) => $e->total_assigned > 0)
            ->sortByDesc('total_assigned');

        // 7. Lost Leads Reasons Breakdown
        $lostReasons = (clone $baseQuery)
            ->where('status', 'lost')
            ->whereNotNull('lost_reason')
            ->select('lost_reason', DB::raw('count(*) as total'))
            ->groupBy('lost_reason')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.leads.analytics', compact(
            'dateFrom',
            'dateTo',
            'totalLeads',
            'convertedLeads',
            'lostLeads',
            'conversionRate',
            'totalRevenue',
            'leadsByPage',
            'leadsBySource',
            'leadsByCampaign',
            'topCourses',
            'topServices',
            'staffReport',
            'lostReasons'
        ));
    }
}
