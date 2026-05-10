<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnlockDecidedNotification extends Notification
{
    public function __construct(
        public readonly string $subjectName,
        public readonly string $sectionName,
        public readonly string $decision,    // 'approved' | 'denied'
        public readonly ?string $notes,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $verb = $this->decision === 'approved' ? 'approved' : 'denied';

        return [
            'type'    => 'unlock_decided',
            'title'   => 'Grade Unlock ' . ucfirst($verb),
            'message' => "Your unlock request for {$this->subjectName} ({$this->sectionName}) was {$verb}."
                       . ($this->notes ? " Note: {$this->notes}" : ''),
            'url'     => '/faculty/gradebook',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verb = $this->decision === 'approved' ? 'Approved' : 'Denied';

        $mail = (new MailMessage)
            ->subject("Grade Unlock Request {$verb} — EncryptEd")
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("Your grade unlock request for **{$this->subjectName}** ({$this->sectionName}) has been **{$verb}**.");

        if ($this->notes) {
            $mail->line("Reviewer note: \"{$this->notes}\"");
        }

        if ($this->decision === 'approved') {
            $mail->action('Open Gradebook', url('/faculty/gradebook'));
        }

        return $mail;
    }
}
