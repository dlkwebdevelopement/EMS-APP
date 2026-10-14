@extends('layouts.admin')

@section('title', 'Add New Lead')
@section('page-title', 'Create Website Lead')
@section('page-subtitle', 'Manually register a prospect enquiry or inbound walk-in/call')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
                <i class="fas fa-arrow-left me-1"></i> Back to All Leads
            </a>
            <h3 class="fw-bold text-dark mb-0">New Lead Registration</h3>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <strong>Please fix the errors below:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.leads.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- Personal & Contact Information --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: var(--r-md);">
                    <div class="card-header bg-transparent d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-user-circle text-white"></i></span>
                        <span class="fw-bold">Contact & Candidate Information</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Candidate / Client Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name') }}" placeholder="e.g. Ramya Krishnan" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Mobile Phone <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" class="form-control form-control-sm" value="{{ old('mobile') }}" placeholder="10-digit mobile number" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">WhatsApp Number</label>
                                <input type="text" name="whatsapp" class="form-control form-control-sm" value="{{ old('whatsapp') }}" placeholder="Leave blank to use mobile number">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}" placeholder="candidate@example.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">College / Company Name</label>
                                <input type="text" name="college_company" class="form-control form-control-sm" value="{{ old('college_company') }}" placeholder="e.g. Anna University / Cognizant">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Degree / Department</label>
                                <input type="text" name="course_department" class="form-control form-control-sm" value="{{ old('course_department') }}" placeholder="e.g. B.E CSE 2026 Batch">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Requirement & Details --}}
                <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md);">
                    <div class="card-header bg-transparent d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-graduation-cap text-white"></i></span>
                        <span class="fw-bold">Enquiry & Requirement Specifications</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Enquiry Category <span class="text-danger">*</span></label>
                                <select name="enquiry_type" class="form-select form-select-sm" required>
                                    <option value="course_enquiry" {{ old('enquiry_type') === 'course_enquiry' ? 'selected' : '' }}>Course Enquiry</option>
                                    <option value="internship_enquiry" {{ old('enquiry_type') === 'internship_enquiry' ? 'selected' : '' }}>Internship Enquiry</option>
                                    <option value="project_enquiry" {{ old('enquiry_type') === 'project_enquiry' ? 'selected' : '' }}>IEEE / Final Year Project</option>
                                    <option value="service_enquiry" {{ old('enquiry_type') === 'service_enquiry' ? 'selected' : '' }}>Software / IT Service</option>
                                    <option value="callback_request" {{ old('enquiry_type') === 'callback_request' ? 'selected' : '' }}>Callback Request</option>
                                    <option value="whatsapp_enquiry" {{ old('enquiry_type') === 'whatsapp_enquiry' ? 'selected' : '' }}>WhatsApp Inbound</option>
                                    <option value="contact_form" {{ old('enquiry_type') === 'contact_form' ? 'selected' : '' }}>Contact Form</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Domain / Technology</label>
                                <select name="domain" class="form-select form-select-sm">
                                    <option value="">-- Select Technology Domain --</option>
                                    @foreach($domains as $dom)
                                        <option value="{{ $dom }}" {{ old('domain') === $dom ? 'selected' : '' }}>{{ $dom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Services Needed (If applicable)</label>
                                <select name="service_needed" class="form-select form-select-sm">
                                    <option value="">-- None / Academic Course --</option>
                                    @foreach($services as $srv)
                                        <option value="{{ $srv }}" {{ old('service_needed') === $srv ? 'selected' : '' }}>{{ $srv }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Specific Requirement</label>
                                <textarea name="requirement" rows="2" class="form-control form-control-sm" placeholder="e.g. Looking for batch starting next month, weekend classes...">{{ old('requirement') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Message</label>
                                <textarea name="message" rows="2" class="form-control form-control-sm" placeholder="Candidate's message or initial notes...">{{ old('message') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lead Attribution & Pipeline Settings --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: var(--r-md);">
                    <div class="card-header bg-transparent d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-sliders-h text-white"></i></span>
                        <span class="fw-bold">Lead Management & Assignment</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Source Channel <span class="text-danger">*</span></label>
                            <select name="source" class="form-select form-select-sm" required>
                                @foreach($sources as $sKey => $sLabel)
                                    <option value="{{ $sKey }}" {{ old('source') === $sKey ? 'selected' : '' }}>{{ $sLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Source Web Page / URL</label>
                            <input type="text" name="source_page" class="form-control form-control-sm" value="{{ old('source_page', '/manual-entry') }}" placeholder="/courses/python">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Priority / Temperature <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select form-select-sm" required>
                                    <option value="hot" {{ old('priority') === 'hot' ? 'selected' : '' }}>🔥 Hot Lead</option>
                                    <option value="warm" {{ old('priority', 'warm') === 'warm' ? 'selected' : '' }}>☀️ Warm Lead</option>
                                    <option value="cold" {{ old('priority') === 'cold' ? 'selected' : '' }}>❄️ Cold Lead</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="new" {{ old('status', 'new') === 'new' ? 'selected' : '' }}>New Lead</option>
                                    <option value="contacted" {{ old('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="interested" {{ old('status') === 'interested' ? 'selected' : '' }}>Interested</option>
                                    <option value="follow_up" {{ old('status') === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                    <option value="not_interested" {{ old('status') === 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Assign to Staff</label>
                            <select name="assigned_to" class="form-select form-select-sm">
                                <option value="">-- Unassigned (Auto-Assign Later) --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('assigned_to') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }} ({{ $emp->department }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Next Follow-up Date & Time</label>
                            <input type="datetime-local" name="next_followup_date" class="form-control form-control-sm" value="{{ old('next_followup_date') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Estimated Deal Value (₹)</label>
                            <input type="number" step="0.01" name="deal_amount" class="form-control form-control-sm" value="{{ old('deal_amount', '0.00') }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                            <i class="fas fa-check-circle me-1"></i> Save & Create Lead
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>
@endsection
