<?php

namespace Database\Seeders;

use App\Models\Esercizio;
use App\Models\Feedback;
use App\Models\FeedbackEsercizio;
use App\Models\Seduta;
use App\Models\SedutaEsercizio;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class SeduteSeeder extends Seeder
{
    public function run(): void
    {
        $team       = Team::where('nome', 'Under 18 Femminile')->first();
        $allenatore = User::where('email', 'allenatore@demo.it')->first();

        if (!$team || !$allenatore) return;

        $esercizi = Esercizio::where('sport_id', $team->sport_id)->take(5)->get();
        if ($esercizi->count() < 3) return;

        // Seduta 1: bozza
        Seduta::firstOrCreate(
            ['titolo' => 'Seduta tecnica bozza', 'team_id' => $team->id],
            ['team_id' => $team->id, 'allenatore_id' => $allenatore->id,
             'data' => now()->format('Y-m-d'), 'stato' => 'bozza']
        );

        // Seduta 2: pubblicata + visibile + esercizi + scadenza
        $seduta2 = Seduta::firstOrCreate(
            ['titolo' => 'Allenamento tecnico completo', 'team_id' => $team->id],
            ['team_id' => $team->id, 'allenatore_id' => $allenatore->id,
             'data' => now()->subDay()->format('Y-m-d'), 'stato' => 'pubblicata',
             'visibile_atleti' => true,
             'scadenza_feedback' => now()->addDays(3)->format('Y-m-d H:i:s')]
        );

        foreach ($esercizi->take(4) as $i => $e) {
            SedutaEsercizio::firstOrCreate(
                ['seduta_id' => $seduta2->id, 'esercizio_id' => $e->id],
                ['seduta_id' => $seduta2->id, 'esercizio_id' => $e->id,
                 'ordinamento' => $i, 'serie' => 3, 'ripetizioni' => 15, 'recupero_sec' => 60,
                 'voto_abilitato' => $i < 2]
            );
        }

        $seduta2->update(['durata_tot_min' => $esercizi->take(4)->sum('durata_min')]);

        // Seduta 3: completata con feedback demo
        $seduta3 = Seduta::firstOrCreate(
            ['titolo' => 'Seduta completata con feedback', 'team_id' => $team->id],
            ['team_id' => $team->id, 'allenatore_id' => $allenatore->id,
             'data' => now()->subWeek()->format('Y-m-d'), 'stato' => 'completata',
             'visibile_atleti' => true,
             'scadenza_feedback' => now()->subDays(2)->format('Y-m-d H:i:s')]
        );

        foreach ($esercizi->take(3) as $i => $e) {
            SedutaEsercizio::firstOrCreate(
                ['seduta_id' => $seduta3->id, 'esercizio_id' => $e->id],
                ['seduta_id' => $seduta3->id, 'esercizio_id' => $e->id,
                 'ordinamento' => $i, 'voto_abilitato' => true]
            );
        }

        $atleti = $team->atleti()->take(3)->get();
        $seIds  = SedutaEsercizio::where('seduta_id', $seduta3->id)->get();

        foreach ($atleti as $atleta) {
            $fb = Feedback::firstOrCreate(
                ['seduta_id' => $seduta3->id, 'atleta_id' => $atleta->id],
                ['seduta_id' => $seduta3->id, 'atleta_id' => $atleta->id,
                 'rpe' => rand(5,9), 'qualita_prestazione' => rand(6,10),
                 'impegno_squadra' => rand(7,10), 'miglioramento_fondamentale' => rand(3,5),
                 'nota' => 'Seduta intensa ma formativa.', 'inviato_in_scadenza' => true]
            );

            foreach ($seIds->where('voto_abilitato', true) as $se) {
                FeedbackEsercizio::firstOrCreate(
                    ['feedback_id' => $fb->id, 'seduta_esercizio_id' => $se->id],
                    ['feedback_id' => $fb->id, 'seduta_esercizio_id' => $se->id,
                     'atleta_id' => $atleta->id, 'gradimento' => rand(3,5)]
                );
            }
        }
    }
}
