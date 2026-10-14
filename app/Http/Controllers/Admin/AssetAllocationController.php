<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetAllocation;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AssetComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssetAllocationController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetAllocation::with(['asset.category', 'employee', 'allocatedBy'])->latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('employee', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('asset', function($sq) use ($search) {
                    $sq->where('asset_tag', 'like', "%{$search}%")
                      ->orWhere('serial_number', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->whereNull('returned_at');
            } elseif ($request->status == 'returned') {
                $query->whereNotNull('returned_at');
            }
        }

        $allocations = $query->paginate(15);
        return view('admin.assets.allocations.index', compact('allocations'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $assets = Asset::where('status', 'available')->get();
        $selectedAsset = $request->has('asset_id') ? Asset::find($request->asset_id) : null;
        $selectedEmployee = $request->has('employee_id') ? Employee::find($request->employee_id) : null;

        return view('admin.assets.allocations.create', compact('employees', 'assets', 'selectedAsset', 'selectedEmployee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'allocated_at' => 'required|date',
            'condition_on_allocation' => 'required|string',
            'remarks' => 'nullable|string',
            // Component Fields
            'component_name' => 'nullable|string|max:255',
            'component_brand' => 'nullable|string|max:255',
            'component_model' => 'nullable|string|max:255',
            'component_serial' => 'nullable|string|max:255',
            'component_cost' => 'nullable|numeric',
        ]);

        $asset = Asset::findOrFail($request->asset_id);
        if ($asset->status !== 'available') {
            return redirect()->back()->with('error', 'This asset is not available for allocation.')->withInput();
        }

        DB::transaction(function() use ($request, $asset) {
            $data = $request->only(['asset_id', 'employee_id', 'allocated_at', 'condition_on_allocation', 'remarks']);
            $data['allocated_by'] = auth()->guard('admin')->id();

            AssetAllocation::create($data);
            $asset->update(['status' => 'allocated']);

            // If component details are provided, create a component record
            if ($request->filled('component_name')) {
                // Deactivate identical components first (Decommissioning Logic)
                AssetComponent::where('asset_id', $asset->id)
                    ->where('name', $request->component_name)
                    ->update(['is_active' => false]);

                $componentData = [
                    'asset_id' => $asset->id,
                    'name' => $request->component_name,
                    'brand' => $request->component_brand,
                    'model' => $request->component_model,
                    'serial_number' => $request->component_serial,
                    'purchase_cost' => $request->component_cost,
                    'is_active' => true,
                    'purchase_date' => $request->allocated_at, // Use allocation date as default
                    'remarks' => 'Added during allocation'
                ];

                AssetComponent::create($componentData);
            }
        });

        $employee = \App\Models\Employee::find($request->employee_id);
        if ($employee && !empty($employee->push_token)) {
            \App\Services\PushService::send(
                $employee->push_token,
                'New Asset Assigned 💻',
                "You have been allocated a new asset: {$asset->name}.",
                ['type' => 'asset']
            );
        }

        return redirect()->route('admin.assets.index')->with('success', 'Asset allocated successfully.');
    }

    public function returnAsset(Request $request, AssetAllocation $allocation)
    {
        $request->validate([
            'returned_at' => 'required|date',
            'condition_on_return' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function() use ($request, $allocation) {
            $allocation->update([
                'returned_at' => $request->returned_at,
                'condition_on_return' => $request->condition_on_return,
                'remarks' => $request->remarks,
            ]);
            $allocation->asset->update(['status' => 'available']);
        });

        $employee = $allocation->employee;
        if ($employee && !empty($employee->push_token)) {
            \App\Services\PushService::send(
                $employee->push_token,
                'Asset Returned ✅',
                "Your asset ({$allocation->asset->name}) has been successfully returned.",
                ['type' => 'asset']
            );
        }

        return redirect()->back()->with('success', 'Asset returned and marked as available.');
    }

    public function handover(AssetAllocation $allocation)
    {
        $allocation->load(['asset.category', 'employee', 'allocatedBy']);
        return view('admin.assets.allocations.handover', compact('allocation'));
    }
}
