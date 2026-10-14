<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimeSheetController extends Controller
{
    /**
     * Display a listing of personal timesheets.
     */
    public function index(Request $request)
    {
        $employee = Auth::user();
        
        $timesheets = TimeSheet::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $timesheets
        ]);
    }

    /**
     * Store a newly created timesheet in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hours' => 'required|numeric|min:0.5|max:24',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = Auth::user();
        
        $timesheet = TimeSheet::create([
            'employee_id' => $employee->id,
            'date' => $request->date,
            'task_name' => $request->task_name,
            'description' => $request->description,
            'hours' => $request->hours,
            'status' => 'pending' // Default status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'TimeSheet logged successfully',
            'data' => $timesheet
        ], 201);
    }
}
