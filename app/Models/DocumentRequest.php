<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequest extends Model
{
    protected $fillable = ['student_id','document_type','purpose','copies','status','remarks','processed_by','processed_at','released_at'];
    protected $casts = ['processed_at'=>'datetime','released_at'=>'datetime'];

    public function student(): BelongsTo { return $this->belongsTo(User::class,'student_id'); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class,'processed_by'); }

    public static array $types = [
        'cert_enrollment' => 'Certificate of Enrollment',
        'good_moral'      => 'Certificate of Good Moral Character',
        'form137'         => 'Form 137 (Permanent Record)',
        'transcript'      => 'Transcript of Records',
        'diploma'         => 'Diploma / Completion Certificate',
    ];

    public function getDocumentLabelAttribute(): string {
        return static::$types[$this->document_type] ?? ucfirst($this->document_type);
    }

    public function getStatusColorAttribute(): array {
        return match($this->status) {
            'processing' => ['#1d4ed8','#dbeafe'],
            'ready'      => ['#166534','#dcfce7'],
            'released'   => ['#475569','#f1f5f9'],
            'rejected'   => ['#991b1b','#fee2e2'],
            default      => ['#92400e','#fef3c7'],
        };
    }
}
