<?php

namespace App\Notifications;

use App\Models\Seduta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class SedutaPubblicataNotification extends Notification
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
            ->title('Nuova seduta disponibile!')
            ->body("L'allenatore ha pubblicato: {$this->seduta->titolo}")
            ->action('Vedi seduta', route('atleta.sedute.show', $this->seduta));
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nuova seduta: {$this->seduta->titolo}")
            ->line('È disponibile una nuova seduta di allenamento.')
            ->action('Vedi la seduta', route('atleta.sedute.show', $this->seduta));
    }
}
