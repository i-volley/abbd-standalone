<?php

namespace App\Console\Commands;

use App\Models\Feedback;
use App\Models\Seduta;
use App\Notifications\ReminderFeedbackNotification;
use Illuminate\Console\Command;

class InviaReminderFeedback extends Command
{
    protected $signature   = 'abbd:reminder-feedback';
    protected $description = 'Invia reminder push+email agli atleti con feedback in scadenza entro 24h';

    public function handle(): void
    {
        $sedute = Seduta::where('visibile_atleti', true)
            ->where('reminder_inviato', false)
            ->whereBetween('scadenza_feedback', [now()->addHours(24), now()->addHours(25)])
            ->with('team.atleti')
            ->get();

        foreach ($sedute as $seduta) {
            foreach ($seduta->team->atleti as $atleta) {
                $haVotato = Feedback::where('seduta_id', $seduta->id)
                    ->where('atleta_id', $atleta->id)->exists();

                if (!$haVotato) {
                    try {
                        $atleta->notify(new ReminderFeedbackNotification($seduta));
                    } catch (\Exception $e) {
                        $this->error("Notifica fallita per atleta {$atleta->id}: {$e->getMessage()}");
                    }
                }
            }

            $seduta->update(['reminder_inviato' => true]);
            $this->info("Reminder inviato per seduta {$seduta->id}: {$seduta->titolo}");
        }

        $this->info('Reminder completati: ' . $sedute->count() . ' sedute processate.');
    }
}
