<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ComplaintReceivedNotification extends Notification
{
    public function __construct(
        public readonly string $studentName,
        public readonly string $subjectName,
        public readonly string $reasonExcerpt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'complaint_received',
            'title'   => 'Grade Complaint Received',
            'message' => "{$this->studentName} filed a complaint about {$this->subjectName}: \"{$this->reasonExcerpt}\"",
            'url'     => '/complaints/manage',
        ];
    }
}
