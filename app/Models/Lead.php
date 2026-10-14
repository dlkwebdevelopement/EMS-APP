<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_code',
        'name',
        'mobile',
        'whatsapp',
        'email',
        'college_company',
        'course_department',
        'enquiry_type',
        'domain',
        'service_needed',
        'requirement',
        'message',
        'source',
        'source_page',
        'landing_page',
        'campaign_name',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'status',
        'priority',
        'is_duplicate',
        'duplicate_of_id',
        'assigned_to',
        'assigned_at',
        'tl_id',
        'assigned_to_tl_at',
        'sales_id',
        'last_contacted_at',
        'next_followup_date',
        'followup_status',
        'conversion_type',
        'payment_status',
        'deal_amount',
        'paid_amount',
        'balance_amount',
        'lost_reason',
        'conversion_notes',
        'converted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_duplicate' => 'boolean',
        'assigned_at' => 'datetime',
        'assigned_to_tl_at' => 'datetime',
        'last_contacted_at' => 'datetime',
        'next_followup_date' => 'datetime',
        'converted_at' => 'datetime',
        'deal_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    /**
     * Auto generate unique lead code (e.g. LEAD-2609-0001)
     */
    public static function generateLeadCode(): string
    {
        $prefix = 'LEAD-' . date('ym') . '-';
        $latest = self::withTrashed()
            ->where('lead_code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/-(\d+)$/', $latest->lead_code, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad((string)$number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function assignedStaff()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function duplicateOf()
    {
        return $this->belongsTo(Lead::class, 'duplicate_of_id');
    }

    public function duplicates()
    {
        return $this->hasMany(Lead::class, 'duplicate_of_id');
    }

    public function followups()
    {
        return $this->hasMany(LeadFollowup::class)->orderBy('created_at', 'desc');
    }

    public function teamLeader()
    {
        return $this->belongsTo(Employee::class, 'tl_id');
    }

    public function salesAgent()
    {
        return $this->belongsTo(Employee::class, 'sales_id');
    }

    public function leadWorkers()
    {
        return $this->hasMany(LeadWorker::class, 'lead_id')->orderBy('start_date', 'asc');
    }

    public function workers()
    {
        return $this->belongsToMany(Employee::class, 'lead_workers', 'lead_id', 'employee_id')
                    ->withPivot(['id', 'assigned_by_tl_id', 'start_date', 'end_date', 'status', 'instructions', 'submission_notes', 'submitted_at'])
                    ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopePriority($query, $priority)
    {
        if ($priority && $priority !== 'all') {
            return $query->where('priority', $priority);
        }
        return $query;
    }

    public function scopeEnquiryType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('enquiry_type', $type);
        }
        return $query;
    }

    public function scopeSource($query, $source)
    {
        if ($source && $source !== 'all') {
            return $query->where('source', $source);
        }
        return $query;
    }

    public function scopeAssignedTo($query, $staffId)
    {
        if ($staffId && $staffId !== 'all') {
            return $query->where('assigned_to', $staffId);
        }
        return $query;
    }

    public function scopeAssignedTl($query, $tlId)
    {
        if ($tlId && $tlId !== 'all') {
            return $query->where('tl_id', $tlId);
        }
        return $query;
    }

    public function scopeSales($query, $salesId)
    {
        if ($salesId && $salesId !== 'all') {
            return $query->where('sales_id', $salesId);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('lead_code', 'like', "%{$search}%")
                  ->orWhere('college_company', 'like', "%{$search}%")
                  ->orWhere('domain', 'like', "%{$search}%")
                  ->orWhere('course_department', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Badges & Helpers
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'new' => ['class' => 'bg-info text-white', 'label' => 'New Lead', 'icon' => 'fas fa-sparkles'],
            'contacted' => ['class' => 'bg-primary text-white', 'label' => 'Contacted', 'icon' => 'fas fa-phone'],
            'interested' => ['class' => 'bg-success text-white', 'label' => 'Interested', 'icon' => 'fas fa-thumbs-up'],
            'follow_up' => ['class' => 'bg-warning text-dark', 'label' => 'Follow-up', 'icon' => 'fas fa-calendar-alt'],
            'not_interested' => ['class' => 'bg-secondary text-white', 'label' => 'Not Interested', 'icon' => 'fas fa-user-slash'],
            'converted' => ['class' => 'bg-success text-white', 'label' => 'Converted', 'icon' => 'fas fa-check-circle'],
            'lost' => ['class' => 'bg-danger text-white', 'label' => 'Lost', 'icon' => 'fas fa-times-circle'],
            default => ['class' => 'bg-light text-dark', 'label' => ucfirst($this->status), 'icon' => 'fas fa-info-circle'],
        };
    }

    public function getPriorityBadgeAttribute(): array
    {
        return match ($this->priority) {
            'hot' => ['class' => 'bg-danger text-white', 'label' => 'Hot', 'icon' => 'fas fa-fire'],
            'warm' => ['class' => 'bg-warning text-dark', 'label' => 'Warm', 'icon' => 'fas fa-sun'],
            'cold' => ['class' => 'bg-info text-white', 'label' => 'Cold', 'icon' => 'fas fa-snowflake'],
            default => ['class' => 'bg-secondary text-white', 'label' => 'Normal', 'icon' => 'fas fa-circle'],
        };
    }

    public function getEnquiryTypeLabelAttribute(): string
    {
        return match ($this->enquiry_type) {
            'course_enquiry' => 'Course Enquiry',
            'internship_enquiry' => 'Internship Enquiry',
            'project_enquiry' => 'Project Enquiry',
            'service_enquiry' => 'Service Enquiry',
            'contact_form' => 'Contact Form',
            'callback_request' => 'Callback Request',
            'whatsapp_enquiry' => 'WhatsApp Enquiry',
            'landing_page' => 'Landing Page',
            'popup_form' => 'Popup Form',
            default => ucwords(str_replace('_', ' ', $this->enquiry_type)),
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'google_search' => 'Google Search',
            'google_ads' => 'Google Ads',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'direct' => 'Direct Visit',
            'referral' => 'Referral',
            'website_page' => 'Website Page',
            'whatsapp' => 'WhatsApp',
            'popup_form' => 'Popup Form',
            default => ucwords(str_replace('_', ' ', $this->source)),
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->next_followup_date && 
               $this->next_followup_date->isPast() && 
               in_array($this->status, ['new', 'contacted', 'interested', 'follow_up']);
    }

    public function getIsTodayFollowupAttribute(): bool
    {
        return $this->next_followup_date && 
               $this->next_followup_date->isToday() && 
               in_array($this->status, ['new', 'contacted', 'interested', 'follow_up']);
    }
}
