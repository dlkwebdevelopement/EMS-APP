<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeadWorker;
use App\Models\LeadFollowup;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AssignedLeadController extends Controller
{
    /**
     * Resolve currently authenticated employee or admin user across guards
     */
    protected function getEmployeeUser()
    {
        return Auth::guard('web')->user() ?? Auth::guard('admin')->user();
    }

    /**
     * Display projects / leads assigned to the logged-in employee
     */
    public function index(Request $request)
    {
        $employee = $this->getEmployeeUser();

        if (!$employee) {
            return redirect()->route('login')->with('error', 'Please log in to access your assigned leads.');
        }

        $query = LeadWorker::where('employee_id', $employee->id)
            ->with(['lead.salesAgent', 'assignedBy'])
            ->latest('start_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assignments = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => LeadWorker::where('employee_id', $employee->id)->count(),
            'in_progress' => LeadWorker::where('employee_id', $employee->id)->where('status', 'in_progress')->count(),
            'submitted' => LeadWorker::where('employee_id', $employee->id)->where('status', 'submitted')->count(),
            'completed' => LeadWorker::where('employee_id', $employee->id)->where('status', 'completed')->count(),
            'overdue' => LeadWorker::where('employee_id', $employee->id)
                ->where('end_date', '<', Carbon::now()->startOfDay())
                ->whereNotIn('status', ['completed', 'submitted'])
                ->count(),
        ];

        return view('employee.assigned-leads.index', compact('assignments', 'stats', 'employee'));
    }

    /**
     * Start work on assigned lead
     */
    public function start(LeadWorker $assignment)
    {
        $employee = $this->getEmployeeUser();

        if (!$employee || $assignment->employee_id !== $employee->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $assignment->status = 'in_progress';
        $assignment->save();

        LeadFollowup::create([
            'lead_id' => $assignment->lead_id,
            'user_id' => $employee->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Work Started by {$employee->name}",
            'notes' => "Staff member {$employee->name} has initiated project execution.",
        ]);

        return redirect()->back()->with('success', 'Project marked as In-Progress! Good luck!');
    }

    /**
     * Submit completed project deliverable
     */
    public function submit(Request $request, LeadWorker $assignment)
    {
        $employee = $this->getEmployeeUser();

        if (!$employee || $assignment->employee_id !== $employee->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'submission_notes' => 'required|string|min:5',
        ]);

        $assignment->submission_notes = $validated['submission_notes'];
        $assignment->submitted_at = Carbon::now();
        $assignment->status = 'submitted';
        $assignment->save();

        $timing = $assignment->submitted_at->lte($assignment->end_date->endOfDay()) ? "On-Time" : "Delayed";

        LeadFollowup::create([
            'lead_id' => $assignment->lead_id,
            'user_id' => $employee->id,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => "Project Submitted ({$timing}) by {$employee->name}",
            'notes' => "Project deliverables submitted for Team Leader review: {$validated['submission_notes']}",
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your completed project work has been submitted to your Team Leader for review!',
                'timing' => $timing,
            ]);
        }

        return redirect()->back()->with('success', 'Project work submitted successfully! Your Team Leader will review and finalize.');
    }
}
