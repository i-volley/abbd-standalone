<?php

namespace App\Notifications;

use App\Models\Seduta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SedutaPubblicataNotification extends Notification
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
            ->subject("Nuova seduta: {$this->seduta->titolo}")
            ->line('È disponibile una nuova seduta di allenamento.')
            ->action('Vedi la seduta', route('atleta.sedute.show', $this->seduta));
    }
}
