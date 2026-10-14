<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayNotification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'type',
        'date',
        'year',
        'is_sent',
        'sent_at',
        'message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    /**
     * Get the employee that owns the notification.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope a query to only include birthday notifications.
     */
    public function scopeBirthdays($query)
    {
        return $query->where('type', 'birthday');
    }

    /**
     * Scope a query to only include anniversary notifications.
     */
    public function scopeAnniversaries($query)
    {
        return $query->where('type', 'anniversary');
    }

    /**
     * Scope a query to only include notifications for a specific year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }
}