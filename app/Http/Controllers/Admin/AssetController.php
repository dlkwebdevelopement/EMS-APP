<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetAllocation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function dashboard()
    {
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'available')->count();
        $allocatedAssets = Asset::where('status', 'allocated')->count();
        $faultyAssets = Asset::where('status', 'faulty')->count();
        $repairingAssets = Asset::where('status', 'repairing')->count();
        $retiredAssets = Asset::where('status', 'retired')->count();
        
        $totalInvestment = Asset::sum('purchase_cost');

        $categoryDistribution = AssetCategory::withCount('assets')->get();
        
        $recentAllocations = AssetAllocation::with(['asset', 'employee', 'allocatedBy'])
            ->latest()
            ->take(5)
            ->get();

        $expiringWarranty = Asset::whereNotNull('warranty_expiry')
            ->where('warranty_expiry', '>', now())
            ->where('warranty_expiry', '<=', now()->addMonths(3))
            ->orderBy('warranty_expiry')
            ->take(5)
            ->get();

        return view('admin.assets.dashboard', compact(
            'totalAssets', 'availableAssets', 'allocatedAssets', 
            'faultyAssets', 'repairingAssets', 'retiredAssets', 
            'totalInvestment', 'categoryDistribution', 'recentAllocations', 
            'expiringWarranty'
        ));
    }

    public function index(Request $request)
    {
        $query = Asset::with('category', 'currentAllocation.employee');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $assets = $query->latest()->paginate(15);
        $categories = AssetCategory::all();

        return view('admin.assets.index', compact('assets', 'categories'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        return view('admin.assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:asset_categories,id',
            'asset_tag' => 'required|unique:assets,asset_tag',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|unique:assets,serial_number',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'specs' => 'nullable|array',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bill_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = $request->all();
        $data['purchase_cost'] = $request->filled('purchase_cost') ? $request->purchase_cost : 0;

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('assets', 'public');
        }

        if ($request->hasFile('bill_image')) {
            $data['bill_image'] = $request->file('bill_image')->store('assets/purchase_bills', 'public');
        }

        Asset::create($data);

        return redirect()->route('admin.assets.index')->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'allocations.employee', 'allocations.allocatedBy', 'maintenanceRecords']);
        return view('admin.assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::all();
        return view('admin.assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'category_id' => 'required|exists:asset_categories,id',
            'asset_tag' => 'required|unique:assets,asset_tag,' . $asset->id,
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|unique:assets,serial_number,' . $asset->id,
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'status' => 'required|in:available,allocated,faulty,repairing,retired',
            'specs' => 'nullable|array',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bill_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = $request->all();
        $data['purchase_cost'] = $request->filled('purchase_cost') ? $request->purchase_cost : 0;

        if ($request->hasFile('profile_photo')) {
            if ($asset->profile_photo) {
                Storage::disk('public')->delete($asset->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('assets', 'public');
        }

        if ($request->hasFile('bill_image')) {
            if ($asset->bill_image) {
                Storage::disk('public')->delete($asset->bill_image);
            }
            $data['bill_image'] = $request->file('bill_image')->store('assets/purchase_bills', 'public');
        }

        $asset->update($data);

        return redirect()->route('admin.assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->profile_photo) {
            Storage::disk('public')->delete($asset->profile_photo);
        }
        if ($asset->bill_image) {
            Storage::disk('public')->delete($asset->bill_image);
        }
        $asset->delete();

        return redirect()->route('admin.assets.index')->with('success', 'Asset deleted successfully.');
    }
}
