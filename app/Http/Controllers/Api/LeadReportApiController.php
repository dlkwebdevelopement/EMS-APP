<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeadReportApiController extends Controller
{
    /**
     * Master Pipeline & Execution Summary Metrics
     */
    public function dashboardStats(Request $request)
    {
        $totalLeads = Lead::count();
        $todayLeads = Lead::whereDate('created_at', Carbon::today())->count();
        $convertedLeads = Lead::where('status', 'converted')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        $totalRevenue = Lead::where('status', 'converted')->sum('paid_amount');
        $totalDealValue = Lead::where('status', 'converted')->sum('deal_amount');

        $assignedToTlCount = Lead::whereNotNull('tl_id')->count();
        $totalWorkerAssignments = LeadWorker::count();
        $submittedDeliverables = LeadWorker::where('status', 'submitted')->count();
        $approvedDeliverables = LeadWorker::whereIn('status', ['approved', 'completed'])->count();

        $byStatus = Lead::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $bySource = Lead::selectRaw('source, count(*) as total')->groupBy('source')->orderByDesc('total')->take(5)->pluck('total', 'source');

        return response()->json([
            'success' => true,
            'summary' => [
                'total_leads' => $totalLeads,
                'today_leads' => $todayLeads,
                'converted_leads' => $convertedLeads,
                'conversion_rate_percent' => $conversionRate,
                'total_revenue_collected' => (float)$totalRevenue,
                'total_deal_value' => (float)$totalDealValue,
                'assigned_to_tl_leads' => $assignedToTlCount,
                'worker_assignments_total' => $totalWorkerAssignments,
                'pending_deliverable_reviews' => $submittedDeliverables,
                'completed_deliverables' => $approvedDeliverables,
            ],
            'by_status' => $byStatus,
            'top_sources' => $bySource,
        ]);
    }

    /**
     * Master Enterprise Filterable Lead & Execution Report
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
            $query->where('status', $request->status);
        }

        $totalLeads = (clone $query)->count();
        $assignedToTl = (clone $query)->whereNotNull('tl_id')->count();
        $converted = (clone $query)->where('status', 'converted')->count();
        $totalRevenue = (clone $query)->where('status', 'converted')->sum('paid_amount');

        $leads = $query->with([
            'salesAgent:id,name,phone',
            'teamLeader:id,name,phone,department',
            'leadWorkers.worker:id,name,department,designation'
        ])->latest()->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sales_id' => $request->sales_id,
                'tl_id' => $request->tl_id,
                'status' => $request->status,
            ],
            'summary' => [
                'total_leads' => $totalLeads,
                'assigned_to_tl' => $assignedToTl,
                'converted_leads' => $converted,
                'total_revenue' => (float)$totalRevenue,
            ],
            'leads' => $leads,
        ]);
    }
}
