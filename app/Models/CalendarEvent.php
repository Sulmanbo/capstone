<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    protected $fillable = ['title','description','start_date','end_date','type','color','all_day','audience','created_by'];
    protected $casts = ['start_date'=>'date','end_date'=>'date','all_day'=>'boolean'];

    public function creator(): BelongsTo { return $this->belongsTo(User::class,'created_by'); }

    public function getTypeColorAttribute(): string {
        return match($this->type) {
            'holiday'    => '#ef4444',
            'exam'       => '#f59e0b',
            'no_classes' => '#8b5cf6',
            default      => '#3b82f6',
        };
    }

    public function getTypeLabelAttribute(): string {
        return match($this->type) {
            'holiday'    => 'Holiday',
            'exam'       => 'Exam / Assessment',
            'no_classes' => 'No Classes',
            default      => 'Event',
        };
    }
}
