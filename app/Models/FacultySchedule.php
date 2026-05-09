<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultySchedule extends Model
{
    protected $fillable = [
        'faculty_id',
        'subject_name',
        'section',
        'room',
        'days',
        'start_time',
        'end_time',
        'academic_year_id',
        'created_by',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getDaysLabelAttribute(): string
    {
        $map = [
            'monday'    => 'Mon',
            'tuesday'   => 'Tue',
            'wednesday' => 'Wed',
            'thursday'  => 'Thu',
            'friday'    => 'Fri',
            'saturday'  => 'Sat',
        ];

        return implode(', ', array_map(fn($d) => $map[$d] ?? ucfirst($d), $this->days ?? []));
    }

    public function getTimeRangeAttribute(): string
    {
        return date('g:i A', strtotime($this->start_time))
             . ' – '
             . date('g:i A', strtotime($this->end_time));
    }
}
