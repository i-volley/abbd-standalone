<?php

namespace App\Notifications;

use App\Models\Seduta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderFeedbackNotification extends Notification
{
    use Queueable;

    public function __construct(public Seduta $seduta) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Reminder feedback: {$this->seduta->titolo}")
            ->line('Hai 24 ore per inviare il tuo feedback sulla seduta.')
            ->action('Invia ora', route('atleta.sedute.show', $this->seduta));
    }
}
