<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;

class AccountDeletionRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountDeletionRequest::with('employee');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.account_deletions.index', compact('requests'));
    }

    public function approve(AccountDeletionRequest $deletionRequest)
    {
        if ($deletionRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        try {
            DB::beginTransaction();

            $employee = $deletionRequest->employee;

            if ($employee) {
                // Optionally delete profile photo
                if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
                    Storage::disk('public')->delete($employee->profile_photo);
                }

                $employee->tokens()->delete();
                $employee->delete();
            }

            $deletionRequest->update(['status' => 'approved']);

            DB::commit();

            return back()->with('success', 'Account deletion request approved. The employee has been successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    public function reject(AccountDeletionRequest $deletionRequest)
    {
        if ($deletionRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $deletionRequest->update(['status' => 'rejected']);

        Notification::create([
            'employee_id' => $deletionRequest->employee_id,
            'title' => 'Account Deletion Rejected',
            'message' => 'Your request to delete your account has been rejected by HR.',
            'type' => 'account',
            'is_read' => false
        ]);

        return back()->with('success', 'Account deletion request rejected.');
    }
}
