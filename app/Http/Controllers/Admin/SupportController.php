<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Notification;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('employee');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.support.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('employee');
        return view('admin.support.show', compact('ticket'));
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'admin_reply' => 'nullable|string'
        ]);

        $ticket->update([
            'status' => $request->status,
            'admin_reply' => $request->admin_reply
        ]);

        // Send a notification to the employee
        $statusFormat = str_replace('_', ' ', ucfirst($request->status));
        $msg = "Your Support Ticket #{$ticket->id} is now '{$statusFormat}'.";
        if ($request->admin_reply) {
            $msg .= " HR has replied.";
        }

        Notification::create([
            'employee_id' => $ticket->employee_id,
            'title' => 'Support Ticket Update',
            'message' => $msg,
            'type' => 'support',
            'is_read' => false
        ]);

        return redirect()->route('admin.support.index')->with('success', 'Support ticket updated successfully.');
    }
}
