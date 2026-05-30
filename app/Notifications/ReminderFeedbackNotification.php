<?php

namespace App\Notifications;

use App\Models\Seduta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ReminderFeedbackNotification extends Notification
{
    use Queueable;

    public function __construct(public Seduta $seduta) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class, 'mail'];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Scadenza feedback domani!')
            ->body("Hai 24 ore per inviare il feedback: {$this->seduta->titolo}")
            ->action('Invia feedback', route('atleta.sedute.show', $this->seduta));
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Reminder: feedback in scadenza — {$this->seduta->titolo}")
            ->line('Hai ancora 24 ore per inviare il tuo feedback sulla seduta.')
            ->action('Invia ora', route('atleta.sedute.show', $this->seduta));
    }
}
