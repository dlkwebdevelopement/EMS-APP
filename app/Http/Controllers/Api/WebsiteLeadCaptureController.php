<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadSetting;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class WebsiteLeadCaptureController extends Controller
{
    /**
     * Public Lead Capture Webhook / API
     */
    public function capture(Request $request)
    {
        // Handle Preflight OPTIONS
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'college_company' => 'nullable|string|max:255',
            'course_department' => 'nullable|string|max:255',
            'enquiry_type' => 'nullable|string|max:50',
            'domain' => 'nullable|string|max:255',
            'service_needed' => 'nullable|string|max:255',
            'requirement' => 'nullable|string',
            'message' => 'nullable|string',
            'source' => 'nullable|string|max:50',
            'source_page' => 'nullable|string|max:255',
            'landing_page' => 'nullable|string|max:255',
            'campaign_name' => 'nullable|string|max:255',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
            'utm_term' => 'nullable|string|max:100',
            'utm_content' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422)->header('Access-Control-Allow-Origin', '*');
        }

        $data = $validator->validated();

        // Standardize phone
        $cleanPhone = preg_replace('/[^0-9]/', '', $data['mobile']);
        if (empty($data['whatsapp'])) {
            $data['whatsapp'] = $data['mobile'];
        }

        $data['enquiry_type'] = $data['enquiry_type'] ?? 'contact_form';
        $data['source'] = $data['source'] ?? 'website_page';
        $data['status'] = 'new';
        $data['priority'] = 'warm';
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();
        $data['lead_code'] = Lead::generateLeadCode();

        // 1. Duplicate Lead Detection (Matching phone or email within last 30 days)
        $existingLead = Lead::where(function ($q) use ($cleanPhone, $data) {
            $q->where('mobile', 'like', "%{$cleanPhone}%");
            if (!empty($data['email'])) {
                $q->orWhere('email', $data['email']);
            }
        })->where('created_at', '>=', Carbon::now()->subDays(30))->first();

        if ($existingLead) {
            $data['is_duplicate'] = true;
            $data['duplicate_of_id'] = $existingLead->id;
            $data['priority'] = 'hot'; // Repeated enquiry indicates high interest
        }

        // 2. Intelligent Auto-Assignment
        $isAutoAssign = LeadSetting::get('auto_assignment_enabled', true);
        if ($isAutoAssign) {
            $routingRules = LeadSetting::get('routing_rules', []);
            $targetDept = $routingRules[$data['enquiry_type']] ?? null;

            $assignedEmployee = null;
            $employees = Employee::all();

            if ($targetDept && $employees->isNotEmpty()) {
                $deptStaff = $employees->filter(function ($emp) use ($targetDept) {
                    return stripos($emp->department, $targetDept) !== false;
                });

                if ($deptStaff->isNotEmpty()) {
                    $assignedEmployee = $deptStaff->random();
                }
            }

            // Fallback: Assign to staff with least active leads or random
            if (!$assignedEmployee && $employees->isNotEmpty()) {
                $assignedEmployee = $employees->random();
            }

            if ($assignedEmployee) {
                $data['assigned_to'] = $assignedEmployee->id;
                $data['assigned_at'] = Carbon::now();
            }
        }

        // Set Next Follow-up Deadline (Default: Within 2 hours for fresh leads)
        $data['next_followup_date'] = Carbon::now()->addHours(2);
        $data['followup_status'] = 'pending';

        $lead = Lead::create($data);

        // Record Activity Log
        LeadFollowup::create([
            'lead_id' => $lead->id,
            'user_id' => $lead->assigned_to,
            'followup_type' => 'note',
            'completed_at' => Carbon::now(),
            'status' => 'completed',
            'outcome' => 'Captured from Website Form',
            'notes' => "New website enquiry received via {$lead->source_label} (" . ($lead->source_page ?: 'Home / Landing') . ")." . 
                       ($existingLead ? " [Duplicate Lead: Original #{$existingLead->lead_code}]" : "") .
                       ($lead->assignedStaff ? " [Auto-assigned to {$lead->assignedStaff->name}]" : ""),
        ]);

        // If duplicate, append note to parent lead as well
        if ($existingLead) {
            LeadFollowup::create([
                'lead_id' => $existingLead->id,
                'user_id' => $existingLead->assigned_to,
                'followup_type' => 'note',
                'completed_at' => Carbon::now(),
                'status' => 'completed',
                'outcome' => 'Repeat Enquiry Submitted',
                'notes' => "User submitted another enquiry from {$lead->source_page}. Created duplicate record #{$lead->lead_code}.",
            ]);
        }

        // If request is from standard browser HTML form post (redirect back or to thank you)
        if (!$request->expectsJson() && !$request->is('api/*')) {
            return redirect()->back()->with('success', 'Thank you! Your enquiry has been received. Our team will contact you shortly.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your enquiry has been submitted successfully.',
            'lead_id' => $lead->lead_code,
            'assigned' => $lead->assignedStaff ? true : false,
        ], 201)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
    }
}
