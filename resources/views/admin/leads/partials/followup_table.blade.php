<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th style="min-width: 170px;">Lead Details</th>
                <th>Enquiry & Domain</th>
                <th>Assigned Staff</th>
                <th>Scheduled Follow-up</th>
                <th>Status</th>
                <th class="text-end pe-4" style="min-width: 130px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leadsList as $lead)
                <tr>
                    <td>
                        <a href="{{ route('admin.leads.show', $lead) }}" class="fw-bold text-dark text-decoration-none d-block">
                            {{ $lead->name }}
                        </a>
                        <span class="badge badge-soft-neutral font-monospace mt-1" style="font-size: 0.65rem;">{{ $lead->lead_code }}</span>
                        <div class="d-flex align-items-center gap-1.5 mt-1.5 flex-wrap">
                            <a href="tel:{{ $lead->mobile }}" class="badge badge-soft-primary text-decoration-none" title="Call {{ $lead->mobile }}">
                                <i class="fas fa-phone-alt me-1"></i>{{ $lead->mobile }}
                            </a>
                            @php $cleanPhone = preg_replace('/[^0-9]/','', $lead->whatsapp ?: $lead->mobile); @endphp
                            <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="badge badge-soft-success text-decoration-none" title="WhatsApp Message">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-soft-teal rounded-pill">{{ $lead->enquiry_type_label }}</span>
                        <div class="small fw-bold text-dark mt-1">{{ $lead->domain ?: 'General' }}</div>
                        @if($lead->college_company)
                            <div class="text-muted small" style="font-size: 0.72rem;">{{ Str::limit($lead->college_company, 22) }}</div>
                        @endif
                    </td>
                    <td>
                        @if($lead->assignedStaff)
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width: 26px; height: 26px; font-size: 0.68rem; background: linear-gradient(135deg, #1e7c42, #0d3b1a);">
                                    {{ strtoupper(substr($lead->assignedStaff->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">{{ $lead->assignedStaff->name }}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">{{ $lead->assignedStaff->department }}</div>
                                </div>
                            </div>
                        @else
                            <span class="badge badge-soft-neutral">
                                <i class="fas fa-user-clock me-1 text-muted"></i>Unassigned
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($lead->next_followup_date)
                            @if($lead->is_overdue)
                                <span class="badge badge-soft-danger rounded-pill fw-bold">
                                    <i class="fas fa-circle-exclamation me-1"></i>Overdue: {{ $lead->next_followup_date->diffForHumans() }}
                                </span>
                            @elseif($lead->is_today_followup)
                                <span class="badge badge-soft-warning rounded-pill fw-bold">
                                    <i class="far fa-clock me-1"></i>Today {{ $lead->next_followup_date->format('h:i A') }}
                                </span>
                            @else
                                <span class="badge badge-soft-neutral">
                                    <i class="far fa-calendar me-1 text-muted"></i>{{ $lead->next_followup_date->format('d M, h:i A') }}
                                </span>
                            @endif
                        @else
                            <span class="text-muted small">Not set</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeStyles = [
                                'new' => 'badge-soft-info',
                                'contacted' => 'badge-soft-primary',
                                'follow_up' => 'badge-soft-warning',
                                'interested' => 'badge-soft-success',
                                'converted' => 'badge-soft-success',
                                'lost' => 'badge-soft-danger',
                            ];
                            $badgeClass = $badgeStyles[$lead->status] ?? 'badge-soft-neutral';
                        @endphp
                        <span class="badge {{ $badgeClass }} rounded-pill">{{ $lead->status_label }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-action-group justify-content-end">
                            <a href="tel:{{ $lead->mobile }}" class="btn-action btn-action-call" title="Call Now">
                                <i class="fas fa-phone-alt"></i>
                            </a>
                            <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="btn-action btn-action-whatsapp" title="WhatsApp Message">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-xs btn-primary rounded-pill px-2.5 shadow-sm d-inline-flex align-items-center gap-1" title="View & Log Notes">
                                <i class="fas fa-clipboard-check"></i>
                                <span>Attend</span>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                            <i class="fas fa-check-circle fs-4 text-success"></i>
                        </div>
                        <p class="mb-0 fw-semibold text-dark">{{ $emptyMsg }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
