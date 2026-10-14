@extends('layouts.admin')

@section('title', 'Edit Lead - ' . $lead->lead_code)
@section('page-title', 'Edit Lead: ' . $lead->lead_code)
@section('page-subtitle', 'Update lead details, candidate enquiry, and Team Leader assignment')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px; max-width: 900px; margin: 0 auto;">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-edit text-success me-2"></i> Edit Lead: {{ $lead->lead_code }}
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i> 360° View
                </a>
                <a href="{{ route('sales.leads.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sales.leads.update', $lead) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Candidate Contact Info --}}
                <div class="text-success fw-bold small text-uppercase mb-3">
                    <i class="fas fa-user me-1"></i> 1. Candidate / Client Information
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $lead->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Mobile Phone <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $lead->mobile) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">WhatsApp Number</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $lead->whatsapp) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">College / Company Name</label>
                        <input type="text" name="college_company" class="form-control" value="{{ old('college_company', $lead->college_company) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Course / Department</label>
                        <input type="text" name="course_department" class="form-control" value="{{ old('course_department', $lead->course_department) }}">
                    </div>
                </div>

                {{-- Requirement & Domain --}}
                <div class="text-success fw-bold small text-uppercase mb-3">
                    <i class="fas fa-laptop-code me-1"></i> 2. Requirement & Domain Details
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Enquiry Type <span class="text-danger">*</span></label>
                        <select name="enquiry_type" class="form-select" required>
                            <option value="course_enquiry" {{ old('enquiry_type', $lead->enquiry_type) === 'course_enquiry' ? 'selected' : '' }}>Course Enquiry</option>
                            <option value="project_enquiry" {{ old('enquiry_type', $lead->enquiry_type) === 'project_enquiry' ? 'selected' : '' }}>Final Year Project Enquiry</option>
                            <option value="internship_enquiry" {{ old('enquiry_type', $lead->enquiry_type) === 'internship_enquiry' ? 'selected' : '' }}>Internship Enquiry</option>
                            <option value="service_enquiry" {{ old('enquiry_type', $lead->enquiry_type) === 'service_enquiry' ? 'selected' : '' }}>Software & Web Development Service</option>
                            <option value="callback_request" {{ old('enquiry_type', $lead->enquiry_type) === 'callback_request' ? 'selected' : '' }}>Callback Request</option>
                            <option value="whatsapp_enquiry" {{ old('enquiry_type', $lead->enquiry_type) === 'whatsapp_enquiry' ? 'selected' : '' }}>WhatsApp Enquiry</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Technology / Domain</label>
                        <input type="text" name="domain" class="form-control" value="{{ old('domain', $lead->domain) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Service Needed</label>
                        <input type="text" name="service_needed" class="form-control" value="{{ old('service_needed', $lead->service_needed) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Lead Source <span class="text-danger">*</span></label>
                        <select name="source" class="form-select" required>
                            <option value="direct" {{ old('source', $lead->source) === 'direct' ? 'selected' : '' }}>Direct Walk-in / Phone</option>
                            <option value="google_search" {{ old('source', $lead->source) === 'google_search' ? 'selected' : '' }}>Google Search</option>
                            <option value="website_page" {{ old('source', $lead->source) === 'website_page' ? 'selected' : '' }}>Website Landing Page</option>
                            <option value="whatsapp" {{ old('source', $lead->source) === 'whatsapp' ? 'selected' : '' }}>WhatsApp Inbound</option>
                            <option value="referral" {{ old('source', $lead->source) === 'referral' ? 'selected' : '' }}>Referral / Student</option>
                            <option value="social_media" {{ old('source', $lead->source) === 'social_media' ? 'selected' : '' }}>Social Media</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Client Project Requirement / Notes</label>
                        <textarea name="requirement" class="form-control" rows="3">{{ old('requirement', $lead->requirement) }}</textarea>
                    </div>
                </div>

                {{-- Status, Priority & TL Assignment --}}
                <div class="text-success fw-bold small text-uppercase mb-3">
                    <i class="fas fa-crown me-1"></i> 3. Team Leader Handover & Financial Deal
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Lead Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="new" {{ old('status', $lead->status) === 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ old('status', $lead->status) === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="interested" {{ old('status', $lead->status) === 'interested' ? 'selected' : '' }}>Interested</option>
                            <option value="follow_up" {{ old('status', $lead->status) === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                            <option value="converted" {{ old('status', $lead->status) === 'converted' ? 'selected' : '' }}>Converted</option>
                            <option value="lost" {{ old('status', $lead->status) === 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Lead Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            <option value="warm" {{ old('priority', $lead->priority) === 'warm' ? 'selected' : '' }}>☀️ Warm</option>
                            <option value="hot" {{ old('priority', $lead->priority) === 'hot' ? 'selected' : '' }}>🔥 Hot (Urgent)</option>
                            <option value="cold" {{ old('priority', $lead->priority) === 'cold' ? 'selected' : '' }}>❄️ Cold</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Deal Value (₹)</label>
                        <input type="number" step="0.01" name="deal_amount" class="form-control" value="{{ old('deal_amount', $lead->deal_amount) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-primary">
                            <i class="fas fa-crown me-1"></i> Team Leader (TL) Assignment
                        </label>
                        <select name="tl_id" class="form-select border-primary">
                            <option value="">-- No Team Leader Assigned --</option>
                            @foreach($teamLeaders as $tl)
                                <option value="{{ $tl->id }}" {{ old('tl_id', $lead->tl_id) == $tl->id ? 'selected' : '' }}>
                                    ⭐ {{ $tl->name }} ({{ $tl->department }} &bull; {{ $tl->designation }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Next Follow-up Date</label>
                        <input type="datetime-local" name="next_followup_date" class="form-control" 
                               value="{{ old('next_followup_date', optional($lead->next_followup_date)->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Conversion / Handover Notes</label>
                        <textarea name="conversion_notes" class="form-control" rows="2">{{ old('conversion_notes', $lead->conversion_notes) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('sales.leads.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn text-white fw-bold px-4 shadow-sm" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); color: #ffffff !important; border-radius: 8px; border: none; padding: 10px 24px;">
                        <i class="fas fa-save me-1 text-white" style="color: #ffffff !important;"></i>
                        <span class="text-white" style="color: #ffffff !important;">Update Lead Details</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
