<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetMaintenance::with('asset')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            })->orWhere('issue_description', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $maintenances = $query->paginate(15);
        return view('admin.assets.maintenance.index', compact('maintenances'));
    }

    public function create(Request $request)
    {
        $assets = Asset::all();
        $selectedAsset = $request->has('asset_id') ? Asset::find($request->asset_id) : null;

        return view('admin.assets.maintenance.create', compact('assets', 'selectedAsset'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'issue_description' => 'required|string',
            'maintenance_at' => 'required|date',
            'service_provider' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request) {
            $maintenance = AssetMaintenance::create($request->all());
            Asset::findOrFail($request->asset_id)->update(['status' => 'repairing']);
        });

        return redirect()->route('admin.assets.maintenance.index')->with('success', 'Maintenance record created and asset status updated.');
    }

    public function receive(AssetMaintenance $maintenance)
    {
        $maintenance->update([
            'status' => 'in_progress',
            'received_at' => now(),
        ]);

        $empId = $maintenance->asset->currentAllocation->employee_id ?? null;
        \App\Services\NotificationService::send(
            $empId,
            'Asset Repair In Progress',
            "Your maintenance report for asset {$maintenance->asset->asset_tag} has been received by Admin and repair is now in progress.",
            'asset_issue',
            ['asset_id' => $maintenance->asset_id]
        );

        return redirect()->back()->with('success', 'Asset marked as received and repair is in progress.');
    }

    public function complete(Request $request, AssetMaintenance $maintenance)
    {
        $request->validate([
            'completion_at' => 'required|date',
            'action_taken' => 'required|string',
            'cost' => 'required|numeric',
            'bill_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        DB::transaction(function() use ($request, $maintenance) {
            $data = [
                'completion_at' => $request->completion_at,
                'action_taken' => $request->action_taken,
                'cost' => $request->cost,
                'status' => 'completed',
            ];

            if ($request->hasFile('bill_image')) {
                $data['bill_image'] = $request->file('bill_image')->store('assets/maintenance/bills', 'public');
            }

            $maintenance->update($data);

            $maintenance->asset->update(['status' => 'available']);
        });

        $empId = $maintenance->asset->currentAllocation->employee_id ?? null;
        \App\Services\NotificationService::send(
            $empId,
            'Asset Maintenance Completed!',
            "Repair for asset {$maintenance->asset->asset_tag} has been completed. Action taken: {$request->action_taken}",
            'asset_issue',
            ['asset_id' => $maintenance->asset_id]
        );

        return redirect()->back()->with('success', 'Maintenance record updated and asset returned to available inventory.');
    }

    public function cancel(AssetMaintenance $maintenance)
    {
        DB::transaction(function() use ($maintenance) {
            $maintenance->update(['status' => 'cancelled']);
            $maintenance->asset->update(['status' => 'available']);
        });

        return redirect()->back()->with('success', 'Maintenance record cancelled and asset returned to inventory.');
    }
}
