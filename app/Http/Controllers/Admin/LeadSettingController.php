<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadSetting;
use App\Models\LeadTemplate;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class LeadSettingController extends Controller
{
    /**
     * Settings, Templates & Integration View
     */
    public function index()
    {
        $sources = LeadSetting::get('lead_sources', []);
        $courses = LeadSetting::get('courses_list', []);
        $domains = LeadSetting::get('domains_list', []);
        $services = LeadSetting::get('services_list', []);
        $routingRules = LeadSetting::get('routing_rules', []);
        $autoAssign = LeadSetting::get('auto_assignment_enabled', true);

        $templates = LeadTemplate::orderBy('category')->get();
        $departments = Department::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();

        $apiEndpointUrl = url('/leads/capture');

        return view('admin.leads.settings', compact(
            'sources',
            'courses',
            'domains',
            'services',
            'routingRules',
            'autoAssign',
            'templates',
            'departments',
            'employees',
            'apiEndpointUrl'
        ));
    }

    /**
     * Update Routing & Master Settings
     */
    public function update(Request $request)
    {
        // 1. Auto Assignment Toggle
        LeadSetting::set('auto_assignment_enabled', $request->boolean('auto_assignment_enabled'));

        // 2. Department Routing Rules
        if ($request->has('routing_rules')) {
            LeadSetting::set('routing_rules', $request->routing_rules);
        }

        // 3. Courses List
        if ($request->has('courses_list')) {
            $courses = array_filter(array_map('trim', explode("\n", $request->courses_list)));
            LeadSetting::set('courses_list', array_values($courses));
        }

        // 4. Domains List
        if ($request->has('domains_list')) {
            $domains = array_filter(array_map('trim', explode("\n", $request->domains_list)));
            LeadSetting::set('domains_list', array_values($domains));
        }

        // 5. Services List
        if ($request->has('services_list')) {
            $services = array_filter(array_map('trim', explode("\n", $request->services_list)));
            LeadSetting::set('services_list', array_values($services));
        }

        return redirect()->back()->with('success', 'Lead management settings saved successfully.');
    }

    /**
     * Store or Update Message Template
     */
    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:whatsapp,email,sms',
            'category' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->filled('template_id')) {
            $template = LeadTemplate::findOrFail($request->template_id);
            $template->update($validated);
            $msg = 'Message template updated successfully.';
        } else {
            LeadTemplate::create($validated);
            $msg = 'Message template created successfully.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Delete Template
     */
    public function deleteTemplate(LeadTemplate $template)
    {
        $template->delete();
        return redirect()->back()->with('success', 'Message template deleted.');
    }
}
