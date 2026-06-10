<?php

namespace App\Console\Commands;

use App\Models\Feedback;
use App\Models\Microciclo;
use App\Services\PassportWebhookService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Rileva i microcicli terminati "ieri" e notifica l'evento microcycle.end al
 * modulo Passport per ogni atleta che vi ha partecipato.
 *
 * ABBD non ha un campo data_fine sul microciclo: la fine viene inferita come
 * (data_inizio del microciclo successivo nello stesso macrociclo) - 1 giorno;
 * per l'ultimo microciclo si usa macrociclo.data_fine. Gli atleti del microciclo
 * sono ricavati dai Feedback collegati alle sue sedute (Microciclo→Seduta→Feedback).
 *
 * Best-effort: PassportWebhookService è no-op se il modulo è disattivo.
 */
class PassportMicrocicliEnd extends Command
{
    protected $signature = 'abbd:passport-microcicli-end {--date= : Data di riferimento (default: ieri), formato Y-m-d}';

    protected $description = 'Notifica al Passport i microcicli terminati (evento microcycle.end)';

    public function handle(PassportWebhookService $webhook): int
    {
        if (! config('services.passport.attivo')) {
            $this->info('Modulo Passport disattivo (services.passport.attivo=false): nessuna notifica.');

            return self::SUCCESS;
        }

        $ref = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::yesterday()->startOfDay();

        $microcicli = Microciclo::with('macrociclo')
            ->orderBy('macrociclo_id')
            ->orderBy('numero')
            ->get()
            ->groupBy('macrociclo_id');

        $totMicrocicli = 0;
        $totNotifiche  = 0;

        foreach ($microcicli as $lista) {
            $arr = $lista->values();

            foreach ($arr as $i => $m) {
                $next = $arr[$i + 1] ?? null;

                $fine = $next
                    ? $next->data_inizio?->copy()->subDay()
                    : optional($m->macrociclo)->data_fine;

                if (! $fine || ! $fine->isSameDay($ref)) {
                    continue;
                }

                $atletaIds = Feedback::whereHas('seduta', fn ($q) => $q->where('microciclo_id', $m->id))
                    ->distinct()
                    ->pluck('atleta_id');

                foreach ($atletaIds as $atletaId) {
                    $webhook->microcycleEnd((int) $atletaId, [
                        'microciclo_id' => $m->id,
                        'numero'        => $m->numero,
                        'macrociclo_id' => $m->macrociclo_id,
                        'data_fine'     => $fine->toDateString(),
                    ]);
                    $totNotifiche++;
                }

                $totMicrocicli++;
                $this->info("Microciclo #{$m->id} (numero {$m->numero}) terminato il {$fine->toDateString()}: {$atletaIds->count()} atleti notificati.");
            }
        }

        $this->info("Completato: {$totMicrocicli} microcicli terminati, {$totNotifiche} notifiche inviate.");

        return self::SUCCESS;
    }
}
