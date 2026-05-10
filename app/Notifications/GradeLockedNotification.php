<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class GradeLockedNotification extends Notification
{
    public function __construct(
        public readonly string $subjectName,
        public readonly string $sectionName,
        public readonly string $quarterLabel,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'grade_locked',
            'title'   => 'Grades Locked',
            'message' => "Your {$this->subjectName} grades for {$this->quarterLabel} have been locked and are now official.",
            'url'     => '/student/report-card',
        ];
    }
}
