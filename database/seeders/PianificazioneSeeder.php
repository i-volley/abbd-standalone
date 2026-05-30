<?php

namespace Database\Seeders;

use App\Models\Macrociclo;
use App\Models\Microciclo;
use App\Models\Stagione;
use App\Models\Team;
use Illuminate\Database\Seeder;

class PianificazioneSeeder extends Seeder
{
    public function run(): void
    {
        $team = Team::where('nome', 'Under 18 Femminile')->first();
        if (!$team) return;

        $stagione = Stagione::firstOrCreate(
            ['team_id' => $team->id, 'nome' => 'Stagione 2024-2025'],
            ['data_inizio' => '2024-09-01', 'data_fine' => '2025-06-30', 'attiva' => true]
        );

        $macrocicli = [
            ['nome' => 'Pre-campionato', 'fase' => 'preparazione',
             'data_inizio' => '2024-09-01', 'data_fine' => '2024-10-31', 'obiettivi' => 'Costruire la base fisica e tecnica'],
            ['nome' => 'Campionato invernale', 'fase' => 'competizione',
             'data_inizio' => '2024-11-01', 'data_fine' => '2025-02-28', 'obiettivi' => 'Mantenere performance in gara'],
            ['nome' => 'Finale stagione', 'fase' => 'competizione',
             'data_inizio' => '2025-03-01', 'data_fine' => '2025-05-31', 'obiettivi' => 'Picco per playoff'],
        ];

        foreach ($macrocicli as $m) {
            $macro = Macrociclo::firstOrCreate(
                ['stagione_id' => $stagione->id, 'nome' => $m['nome']],
                array_merge($m, ['stagione_id' => $stagione->id])
            );

            // Aggiungi microciclo settimana corrente nel primo macrociclo
            if ($m['fase'] === 'preparazione') {
                Microciclo::firstOrCreate(
                    ['macrociclo_id' => $macro->id, 'numero' => 1],
                    ['macrociclo_id' => $macro->id, 'numero' => 1,
                     'data_inizio' => now()->startOfWeek()->format('Y-m-d'),
                     'intensita' => 'media']
                );
            }
        }
    }
}
