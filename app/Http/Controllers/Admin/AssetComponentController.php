<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Asset;
use App\Models\AssetComponent;
use Illuminate\Support\Facades\Storage;

class AssetComponentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'cost' => 'nullable|numeric',
            'bill_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remarks' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('bill_image')) {
            $path = $request->file('bill_image')->store('assets/components/bills', 'public');
            $data['bill_image'] = $path;
        }

        // Deactivate other identical components if any
        AssetComponent::where('asset_id', $request->asset_id)
            ->where('name', $request->name)
            ->update(['is_active' => false]);

        $data['is_active'] = true;
        AssetComponent::create($data);

        return redirect()->back()->with('success', 'Hardware component added successfully.');
    }
}
