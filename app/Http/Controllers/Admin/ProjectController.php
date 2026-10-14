<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['client', 'employees'])->latest()->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::all();
        $employees = Employee::all();
        return view('admin.projects.create', compact('clients', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'program_tech_stack' => 'nullable|string|max:255',
            'full_details' => 'nullable|string',
            'frontend_section' => 'nullable|string',
            'backend_section' => 'nullable|string',
            'database' => 'nullable|string',
            'server_host_details' => 'nullable|string',
            'site_link' => 'nullable|string|max:255',
            'credentials_details' => 'nullable|string',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        $project = Project::create($validated);

        if ($request->has('employee_ids')) {
            $project->employees()->sync($request->employee_ids);

            $tokens = \App\Models\Employee::whereIn('id', $request->employee_ids)
                ->whereNotNull('push_token')
                ->pluck('push_token')
                ->toArray();
                
            if (!empty($tokens)) {
                \App\Services\PushService::send(
                    $tokens,
                    'New Project Assigned 🚀',
                    "You have been assigned to a new project: {$project->title}",
                    ['type' => 'project', 'project_id' => $project->id]
                );
            }
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['client', 'employees']);
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::all();
        $employees = Employee::all();
        $project->load('employees');
        return view('admin.projects.edit', compact('project', 'clients', 'employees'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'program_tech_stack' => 'nullable|string|max:255',
            'full_details' => 'nullable|string',
            'frontend_section' => 'nullable|string',
            'backend_section' => 'nullable|string',
            'database' => 'nullable|string',
            'server_host_details' => 'nullable|string',
            'site_link' => 'nullable|string|max:255',
            'credentials_details' => 'nullable|string',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        $project->update($validated);

        if ($request->has('employee_ids')) {
            $project->employees()->sync($request->employee_ids);
            
            $tokens = \App\Models\Employee::whereIn('id', $request->employee_ids)
                ->whereNotNull('push_token')
                ->pluck('push_token')
                ->toArray();
                
            if (!empty($tokens)) {
                \App\Services\PushService::send(
                    $tokens,
                    'Project Updated 🔄',
                    "There has been an update to your project: {$project->title}",
                    ['type' => 'project', 'project_id' => $project->id]
                );
            }
        } else {
            $project->employees()->sync([]);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
}
