<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Leads Table
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_code', 50)->unique();
            
            // Lead Details
            $table->string('name');
            $table->string('mobile', 30)->index();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email')->nullable()->index();
            $table->string('college_company')->nullable();
            $table->string('course_department')->nullable();
            $table->string('enquiry_type', 50)->default('general')->index(); 
            // e.g. course_enquiry, internship_enquiry, project_enquiry, service_enquiry, contact_form, callback_request, whatsapp_enquiry, landing_page, popup_form, general
            $table->string('domain')->nullable(); // e.g. Python, AI/ML, Full Stack, Embedded, Digital Marketing, etc.
            $table->string('service_needed')->nullable(); // e.g. Software Services, Website/App Dev, SEO, etc.
            $table->text('requirement')->nullable();
            $table->text('message')->nullable();
            
            // Source & Attribution Tracking
            $table->string('source', 50)->default('direct')->index(); 
            // e.g. google_search, google_ads, facebook, instagram, youtube, direct, referral, website_page, whatsapp, popup_form, other
            $table->string('source_page')->nullable();
            $table->string('landing_page')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            
            // Lead Management Status & Priority
            $table->enum('status', ['new', 'contacted', 'interested', 'follow_up', 'not_interested', 'converted', 'lost'])->default('new')->index();
            $table->enum('priority', ['hot', 'warm', 'cold'])->default('warm')->index();
            
            // Duplicate Lead Detection
            $table->boolean('is_duplicate')->default(false)->index();
            $table->foreignId('duplicate_of_id')->nullable()->constrained('leads')->nullOnDelete();
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();
            
            // Follow-up Tracking
            $table->dateTime('last_contacted_at')->nullable();
            $table->dateTime('next_followup_date')->nullable()->index();
            $table->string('followup_status', 30)->nullable(); // pending, completed, missed, overdue
            
            // Conversion & Payment Tracking
            $table->string('conversion_type', 50)->nullable(); // training_registration, internship_registration, project_registration, service_customer, other
            $table->enum('payment_status', ['none', 'advance_paid', 'full_paid', 'balance_payment'])->default('none')->index();
            $table->decimal('deal_amount', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('balance_amount', 12, 2)->default(0.00);
            $table->text('lost_reason')->nullable();
            $table->text('conversion_notes')->nullable();
            $table->dateTime('converted_at')->nullable();
            
            // Tech info
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Lead Follow-ups & Activities Table
        Schema::create('lead_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('employees')->nullOnDelete(); // Assigned staff/caller
            $table->enum('followup_type', ['call', 'whatsapp', 'email', 'meeting', 'note'])->default('call');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'missed', 'cancelled'])->default('pending')->index();
            $table->string('outcome')->nullable(); // e.g. 'Answered - Interested', 'Call Back Later', 'Wrong Number', etc.
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Lead Message Templates Table (WhatsApp, Email, SMS)
        Schema::create('lead_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['whatsapp', 'email', 'sms'])->default('whatsapp');
            $table->string('category', 50)->default('general'); // course_details, fee_details, location_sharing, payment_link, general
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Lead Settings / Configuration Table
        Schema::create('lead_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_followups');
        Schema::dropIfExists('lead_templates');
        Schema::dropIfExists('lead_settings');
        Schema::dropIfExists('leads');
    }
};
