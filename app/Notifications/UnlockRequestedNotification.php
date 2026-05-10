<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class UnlockRequestedNotification extends Notification
{
    public function __construct(
        public readonly string $subjectName,
        public readonly string $sectionName,
        public readonly string $facultyName,
        public readonly string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $excerpt = mb_substr($this->reason, 0, 80) . (mb_strlen($this->reason) > 80 ? '…' : '');

        return [
            'type'    => 'unlock_requested',
            'title'   => 'Grade Unlock Requested',
            'message' => "{$this->facultyName} requested a grade unlock for {$this->subjectName} ({$this->sectionName}): \"{$excerpt}\"",
            'url'     => '/registrar/grade-lock',
        ];
    }
}
