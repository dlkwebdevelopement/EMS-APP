<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeadWorker extends Model
{
    use HasFactory;

    protected $table = 'lead_workers';

    protected $fillable = [
        'lead_id',
        'employee_id',
        'assigned_by_tl_id',
        'start_date',
        'end_date',
        'status',
        'instructions',
        'submission_notes',
        'submitted_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function worker()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Employee::class, 'assigned_by_tl_id');
    }

    /**
     * Helpers & Badges
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date && 
               $this->end_date->endOfDay()->isPast() && 
               !in_array($this->status, ['completed', 'submitted']);
    }

    public function getDaysLeftAttribute(): ?int
    {
        if (!$this->end_date) return null;
        return (int) round(now()->startOfDay()->diffInDays($this->end_date->startOfDay(), false));
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->is_overdue) {
            return [
                'class' => 'bg-danger text-white',
                'label' => 'Overdue Deadline',
                'icon' => 'fas fa-exclamation-triangle'
            ];
        }

        return match ($this->status) {
            'assigned' => [
                'class' => 'bg-info text-white',
                'label' => 'Assigned',
                'icon' => 'fas fa-user-clock'
            ],
            'in_progress' => [
                'class' => 'bg-primary text-white',
                'label' => 'In Progress',
                'icon' => 'fas fa-spinner fa-spin'
            ],
            'submitted' => [
                'class' => 'bg-warning text-dark',
                'label' => 'Submitted / In Review',
                'icon' => 'fas fa-file-export'
            ],
            'completed' => [
                'class' => 'bg-success text-white',
                'label' => 'Completed',
                'icon' => 'fas fa-check-circle'
            ],
            'overdue' => [
                'class' => 'bg-danger text-white',
                'label' => 'Overdue',
                'icon' => 'fas fa-clock'
            ],
            default => [
                'class' => 'bg-secondary text-white',
                'label' => ucfirst($this->status),
                'icon' => 'fas fa-circle'
            ],
        };
    }
}
