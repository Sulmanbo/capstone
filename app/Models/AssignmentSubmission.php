<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $fillable = ['assignment_id','student_id','content','file_path','file_name','score','feedback','status','submitted_at','graded_at','graded_by'];
    protected $casts = ['submitted_at'=>'datetime','graded_at'=>'datetime','score'=>'float'];

    public function assignment(): BelongsTo { return $this->belongsTo(Assignment::class); }
    public function student(): BelongsTo { return $this->belongsTo(User::class,'student_id'); }
    public function grader(): BelongsTo { return $this->belongsTo(User::class,'graded_by'); }

    public function isGraded(): bool { return !is_null($this->score); }
    public function getPercentageAttribute(): ?float {
        if (!$this->score || !$this->assignment?->max_score) return null;
        return round(($this->score / $this->assignment->max_score) * 100, 1);
    }
}
