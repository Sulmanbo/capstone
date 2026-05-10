<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintRespondedNotification extends Notification
{
    public function __construct(
        public readonly string $subjectName,
        public readonly string $status,       // resolved | dismissed | under_review
        public readonly string $responseExcerpt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $label = match ($this->status) {
            'resolved'     => 'resolved',
            'dismissed'    => 'dismissed',
            default        => 'marked as under review',
        };

        return [
            'type'    => 'complaint_responded',
            'title'   => 'Grade Complaint Updated',
            'message' => "Your complaint about {$this->subjectName} has been {$label}.",
            'url'     => '/complaints',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = match ($this->status) {
            'resolved'  => 'Resolved',
            'dismissed' => 'Dismissed',
            default     => 'Under Review',
        };

        return (new MailMessage)
            ->subject("Grade Complaint {$label} — EncryptEd")
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("Your complaint about **{$this->subjectName}** has been marked as **{$label}**.")
            ->line("Response: \"{$this->responseExcerpt}\"")
            ->action('View My Complaints', url('/complaints'));
    }
}
