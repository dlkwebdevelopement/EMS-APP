<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetAllocation;
use App\Models\AssetMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * Display a listing of assets currently allocated to the employee.
     */
    public function myAssets()
    {
        $employee = Auth::user();
        
        $allocations = AssetAllocation::with(['asset.category', 'allocatedBy'])
            ->where('employee_id', $employee->id)
            ->whereNull('returned_at')
            ->get();

        foreach ($allocations as $allocation) {
            if ($allocation->asset) {
                $allocation->asset->image_url = $allocation->asset->profile_photo 
                    ? asset('storage/' . $allocation->asset->profile_photo)
                    : null;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $allocations
        ]);
    }

    /**
     * Submit an Asset Issue / Maintenance Report from Employee App.
     */
    public function reportIssue(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|integer',
            'issue_description' => 'required|string|max:1000',
        ]);

        $employee = Auth::user();
        $assetId = $request->input('asset_id');
        $issueDescription = $request->input('issue_description');

        $maintenance = AssetMaintenance::create([
            'asset_id' => $assetId,
            'issue_description' => "Reported by {$employee->name} ({$employee->email}): " . $issueDescription,
            'status' => 'pending',
            'maintenance_at' => now(),
        ]);

        // Create Admin Notification for Reported Asset Issue
        \App\Models\Notification::create([
            'employee_id' => null, // Global / Admin notification
            'title' => 'New Asset Issue Reported!',
            'message' => "{$employee->name} reported an issue for asset: " . ($maintenance->asset->brand ?? 'Asset') . " " . ($maintenance->asset->model ?? '') . " (Tag: " . ($maintenance->asset->asset_tag ?? '') . ")",
            'type' => 'asset_issue',
            'is_read' => false,
            'data' => [
                'asset_id' => $assetId,
                'maintenance_id' => $maintenance->id,
                'reporter_name' => $employee->name,
                'reporter_email' => $employee->email,
            ]
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Asset issue report submitted successfully to Admin Panel.',
            'data' => $maintenance
        ]);
    }
}
