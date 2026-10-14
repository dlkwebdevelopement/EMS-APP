<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'followup_type',
        'scheduled_at',
        'completed_at',
        'status',
        'outcome',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function staff()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    public function getTypeBadgeAttribute(): array
    {
        return match ($this->followup_type) {
            'call' => ['class' => 'bg-primary text-white', 'icon' => 'fas fa-phone-alt', 'label' => 'Phone Call'],
            'whatsapp' => ['class' => 'bg-success text-white', 'icon' => 'fab fa-whatsapp', 'label' => 'WhatsApp'],
            'email' => ['class' => 'bg-danger text-white', 'icon' => 'fas fa-envelope', 'label' => 'Email'],
            'meeting' => ['class' => 'bg-purple text-white', 'icon' => 'fas fa-handshake', 'label' => 'In-Person / Demo'],
            default => ['class' => 'bg-secondary text-white', 'icon' => 'fas fa-sticky-note', 'label' => 'Internal Note'],
        };
    }
}
