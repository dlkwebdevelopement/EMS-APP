<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Resolve sales user
     */
    protected function getSalesUser()
    {
        return Auth::guard('web')->user() ?? Auth::guard('admin')->user();
    }

    /**
     * Display Help Desk / Support tickets for current Sales user
     */
    public function index(Request $request)
    {
        $salesUser = $this->getSalesUser();
        if (!$salesUser) {
            return redirect()->route('sales.login');
        }

        $query = SupportTicket::where('employee_id', $salesUser->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(15);
        $pendingCount = SupportTicket::where('employee_id', $salesUser->id)->where('status', 'pending')->count();
        $inProgressCount = SupportTicket::where('employee_id', $salesUser->id)->where('status', 'in_progress')->count();
        $resolvedCount = SupportTicket::where('employee_id', $salesUser->id)->where('status', 'resolved')->count();

        return view('sales.support.index', compact('tickets', 'salesUser', 'pendingCount', 'inProgressCount', 'resolvedCount'));
    }

    /**
     * Submit a new Help Desk support ticket
     */
    public function store(Request $request)
    {
        $salesUser = $this->getSalesUser();
        if (!$salesUser) {
            return redirect()->route('sales.login');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $ticket = SupportTicket::create([
            'employee_id' => $salesUser->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return redirect()->route('sales.support')->with('success', "Support ticket #{$ticket->id} submitted to Help Desk. HR will review shortly.");
    }

    /**
     * View single support ticket
     */
    public function show(SupportTicket $ticket)
    {
        $salesUser = $this->getSalesUser();
        if (!$salesUser) {
            return redirect()->route('sales.login');
        }

        // Only allow viewing own ticket unless admin
        if (!Auth::guard('admin')->check() && $ticket->employee_id !== $salesUser->id) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        return view('sales.support.show', compact('ticket', 'salesUser'));
    }
}
