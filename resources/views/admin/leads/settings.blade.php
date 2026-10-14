@extends('layouts.admin')

@section('title', 'Lead Settings & Website Integration')
@section('page-title', 'Lead Settings & Form Integration')
@section('page-subtitle', 'Auto-routing rules, master catalogs, message templates & website embed code')

@section('content')
<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Lead System Configuration</h3>
            <span class="text-muted small">Configure auto-assignment, courses, WhatsApp templates and integrate external websites</span>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Leads
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Settings Navigation Tabs --}}
    <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: var(--r-md);">
        <div class="card-header bg-transparent p-3">
            <ul class="nav nav-pills card-header-pills" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" role="tab" class="nav-link active rounded-pill btn-sm" data-bs-toggle="pill" data-bs-target="#integrationTab" aria-controls="integrationTab" aria-selected="true">
                        <i class="fas fa-code me-1 text-primary"></i> Website Form Integration
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" role="tab" class="nav-link rounded-pill btn-sm" data-bs-toggle="pill" data-bs-target="#routingTab" aria-controls="routingTab" aria-selected="false">
                        <i class="fas fa-sitemap me-1 text-success"></i> Department Auto-Routing
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" role="tab" class="nav-link rounded-pill btn-sm" data-bs-toggle="pill" data-bs-target="#templatesTab" aria-controls="templatesTab" aria-selected="false">
                        <i class="fab fa-whatsapp me-1 text-success"></i> WhatsApp & Email Templates
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" role="tab" class="nav-link rounded-pill btn-sm" data-bs-toggle="pill" data-bs-target="#mastersTab" aria-controls="mastersTab" aria-selected="false">
                        <i class="fas fa-database me-1 text-warning"></i> Master Catalogs
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content">

                {{-- ══════════════════════════════════════
                   TAB 1: WEBSITE FORM INTEGRATION & TEST
                ══════════════════════════════════════ --}}
                <div class="tab-pane fade show active" id="integrationTab">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <h5 class="fw-bold text-dark mb-2"><i class="fas fa-plug text-primary me-2"></i>Connect Any Website to EMS</h5>
                            <p class="text-muted small">
                                Any website (React, Next.js, HTML, WordPress, landing pages) can send enquiries directly to this Admin Panel.
                                Leads are instantly captured, checked for duplicates, auto-routed to staff, and appear on your dashboard in real-time.
                            </p>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Public Capture Endpoint URL (POST)</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="endpointUrl" class="form-control font-monospace bg-light" value="{{ $apiEndpointUrl }}" readonly>
                                    <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $apiEndpointUrl }}'); alert('Endpoint URL copied!');">
                                        <i class="fas fa-copy me-1"></i> Copy URL
                                    </button>
                                </div>
                                <div class="form-text small">Also available via API: <code>{{ url('/api/v1/leads/capture') }}</code> (CORS enabled)</div>
                            </div>

                            {{-- React / Axios Code Snippet --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold text-dark"><i class="fab fa-react text-info me-1"></i> React / Axios Integration Snippet</span>
                                    <button class="btn btn-xs btn-outline-primary" onclick="navigator.clipboard.writeText(document.getElementById('reactSnippet').innerText); alert('React snippet copied!');">
                                        Copy Code
                                    </button>
                                </div>
                                <pre class="bg-dark text-white p-3 rounded small overflow-auto" id="reactSnippet" style="max-height: 220px;"><code>// Example for React contact / enquiry form
const handleSubmitLead = async (formData) => {
  try {
    const response = await fetch('{{ $apiEndpointUrl }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        name: formData.name,
        mobile: formData.mobile,
        whatsapp: formData.whatsapp || formData.mobile,
        email: formData.email,
        college_company: formData.college,
        course_department: formData.department,
        enquiry_type: 'course_enquiry', // 'course_enquiry', 'internship_enquiry', 'project_enquiry', 'service_enquiry'
        domain: formData.selectedDomain, // e.g. 'Python Full Stack & AI'
        requirement: formData.requirement,
        message: formData.message,
        source: 'website_page',
        source_page: window.location.pathname,
        utm_source: new URLSearchParams(window.location.search).get('utm_source'),
        utm_medium: new URLSearchParams(window.location.search).get('utm_medium'),
        utm_campaign: new URLSearchParams(window.location.search).get('utm_campaign')
      })
    });
    const result = await response.json();
    if (result.success) {
      alert('Thank you! Your enquiry has been registered: ' + result.lead_id);
    }
  } catch (error) {
    console.error('Lead submission failed', error);
  }
};</code></pre>
                            </div>

                            {{-- HTML Form Snippet --}}
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold text-dark"><i class="fab fa-html5 text-danger me-1"></i> Vanilla HTML Form Snippet</span>
                                    <button class="btn btn-xs btn-outline-primary" onclick="navigator.clipboard.writeText(document.getElementById('htmlSnippet').innerText); alert('HTML snippet copied!');">
                                        Copy Code
                                    </button>
                                </div>
                                <pre class="bg-dark text-white p-3 rounded small overflow-auto" id="htmlSnippet" style="max-height: 180px;"><code>&lt;form action="{{ $apiEndpointUrl }}" method="POST"&gt;
  &lt;input type="text" name="name" placeholder="Your Name" required /&gt;
  &lt;input type="tel" name="mobile" placeholder="Mobile Number" required /&gt;
  &lt;input type="email" name="email" placeholder="Email Address" /&gt;
  &lt;input type="hidden" name="enquiry_type" value="course_enquiry" /&gt;
  &lt;input type="hidden" name="source" value="landing_page" /&gt;
  &lt;textarea name="message" placeholder="Requirement"&gt;&lt;/textarea&gt;
  &lt;button type="submit"&gt;Send Enquiry&lt;/button&gt;
&lt;/form&gt;</code></pre>
                            </div>
                        </div>

                        {{-- In-Browser Live Test Form --}}
                        <div class="col-lg-5">
                            <div class="card border border-2 border-primary shadow-none p-4 rounded bg-light">
                                <h5 class="fw-bold text-primary mb-2"><i class="fas fa-vial me-2"></i>Live In-Browser Test Submission</h5>
                                <p class="small text-muted mb-3">Simulate an incoming website lead right now to test auto-capture, duplicate checks and routing!</p>

                                <form id="testCaptureForm" onsubmit="event.preventDefault(); submitTestLead();">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Visitor Name</label>
                                        <input type="text" id="test_name" class="form-control form-control-sm" value="Test Candidate" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Mobile</label>
                                        <input type="text" id="test_mobile" class="form-control form-control-sm" value="9876543210" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Email</label>
                                        <input type="email" id="test_email" class="form-control form-control-sm" value="test.visitor@example.com">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Enquiry Type</label>
                                        <select id="test_enquiry_type" class="form-select form-select-sm">
                                            <option value="course_enquiry">Training & Course Enquiry</option>
                                            <option value="internship_enquiry">Internship Enquiry</option>
                                            <option value="project_enquiry">IEEE Academic Project</option>
                                            <option value="service_enquiry">Software & Web Development</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Domain / Technology</label>
                                        <input type="text" id="test_domain" class="form-control form-control-sm" value="Python Full Stack & AI">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Requirement Message</label>
                                        <textarea id="test_message" rows="2" class="form-control form-control-sm">Looking for practical training with placement guarantee.</textarea>
                                    </div>
                                    <button type="submit" id="btnTestSubmit" class="btn btn-primary btn-sm w-100 rounded-pill py-2 shadow-sm">
                                        <i class="fas fa-paper-plane me-1"></i> Send Test Enquiry Now
                                    </button>
                                    <div id="testResult" class="mt-2 text-center" style="display:none;"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════
                   TAB 2: DEPARTMENT ROUTING RULES
                ══════════════════════════════════════ --}}
                <div class="tab-pane fade" id="routingTab">
                    <form action="{{ route('admin.leads.settings.update') }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Automated Team & Department Routing</h5>
                                <span class="text-muted small">Route incoming visitor leads to corresponding department staff automatically</span>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="auto_assignment_enabled" value="1" id="autoAssignSwitch" {{ $autoAssign ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold small" for="autoAssignSwitch">Enable Auto-Assignment</label>
                            </div>
                        </div>

                        <div class="card bg-light border-0 p-3 mb-4 rounded">
                            <p class="small text-muted mb-0">
                                <strong>How Routing Works:</strong> When a visitor submits an enquiry (e.g. <em>Course Enquiry</em>), the CRM inspects the target department (e.g. <em>Training</em>) and automatically round-robins the lead to staff in that team.
                            </p>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 250px;">Enquiry Type</th>
                                        <th>Target Department / Team</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $enquiryTypes = [
                                            'course_enquiry' => 'Course Enquiry',
                                            'internship_enquiry' => 'Internship Enquiry',
                                            'project_enquiry' => 'IEEE Project Enquiry',
                                            'service_enquiry' => 'Software / Web Development Service',
                                            'callback_request' => 'Callback Request',
                                            'whatsapp_enquiry' => 'WhatsApp Lead',
                                            'contact_form' => 'General Contact Form',
                                            'landing_page' => 'Landing Page Lead',
                                            'popup_form' => 'Popup Form Lead'
                                        ];
                                    @endphp
                                    @foreach($enquiryTypes as $eqKey => $eqTitle)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $eqTitle }}</td>
                                            <td>
                                                <input type="text" name="routing_rules[{{ $eqKey }}]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $routingRules[$eqKey] ?? 'Sales' }}" 
                                                       placeholder="e.g. Training, Projects, Software, HR...">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Save Routing Rules
                        </button>
                    </form>
                </div>

                {{-- ══════════════════════════════════════
                   TAB 3: WHATSAPP & EMAIL TEMPLATES
                ══════════════════════════════════════ --}}
                <div class="tab-pane fade" id="templatesTab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Pre-Composed Message Templates</h5>
                            <span class="text-muted small">Supports variables: <code>{name}</code>, <code>{course}</code>, <code>{lead_code}</code>, <code>{phone}</code>, <code>{college}</code></span>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#templateModal" onclick="resetTemplateModal()">
                            <i class="fas fa-plus me-1"></i> New Template
                        </button>
                    </div>

                    <div class="row g-3">
                        @forelse($templates as $tmpl)
                            <div class="col-md-6">
                                <div class="card border border-light shadow-sm h-100" style="border-radius: var(--r-sm);">
                                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <span class="badge {{ $tmpl->type === 'whatsapp' ? 'bg-success' : 'bg-primary' }} rounded-pill me-1">
                                                <i class="{{ $tmpl->type === 'whatsapp' ? 'fab fa-whatsapp' : 'fas fa-envelope' }} me-1"></i>
                                                {{ ucfirst($tmpl->type) }}
                                            </span>
                                            <strong class="text-dark small">{{ $tmpl->title }}</strong>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-light btn-xs text-primary" onclick="editTemplate({{ json_encode($tmpl) }})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.leads.templates.destroy', $tmpl) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete template?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-xs text-danger" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        <p class="small text-secondary mb-0" style="white-space: pre-wrap; font-size: 0.82rem;">{{ $tmpl->body }}</p>
                                    </div>
                                    <div class="card-footer bg-light py-1 px-3 d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size: 0.7rem;">Category: {{ $tmpl->category }}</span>
                                        <span class="badge bg-white text-muted border" style="font-size: 0.65rem;">Active</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">No message templates found.</div>
                        @endforelse
                    </div>
                </div>

                {{-- ══════════════════════════════════════
                   TAB 4: MASTER CATALOGS
                ══════════════════════════════════════ --}}
                <div class="tab-pane fade" id="mastersTab">
                    <form action="{{ route('admin.leads.settings.update') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Master Courses List (One per line)</label>
                                <textarea name="courses_list" rows="10" class="form-control form-control-sm font-monospace">{{ implode("\n", $courses) }}</textarea>
                                <div class="form-text small">Appears in enquiry dropdowns and registration forms.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Master Technology Domains (One per line)</label>
                                <textarea name="domains_list" rows="10" class="form-control form-control-sm font-monospace">{{ implode("\n", $domains) }}</textarea>
                                <div class="form-text small">Classifies candidate's area of technical interest.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Master IT & Software Services (One per line)</label>
                                <textarea name="services_list" rows="10" class="form-control form-control-sm font-monospace">{{ implode("\n", $services) }}</textarea>
                                <div class="form-text small">Corporate & software development enquiry options.</div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-save me-1"></i> Update Master Catalogs
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Template Modal --}}
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="tmplModalTitle"><i class="fab fa-whatsapp me-2"></i> Message Template</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.leads.templates.store') }}" method="POST">
                @csrf
                <input type="hidden" name="template_id" id="tmpl_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Template Title</label>
                        <input type="text" name="title" id="tmpl_title" class="form-control form-control-sm" required placeholder="e.g. Course Details - Full Stack">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Channel</label>
                            <select name="type" id="tmpl_type" class="form-select form-select-sm" required>
                                <option value="whatsapp">WhatsApp Message</option>
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" id="tmpl_category" class="form-select form-select-sm" required>
                                <option value="course_details">Course Details</option>
                                <option value="fee_details">Fee Structure & Payment</option>
                                <option value="location_sharing">Location Sharing</option>
                                <option value="payment_link">Payment Link Sharing</option>
                                <option value="general">General Follow-up</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subject (for Email)</label>
                        <input type="text" name="subject" id="tmpl_subject" class="form-control form-control-sm" placeholder="Optional for email">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Message Content</label>
                        <textarea name="body" id="tmpl_body" rows="6" class="form-control form-control-sm" required placeholder="Type your message with {name}, {course}, {lead_code}..."></textarea>
                    </div>
                    <div class="form-text small">Tags: <code>{name}</code>, <code>{course}</code>, <code>{phone}</code>, <code>{lead_code}</code>, <code>{college}</code></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function resetTemplateModal() {
        document.getElementById('tmpl_id').value = '';
        document.getElementById('tmpl_title').value = '';
        document.getElementById('tmpl_subject').value = '';
        document.getElementById('tmpl_body').value = '';
        document.getElementById('tmplModalTitle').innerHTML = '<i class="fab fa-whatsapp me-2"></i> New Message Template';
    }

    function editTemplate(tmpl) {
        document.getElementById('tmpl_id').value = tmpl.id;
        document.getElementById('tmpl_title').value = tmpl.title;
        document.getElementById('tmpl_type').value = tmpl.type;
        document.getElementById('tmpl_category').value = tmpl.category;
        document.getElementById('tmpl_subject').value = tmpl.subject || '';
        document.getElementById('tmpl_body').value = tmpl.body;
        document.getElementById('tmplModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i> Edit Message Template';
        new bootstrap.Modal(document.getElementById('templateModal')).show();
    }

    async function submitTestLead() {
        const btn = document.getElementById('btnTestSubmit');
        const resDiv = document.getElementById('testResult');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';
        resDiv.style.display = 'none';

        try {
            const payload = {
                name: document.getElementById('test_name').value,
                mobile: document.getElementById('test_mobile').value,
                email: document.getElementById('test_email').value,
                enquiry_type: document.getElementById('test_enquiry_type').value,
                domain: document.getElementById('test_domain').value,
                message: document.getElementById('test_message').value,
                source: 'website_page',
                source_page: '/test-simulator',
            };

            const resp = await fetch('{{ $apiEndpointUrl }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await resp.json();
            resDiv.style.display = 'block';

            if (data.success) {
                resDiv.innerHTML = `<div class="alert alert-success py-2 small mb-0"><i class="fas fa-check-circle me-1"></i> Success! Created Lead ID: <strong>${data.lead_id}</strong>. <a href="{{ route('admin.leads.index') }}" class="alert-link">View in Leads &rarr;</a></div>`;
            } else {
                resDiv.innerHTML = `<div class="alert alert-danger py-2 small mb-0"><i class="fas fa-times-circle me-1"></i> Error: ${data.message || 'Submission failed'}</div>`;
            }
        } catch (e) {
            resDiv.style.display = 'block';
            resDiv.innerHTML = `<div class="alert alert-danger py-2 small mb-0">Network error: ${e.message}</div>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Test Enquiry Now';
        }
    }
</script>
@endpush
