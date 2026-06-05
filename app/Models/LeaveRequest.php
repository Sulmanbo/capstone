<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = ['faculty_id','leave_type','start_date','end_date','days_count','reason','status','admin_remarks','reviewed_by','reviewed_at'];
    protected $casts = ['start_date'=>'date','end_date'=>'date','reviewed_at'=>'datetime'];

    public function faculty(): BelongsTo { return $this->belongsTo(User::class,'faculty_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class,'reviewed_by'); }

    public static array $types = [
        'sick'        => 'Sick Leave',
        'vacation'    => 'Vacation Leave',
        'emergency'   => 'Emergency Leave',
        'maternity'   => 'Maternity Leave',
        'paternity'   => 'Paternity Leave',
        'bereavement' => 'Bereavement Leave',
    ];

    public function getTypeLabelAttribute(): string {
        return static::$types[$this->leave_type] ?? ucfirst($this->leave_type);
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
}
