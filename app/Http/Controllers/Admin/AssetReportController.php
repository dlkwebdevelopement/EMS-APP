<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetAllocation;
use App\Models\AssetMaintenance;
use App\Models\AssetComponent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = AssetCategory::all();
        $statuses = ['available', 'allocated', 'faulty', 'repairing', 'retired'];
        
        $query = Asset::with(['category', 'currentAllocation.employee']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        $assets = $query->latest()->get();
        $totalCost = $assets->sum('purchase_cost');

        return view('admin.assets.reports.index', compact('assets', 'categories', 'statuses', 'totalCost'));
    }

    public function invoice(Request $request)
    {
        $query = Asset::with(['category', 'currentAllocation.employee']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        $assets = $query->get();
        $totalCost = $assets->sum('purchase_cost');
        
        // Group by category for the summary section
        $categorySummary = $assets->groupBy('category.name')->map(function($items) {
            return [
                'count' => $items->count(),
                'total_cost' => $items->sum('purchase_cost')
            ];
        });

        return view('admin.assets.reports.invoice', compact('assets', 'totalCost', 'categorySummary'));
    }

    public function maintenanceInvoice(Request $request)
    {
        $query = AssetMaintenance::with(['asset.category']);

        if ($request->filled('category_id')) {
            $query->whereHas('asset', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('maintenance_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('maintenance_at', '<=', $request->date_to);
        }

        $maintenances = $query->latest()->get();
        $totalMaintenanceCost = $maintenances->sum('cost');

        return view('admin.assets.reports.maintenance_invoice', compact('maintenances', 'totalMaintenanceCost'));
    }

    public function componentsInvoice(Request $request)
    {
        $query = AssetComponent::with(['asset.category']);

        if ($request->filled('category_id')) {
            $query->whereHas('asset', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('status')) {
            $status = $request->status == 'available' ? true : false;
            $query->where('is_active', $status);
        }

        $components = $query->latest()->get();

        return view('admin.assets.reports.components_invoice', compact('components'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'inventory');
        $format = $request->input('format', 'csv');

        if ($type == 'inventory') {
            return $this->exportInventory($format);
        } elseif ($type == 'allocations') {
            return $this->exportAllocations($format);
        }

        return redirect()->back()->with('error', 'Invalid report type.');
    }

    private function exportInventory($format)
    {
        $assets = Asset::with('category')->get();
        
        $callback = function() use ($assets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Asset Tag', 'Category', 'Brand', 'Model', 'Serial Number', 'Status', 'Purchase Date', 'Cost']);

            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->asset_tag,
                    $asset->category->name ?? 'N/A',
                    $asset->brand,
                    $asset->model,
                    $asset->serial_number,
                    ucfirst($asset->status),
                    $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : 'N/A',
                    $asset->purchase_cost
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="asset_inventory_' . date('Y-m-d') . '.csv"',
        ]);
    }

    private function exportAllocations($format)
    {
        $allocations = AssetAllocation::with(['asset', 'employee'])->get();
        
        $callback = function() use ($allocations) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Asset Tag', 'Employee', 'Allocated At', 'Returned At', 'Condition on Allocation', 'Condition on Return']);

            foreach ($allocations as $allocation) {
                fputcsv($file, [
                    $allocation->asset->asset_tag,
                    $allocation->employee->name,
                    $allocation->allocated_at->format('Y-m-d'),
                    $allocation->returned_at ? $allocation->returned_at->format('Y-m-d') : 'Still Allocated',
                    $allocation->condition_on_allocation,
                    $allocation->condition_on_return ?? 'N/A'
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="asset_allocations_' . date('Y-m-d') . '.csv"',
        ]);
    }
}
