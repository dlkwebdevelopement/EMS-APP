<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeEducation;
use App\Models\EmployeeExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileDetailController extends Controller
{
    // MULTI-EXPERIENCE API
    public function indexExperiences(Request $request)
    {
        try {
            $employee = $request->user();
            $experiences = EmployeeExperience::where('employee_id', $employee->id)
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'experiences' => $experiences
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'experiences' => []]);
        }
    }

    public function storeExperience(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current_job' => 'nullable|boolean',
            'job_description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $employee = $request->user();
        $employee->update(['is_fresher' => false]);

        $exp = EmployeeExperience::create([
            'employee_id' => $employee->id,
            'company_name' => $request->company_name,
            'designation' => $request->designation,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_current_job' => $request->boolean('is_current_job'),
            'job_description' => $request->job_description,
            'location' => $request->location,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Experience added successfully!',
            'experience' => $exp
        ], 201);
    }

    public function destroyExperience(Request $request, $id)
    {
        $employee = $request->user();
        $exp = EmployeeExperience::where('employee_id', $employee->id)->where('id', $id)->first();

        if (!$exp) {
            return response()->json(['success' => false, 'message' => 'Experience record not found.'], 404);
        }

        $exp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Experience record deleted.'
        ]);
    }

    // MULTI-EDUCATION API
    public function indexEducations(Request $request)
    {
        try {
            $employee = $request->user();
            $educations = EmployeeEducation::where('employee_id', $employee->id)
                ->orderBy('passout_year', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'educations' => $educations
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'educations' => []]);
        }
    }

    public function storeEducation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|string|max:100',
            'degree_title' => 'required|string|max:255',
            'institution_name' => 'required|string|max:255',
            'board_or_university' => 'nullable|string|max:255',
            'passout_year' => 'nullable|string|max:10',
            'score_or_grade' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $employee = $request->user();
        $edu = EmployeeEducation::create([
            'employee_id' => $employee->id,
            'level' => $request->level,
            'degree_title' => $request->degree_title,
            'institution_name' => $request->institution_name,
            'board_or_university' => $request->board_or_university,
            'passout_year' => $request->passout_year,
            'score_or_grade' => $request->score_or_grade,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Education entry added successfully!',
            'education' => $edu
        ], 201);
    }

    public function destroyEducation(Request $request, $id)
    {
        $employee = $request->user();
        $edu = EmployeeEducation::where('employee_id', $employee->id)->where('id', $id)->first();

        if (!$edu) {
            return response()->json(['success' => false, 'message' => 'Education record not found.'], 404);
        }

        $edu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Education record deleted.'
        ]);
    }

    public function destroyAccount(Request $request)
    {
        $employee = $request->user();
        
        // Revoke all tokens
        $employee->tokens()->delete();
        
        // Optionally delete profile photo from storage if needed
        if ($employee->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->profile_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->profile_photo);
        }

        // Hard delete the employee (cascade deletes attendance, leaves, etc.)
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Your account has been permanently deleted.'
        ]);
    }

    public function requestAccountDeletion(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $employee = $request->user();

        // Check if a pending request already exists
        $existingRequest = \App\Models\AccountDeletionRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending account deletion request.'
            ], 422);
        }

        \App\Models\AccountDeletionRequest::create([
            'employee_id' => $employee->id,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your account deletion request has been submitted to HR.'
        ]);
    }
}
