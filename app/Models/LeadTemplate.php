<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'category',
        'subject',
        'body',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Replace template tags with lead data
     */
    public function renderForLead(Lead $lead): string
    {
        $replacements = [
            '{name}' => $lead->name,
            '{lead_code}' => $lead->lead_code,
            '{course}' => $lead->domain ?? $lead->course_department ?? 'Course / Internship',
            '{domain}' => $lead->domain ?? 'Program',
            '{phone}' => $lead->mobile,
            '{email}' => $lead->email ?? '',
            '{college}' => $lead->college_company ?? 'College',
            '{company}' => 'DLK Technologies',
            '{website}' => config('app.url'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->body);
    }
}
