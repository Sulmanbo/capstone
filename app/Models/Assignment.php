<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = ['section_subject_id','created_by','title','instructions','type','max_score','due_date','allow_late','is_published'];
    protected $casts = ['due_date'=>'datetime','allow_late'=>'boolean','is_published'=>'boolean','max_score'=>'float'];

    public function sectionSubject(): BelongsTo { return $this->belongsTo(SectionSubject::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class,'created_by'); }
    public function submissions(): HasMany { return $this->hasMany(AssignmentSubmission::class); }

    public function submissionFor(int $studentId): ?AssignmentSubmission {
        return $this->submissions->firstWhere('student_id', $studentId);
    }

    public function isOverdue(): bool {
        return $this->due_date && now()->isAfter($this->due_date);
    }
}
