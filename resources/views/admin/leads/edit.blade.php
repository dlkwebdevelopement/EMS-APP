@extends('layouts.admin')

@section('title', 'Edit Lead - ' . $lead->lead_code)
@section('page-title', 'Edit Lead')
@section('page-subtitle', 'Update contact details, requirement and attribution tracking')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Lead {{ $lead->lead_code }}
            </a>
            <h3 class="fw-bold text-dark mb-0">Edit Lead #{{ $lead->lead_code }}</h3>
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

    <form action="{{ route('admin.leads.update', $lead) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Contact Information --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: var(--r-md);">
                    <div class="card-header bg-transparent d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-user-edit text-white"></i></span>
                        <span class="fw-bold">Contact & Candidate Information</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Candidate / Client Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $lead->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Mobile Phone <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" class="form-control form-control-sm" value="{{ old('mobile', $lead->mobile) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">WhatsApp Number</label>
                                <input type="text" name="whatsapp" class="form-control form-control-sm" value="{{ old('whatsapp', $lead->whatsapp) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email', $lead->email) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">College / Company Name</label>
                                <input type="text" name="college_company" class="form-control form-control-sm" value="{{ old('college_company', $lead->college_company) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Degree / Department</label>
                                <input type="text" name="course_department" class="form-control form-control-sm" value="{{ old('course_department', $lead->course_department) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Requirement & Details --}}
                <div class="card border-0 shadow-sm bg-white" style="border-radius: var(--r-md);">
                    <div class="card-header bg-transparent d-flex align-items-center gap-2">
                        <span class="header-icon"><i class="fas fa-file-alt text-white"></i></span>
                        <span class="fw-bold">Enquiry & Requirement Specifications</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Enquiry Category <span class="text-danger">*</span></label>
                                <select name="enquiry_type" class="form-select form-select-sm" required>
                                    @foreach(['course_enquiry' => 'Course Enquiry', 'internship_enquiry' => 'Internship Enquiry', 'project_enquiry' => 'IEEE Project', 'service_enquiry' => 'Software Service', 'callback_request' => 'Callback Request', 'whatsapp_enquiry' => 'WhatsApp Inbound', 'contact_form' => 'Contact Form'] as $eqKey => $eqLabel)
                                        <option value="{{ $eqKey }}" {{ old('enquiry_type', $lead->enquiry_type) === $eqKey ? 'selected' : '' }}>{{ $eqLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Domain / Technology</label>
                                <select name="domain" class="form-select form-select-sm">
                                    <option value="">-- Select Technology Domain --</option>
                                    @foreach($domains as $dom)
                                        <option value="{{ $dom }}" {{ old('domain', $lead->domain) === $dom ? 'selected' : '' }}>{{ $dom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Services Needed</label>
                                <select name="service_needed" class="form-select form-select-sm">
                                    <option value="">-- None / Academic Course --</option>
                                    @foreach($services as $srv)
                                        <option value="{{ $srv }}" {{ old('service_needed', $lead->service_needed) === $srv ? 'selected' : '' }}>{{ $srv }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Specific Requirement</label>
                                <textarea name="requirement" rows="2" class="form-control form-control-sm">{{ old('requirement', $lead->requirement) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Message</label>
                                <textarea name="message" rows="2" class="form-control form-control-sm">{{ old('message', $lead->message) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attribution & Lead Settings --}}
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
                                    <option value="{{ $sKey }}" {{ old('source', $lead->source) === $sKey ? 'selected' : '' }}>{{ $sLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select form-select-sm" required>
                                    <option value="hot" {{ old('priority', $lead->priority) === 'hot' ? 'selected' : '' }}>🔥 Hot Lead</option>
                                    <option value="warm" {{ old('priority', $lead->priority) === 'warm' ? 'selected' : '' }}>☀️ Warm Lead</option>
                                    <option value="cold" {{ old('priority', $lead->priority) === 'cold' ? 'selected' : '' }}>❄️ Cold Lead</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm" required>
                                    @foreach(['new' => 'New Lead', 'contacted' => 'Contacted', 'interested' => 'Interested', 'follow_up' => 'Follow-up', 'not_interested' => 'Not Interested', 'converted' => 'Converted', 'lost' => 'Lost'] as $stKey => $stVal)
                                        <option value="{{ $stKey }}" {{ old('status', $lead->status) === $stKey ? 'selected' : '' }}>{{ $stVal }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Assign to Staff</label>
                            <select name="assigned_to" class="form-select form-select-sm">
                                <option value="">-- Unassigned --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('assigned_to', $lead->assigned_to) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }} ({{ $emp->department }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Next Follow-up Date & Time</label>
                            <input type="datetime-local" name="next_followup_date" class="form-control form-control-sm" 
                                   value="{{ old('next_followup_date', $lead->next_followup_date ? $lead->next_followup_date->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Deal Value (₹)</label>
                            <input type="number" step="0.01" name="deal_amount" class="form-control form-control-sm" value="{{ old('deal_amount', $lead->deal_amount) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Lost Reason (If applicable)</label>
                            <input type="text" name="lost_reason" class="form-control form-control-sm" value="{{ old('lost_reason', $lead->lost_reason) }}" placeholder="e.g. Budget issue, distance, timing...">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> Update Lead Details
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>
@endsection
