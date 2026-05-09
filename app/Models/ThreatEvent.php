<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ThreatEvent extends Model
{
    const UPDATED_AT = null;

    protected $table = 'threat_events';

    protected $fillable = [
        'user_id',
        'threat_type',
        'severity',
        'status',
        'event_label',
        'description',
        'source_ip',
        'target_route',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'created_at'  => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Quick helper to log a threat event from anywhere in the app.
     */
    public static function record(
        string  $type,
        string  $severity,
        string  $label,
        string  $description,
        ?int    $userId      = null,
        ?string $targetRoute = null
    ): void {
        try {
            static::create([
                'user_id'      => $userId ?? auth()->id(),
                'threat_type'  => $type,
                'severity'     => $severity,
                'status'       => 'active',
                'event_label'  => $label,
                'description'  => $description,
                'source_ip'    => Request::ip(),
                'target_route' => $targetRoute ?? Request::path(),
            ]);
        } catch (\Exception $e) {
            \Log::error('ThreatEvent::record failed: ' . $e->getMessage());
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
