<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $employee = Auth::guard('sanctum')->user();
        if (!$employee) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Return only non-sensitive data
        $projects = $employee->projects()->with('client:id,name')->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'client_name' => $project->client ? $project->client->name : 'N/A',
                'program_tech_stack' => $project->program_tech_stack,
                'full_details' => $project->full_details,
                'frontend_section' => $project->frontend_section,
                'backend_section' => $project->backend_section,
                'database' => $project->database,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $projects
        ]);
    }
}
