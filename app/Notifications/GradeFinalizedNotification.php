<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradeFinalizedNotification extends Notification
{
    public function __construct(
        public readonly string $subjectName,
        public readonly string $sectionName,
        public readonly string $quarterLabel,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'grade_finalized',
            'title'   => 'Grades Finalized',
            'message' => "Your {$this->subjectName} grades for {$this->quarterLabel} have been finalized.",
            'url'     => '/student/report-card',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Grades Have Been Finalized — EncryptEd')
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("Your grades for **{$this->subjectName}** ({$this->quarterLabel}) in {$this->sectionName} have been finalized.")
            ->action('View Report Card', url('/student/report-card'))
            ->line('If you have concerns about your grade, you may file a grade complaint through the portal.');
    }
}
