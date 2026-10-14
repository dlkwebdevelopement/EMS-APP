<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Lead;
use Illuminate\Http\Request;

class AdminTeamLeaderApiController extends Controller
{
    /**
     * Admin: List all Team Leaders with Project & Worker Stats
     */
    public function index(Request $request)
    {
        $teamLeaders = Employee::where(function ($q) {
            $q->where('is_team_leader', true)
              ->orWhere('role', 'team_leader')
              ->orWhere('role', 'manager');
        })->withCount([
            'assignedTlLeads as total_assigned_leads',
            'assignedTlLeads as active_leads' => function ($q) {
                $q->whereIn('status', ['new', 'contacted', 'interested', 'follow_up']);
            },
            'assignedTlLeads as converted_leads' => function ($q) {
                $q->where('status', 'converted');
            }
        ])->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'team_leaders' => $teamLeaders,
        ]);
    }

    /**
     * Admin: Toggle Employee Team Leader Role / Position
     */
    public function toggle(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $newTlStatus = !$employee->is_team_leader;

        $employee->update([
            'is_team_leader' => $newTlStatus,
            'role' => $newTlStatus ? 'team_leader' : ($employee->role === 'team_leader' ? 'employee' : $employee->role),
        ]);

        $actionText = $newTlStatus ? 'promoted to Team Leader' : 'revoked from Team Leader role';

        return response()->json([
            'success' => true,
            'message' => "Employee {$employee->name} has been {$actionText}!",
            'employee' => $employee->fresh(),
        ]);
    }

    /**
     * Admin: View Leads and Workers for a specific Team Leader
     */
    public function leads(Request $request, $id)
    {
        $tl = Employee::findOrFail($id);

        $leads = Lead::where('tl_id', $tl->id)
            ->with(['salesAgent:id,name,phone', 'leadWorkers.worker:id,name,department,designation'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'team_leader' => [
                'id' => $tl->id,
                'name' => $tl->name,
                'email' => $tl->email,
                'phone' => $tl->phone,
                'department' => $tl->department,
            ],
            'leads' => $leads,
        ]);
    }
}
