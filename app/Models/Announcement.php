<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'message',
        'priority',
        'target_audience',
        'created_by',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function scopeForRole($query, string $roleLabel)
    {
        return $query->where(function ($q) use ($roleLabel) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', strtolower($roleLabel));
        });
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function getAudienceLabelAttribute(): string
    {
        return match($this->target_audience) {
            'all'       => 'Everyone',
            'student'   => 'Students',
            'faculty'   => 'Faculty',
            'registrar' => 'Registrars',
            default     => ucfirst($this->target_audience),
        };
    }
}
