<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadTemplate;
use App\Models\LeadSetting;
use App\Models\Employee;
use Carbon\Carbon;

class LeadManagementSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Message Templates
        $templates = [
            [
                'title' => 'Course Details & Syllabus',
                'type' => 'whatsapp',
                'category' => 'course_details',
                'subject' => 'Course Curriculum - DLK Technologies',
                'body' => "Hi {name}! 👋 Thank you for your enquiry regarding {course} at DLK Technologies.\n\nWe provide 100% Practical Hands-on Training with Real-Time Projects and Placement Assistance.\n\nKey Highlights:\n✅ Live Industry Projects\n✅ 1-on-1 Mentor Guidance\n✅ Certificate & Resume Building\n✅ Flexible Batch Timings\n\nWould you like to schedule a free demo session today or tomorrow?\n\nBest regards,\nDLK Technologies Team",
            ],
            [
                'title' => 'Fee Structure & Payment Link',
                'type' => 'whatsapp',
                'category' => 'fee_details',
                'subject' => 'Official Fee Structure & Registration Link',
                'body' => "Hello {name}, as discussed, here is the fee summary for the {course} program:\n\n📌 Total Program Fee: ₹15,000\n🎁 Early-Bird Discount: ₹3,000 off (Valid this week)\n👉 Payable Amount: ₹12,000 (Installment facility available)\n\nYou can secure your seat with an advance registration of ₹2,000:\n💳 Payment Link: https://rzp.io/l/dlk-training-reg\n\nPlease share the screenshot once done to activate your student portal!\n\nWarm regards,\nDLK Admissions Desk",
            ],
            [
                'title' => 'Office Location Sharing',
                'type' => 'whatsapp',
                'category' => 'location_sharing',
                'subject' => 'DLK Technologies Office Location & Directions',
                'body' => "Dear {name}, here are our office details for your visit:\n\n🏢 DLK Technologies Private Limited\n📍 No. 25, 2nd Floor, Anna Nagar Main Road, Chennai - 600040\n⏱️ Visiting Hours: Monday to Saturday, 9:30 AM - 6:30 PM\n🗺️ Google Maps: https://maps.google.com/?q=DLK+Technologies+Chennai\n\nContact: +91 98409 00000 / +91 98409 11111\nLooking forward to meeting you!",
            ],
            [
                'title' => 'Callback Request Acknowledgment',
                'type' => 'whatsapp',
                'category' => 'callback_acknowledgment',
                'subject' => 'We received your callback request',
                'body' => "Hello {name}, we received your callback request regarding {course}!\n\nOur senior academic advisor will call you within 15-30 minutes. If you have an immediate question, you can reply directly to this WhatsApp message.\n\nThank you for choosing DLK Technologies!",
            ],
            [
                'title' => 'Official Course Brochure Email',
                'type' => 'email',
                'category' => 'course_details',
                'subject' => 'Welcome to DLK Technologies - Detailed Course Brochure & Modules',
                'body' => "Dear {name},\n\nThank you for expressing interest in the {course} at DLK Technologies.\n\nAttached is the comprehensive syllabus and module breakdown. Our training programs are meticulously crafted to bridge the gap between academic theory and high-demand corporate skills.\n\nWe look forward to speaking with you soon to discuss your career goals and tailor the roadmap for you.\n\nWarm Regards,\nDLK Technologies Academic Council\nEmail: contact@dlktechnologies.com\nWeb: https://dlktechnologies.com",
            ]
        ];

        foreach ($templates as $tmpl) {
            LeadTemplate::updateOrCreate(
                ['title' => $tmpl['title']],
                $tmpl
            );
        }

        // 2. Settings & Catalogs
        LeadSetting::set('lead_sources', [
            'google_search' => 'Google Organic Search',
            'google_ads' => 'Google Ads / SEM',
            'facebook' => 'Facebook Campaign',
            'instagram' => 'Instagram Ad / Reels',
            'youtube' => 'YouTube Channel',
            'direct' => 'Direct Website Visit',
            'referral' => 'Friend / Student Referral',
            'website_page' => 'Website Content Page',
            'whatsapp' => 'WhatsApp Direct Click',
            'popup_form' => 'Popup Lead Form',
            'other' => 'Other Media'
        ], 'Allowed Lead Sources');

        LeadSetting::set('courses_list', [
            'Python Full Stack & AI',
            'Data Science & Machine Learning',
            'Java Spring Boot & Microservices',
            'MERN / Full Stack Web Development',
            'Embedded Systems & IoT',
            'Cyber Security & Ethical Hacking',
            'Flutter & Android Mobile Apps',
            'Cloud Computing & DevOps (AWS/Azure)',
            'Digital Marketing & SEO Mastery'
        ], 'Master Courses Catalog');

        LeadSetting::set('domains_list', [
            'Artificial Intelligence & ML',
            'Web Development',
            'Embedded Systems & IoT',
            'Java Technologies',
            'Python Technologies',
            'Mobile Application Development',
            'Cloud & DevOps',
            'VLSI Design',
            'Robotics & Automation',
            'Digital Marketing',
            'Software Quality Assurance'
        ], 'Master Domains');

        LeadSetting::set('services_list', [
            'Software Development',
            'Website & Web App Development',
            'Mobile App Development (iOS/Android)',
            'Digital Marketing & Lead Generation',
            'IEEE Final Year Academic Projects',
            'Corporate Corporate Upskilling',
            'UI/UX Design & Branding'
        ], 'Master Services');

        LeadSetting::set('routing_rules', [
            'course_enquiry' => 'Training',
            'internship_enquiry' => 'Internship',
            'project_enquiry' => 'Projects',
            'service_enquiry' => 'Software Services',
            'callback_request' => 'Support',
            'whatsapp_enquiry' => 'Sales',
            'contact_form' => 'General',
            'landing_page' => 'Marketing',
            'popup_form' => 'Marketing'
        ], 'Automated Enquiry Type to Department Routing Rules');

        LeadSetting::set('auto_assignment_enabled', true, 'Enable automatic lead assignment to staff upon capture');

        // 3. Sample Leads (Only if leads table is empty)
        if (Lead::count() === 0) {
            $staffList = Employee::take(5)->pluck('id')->toArray();
            $defaultStaff = !empty($staffList) ? $staffList[0] : null;

            $leadsData = [
                [
                    'lead_code' => 'LEAD-' . date('ym') . '-0001',
                    'name' => 'Kavitha Ramasamy',
                    'mobile' => '9840123456',
                    'whatsapp' => '9840123456',
                    'email' => 'kavitha.r@gmail.com',
                    'college_company' => 'SRM Institute of Science and Technology',
                    'course_department' => 'B.Tech CSE, 3rd Year',
                    'enquiry_type' => 'course_enquiry',
                    'domain' => 'Python Full Stack & AI',
                    'service_needed' => null,
                    'requirement' => 'Looking for weekend batch with placement assistance.',
                    'message' => 'Hi, what is the duration and fee for Python AI full stack course?',
                    'source' => 'google_search',
                    'source_page' => '/courses/python-full-stack',
                    'landing_page' => '/courses',
                    'campaign_name' => 'SEO_Chennai_Python',
                    'utm_source' => 'google',
                    'utm_medium' => 'organic',
                    'status' => 'new',
                    'priority' => 'hot',
                    'assigned_to' => $defaultStaff,
                    'assigned_at' => Carbon::now()->subHours(2),
                    'next_followup_date' => Carbon::now()->addHours(2),
                    'deal_amount' => 15000.00,
                    'payment_status' => 'none',
                    'created_at' => Carbon::now()->subHours(2),
                ],
                [
                    'lead_code' => 'LEAD-' . date('ym') . '-0002',
                    'name' => 'Arun Kumar',
                    'mobile' => '9789123890',
                    'whatsapp' => '9789123890',
                    'email' => 'arun.k.tech@gmail.com',
                    'college_company' => 'SSN College of Engineering',
                    'course_department' => 'B.E ECE, Final Year',
                    'enquiry_type' => 'project_enquiry',
                    'domain' => 'Embedded Systems & IoT',
                    'service_needed' => 'IEEE Final Year Academic Projects',
                    'requirement' => 'IEEE 2026 Smart Healthcare Monitoring with ESP32 and MQTT cloud.',
                    'message' => 'Need hardware kit and complete documentation support.',
                    'source' => 'google_ads',
                    'source_page' => '/ieee-projects/embedded-iot',
                    'landing_page' => '/landing/ieee-projects',
                    'campaign_name' => 'Google_Ads_IEEE_Chennai',
                    'utm_source' => 'google',
                    'utm_medium' => 'cpc',
                    'utm_campaign' => 'ieee_2026_tech',
                    'status' => 'contacted',
                    'priority' => 'hot',
                    'assigned_to' => $defaultStaff,
                    'assigned_at' => Carbon::now()->subDays(1),
                    'last_contacted_at' => Carbon::now()->subHours(5),
                    'next_followup_date' => Carbon::now()->addDay(),
                    'deal_amount' => 12000.00,
                    'paid_amount' => 2000.00,
                    'balance_amount' => 10000.00,
                    'payment_status' => 'advance_paid',
                    'conversion_type' => 'project_registration',
                    'created_at' => Carbon::now()->subDays(1),
                ],
                [
                    'lead_code' => 'LEAD-' . date('ym') . '-0003',
                    'name' => 'Priya Dharshini',
                    'mobile' => '9444567890',
                    'whatsapp' => '9444567890',
                    'email' => 'priya.dharshini@outlook.com',
                    'college_company' => 'Sathyabama University',
                    'course_department' => 'MCA, 1st Year',
                    'enquiry_type' => 'internship_enquiry',
                    'domain' => 'MERN / Full Stack Web Development',
                    'service_needed' => null,
                    'requirement' => '1-month winter internship with live React & Node project.',
                    'message' => 'Does this internship provide certificate and letter of recommendation?',
                    'source' => 'instagram',
                    'source_page' => '/internships',
                    'landing_page' => '/landing/internship-offer',
                    'campaign_name' => 'Insta_Reels_Intern_Feb',
                    'utm_source' => 'instagram',
                    'utm_medium' => 'social_paid',
                    'status' => 'interested',
                    'priority' => 'warm',
                    'assigned_to' => $defaultStaff,
                    'assigned_at' => Carbon::now()->subDays(2),
                    'last_contacted_at' => Carbon::now()->subHours(18),
                    'next_followup_date' => Carbon::today()->setHour(16)->setMinute(0),
                    'deal_amount' => 6000.00,
                    'payment_status' => 'none',
                    'created_at' => Carbon::now()->subDays(2),
                ],
                [
                    'lead_code' => 'LEAD-' . date('ym') . '-0004',
                    'name' => 'Suresh Babu',
                    'mobile' => '9884019283',
                    'whatsapp' => '9884019283',
                    'email' => 'suresh@apexlogistics.in',
                    'college_company' => 'Apex Logistics Solutions',
                    'course_department' => 'Operations Director',
                    'enquiry_type' => 'service_enquiry',
                    'domain' => 'Software Development',
                    'service_needed' => 'Website & Web App Development',
                    'requirement' => 'Custom fleet tracking and billing dashboard for logistics.',
                    'message' => 'We need an estimate and timeline for a 5-module logistics management portal.',
                    'source' => 'direct',
                    'source_page' => '/services/custom-software',
                    'landing_page' => '/',
                    'status' => 'follow_up',
                    'priority' => 'hot',
                    'assigned_to' => $defaultStaff,
                    'assigned_at' => Carbon::now()->subDays(3),
                    'last_contacted_at' => Carbon::now()->subDays(1),
                    'next_followup_date' => Carbon::now()->subHours(2), // Overdue
                    'deal_amount' => 75000.00,
                    'payment_status' => 'none',
                    'created_at' => Carbon::now()->subDays(3),
                ],
                [
                    'lead_code' => 'LEAD-' . date('ym') . '-0005',
                    'name' => 'Dinesh Karthik',
                    'mobile' => '9962345678',
                    'whatsapp' => '9962345678',
                    'email' => 'dineshkarthik99@gmail.com',
                    'college_company' => 'St. Josephs College of Engineering',
                    'course_department' => 'B.Tech IT',
                    'enquiry_type' => 'course_enquiry',
                    'domain' => 'Java Spring Boot & Microservices',
                    'service_needed' => null,
                    'requirement' => 'Job guarantee batch starting next Monday.',
                    'message' => 'Registered and paid full fees.',
                    'source' => 'youtube',
                    'source_page' => '/courses/java-spring-boot',
                    'status' => 'converted',
                    'priority' => 'hot',
                    'assigned_to' => $defaultStaff,
                    'assigned_at' => Carbon::now()->subDays(5),
                    'last_contacted_at' => Carbon::now()->subDays(2),
                    'converted_at' => Carbon::now()->subDays(2),
                    'conversion_type' => 'training_registration',
                    'deal_amount' => 18000.00,
                    'paid_amount' => 18000.00,
                    'balance_amount' => 0.00,
                    'payment_status' => 'full_paid',
                    'conversion_notes' => 'Enrolled in March Batch A. Student ID: DLK-2026-TR-108',
                    'created_at' => Carbon::now()->subDays(5),
                ],
                [
                    'lead_code' => 'LEAD-' . date('ym') . '-0006',
                    'name' => 'Meena Sundaram',
                    'mobile' => '9710987654',
                    'whatsapp' => '9710987654',
                    'email' => 'meena.s@yahoo.com',
                    'college_company' => 'Loyola College',
                    'course_department' => 'B.Sc Computer Science',
                    'enquiry_type' => 'callback_request',
                    'domain' => 'Data Science & Machine Learning',
                    'service_needed' => null,
                    'requirement' => 'Requested callback regarding morning batch.',
                    'message' => 'Please call back between 4 PM and 6 PM.',
                    'source' => 'whatsapp',
                    'source_page' => '/',
                    'status' => 'new',
                    'priority' => 'warm',
                    'assigned_to' => $defaultStaff,
                    'assigned_at' => Carbon::now()->subHours(1),
                    'next_followup_date' => Carbon::today()->setHour(17)->setMinute(0),
                    'payment_status' => 'none',
                    'created_at' => Carbon::now()->subHours(1),
                ],
                [
                    'lead_code' => 'LEAD-' . date('ym') . '-0007',
                    'name' => 'Vigneshwaran M',
                    'mobile' => '9841876543',
                    'whatsapp' => '9841876543',
                    'email' => 'vignesh.m@gmail.com',
                    'college_company' => 'Hindustan University',
                    'course_department' => 'B.E Mech',
                    'enquiry_type' => 'course_enquiry',
                    'domain' => 'Digital Marketing & SEO Mastery',
                    'requirement' => 'Fee too high, decided to study online tutorials.',
                    'message' => 'Looking for very cheap course under 3000.',
                    'source' => 'facebook',
                    'status' => 'lost',
                    'priority' => 'cold',
                    'lost_reason' => 'Budget constraint - Looking for free / low-cost tutorials.',
                    'assigned_to' => $defaultStaff,
                    'created_at' => Carbon::now()->subDays(6),
                ]
            ];

            foreach ($leadsData as $data) {
                $lead = Lead::create($data);

                // Add sample follow-ups for active leads
                if (in_array($lead->status, ['contacted', 'interested', 'follow_up', 'converted'])) {
                    LeadFollowup::create([
                        'lead_id' => $lead->id,
                        'user_id' => $lead->assigned_to,
                        'followup_type' => 'call',
                        'scheduled_at' => Carbon::now()->subDays(1),
                        'completed_at' => Carbon::now()->subDays(1),
                        'status' => 'completed',
                        'outcome' => 'Answered - Explained curriculum',
                        'notes' => 'Spoke with ' . $lead->name . '. Explained program structure and weekend schedule. Candidate expressed high interest.',
                    ]);

                    LeadFollowup::create([
                        'lead_id' => $lead->id,
                        'user_id' => $lead->assigned_to,
                        'followup_type' => 'whatsapp',
                        'scheduled_at' => Carbon::now()->subHours(6),
                        'completed_at' => Carbon::now()->subHours(6),
                        'status' => 'completed',
                        'outcome' => 'Sent Course Syllabus & Fee link',
                        'notes' => 'Shared detailed PDF brochure and fee breakdown on WhatsApp.',
                    ]);
                }
            }
        }
    }
}
