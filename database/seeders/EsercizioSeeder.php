<?php

namespace Database\Seeders;

use App\Models\Capacita;
use App\Models\Esercizio;
use App\Models\GestoTecnico;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Database\Seeder;

class EsercizioSeeder extends Seeder
{
    public function run(): void
    {
        $sport      = Sport::where('slug', 'pallavolo')->first();
        $allenatore = User::where('email', 'allenatore@demo.it')->first();

        if (!$sport || !$allenatore) return;

        $gesti    = GestoTecnico::where('sport_id', $sport->id)->pluck('id', 'nome');
        $capacita = Capacita::pluck('id', 'nome');

        $esercizi = [
            // ── RISCALDAMENTO ────────────────────────────────────────────────
            [
                'nome' => 'Bagher a coppie', 'fase' => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto' => 'Bagher', 'durata_min' => 10, 'n_gesti' => 50,
                'categoria_eta' => 'U13',
                'capacita' => ['Coordinazione', 'Percezione'],
            ],
            [
                'nome' => 'Palleggio muro', 'fase' => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto' => 'Palleggio', 'durata_min' => 8, 'n_gesti' => 60,
                'categoria_eta' => 'U12',
                'capacita' => ['Coordinazione', 'Attenzione'],
            ],
            [
                'nome' => 'Saltelli e mobilità caviglie', 'fase' => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto' => null, 'durata_min' => 7,
                'categoria_eta' => 'U10',
                'capacita' => ['Coordinazione', 'Equilibrio'],
            ],
            [
                'nome' => 'Corsa tecnica e coordinazione', 'fase' => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto' => null, 'durata_min' => 8,
                'categoria_eta' => 'Minivolley',
                'capacita' => ['Coordinazione', 'Velocità'],
            ],
            // ── POTENZIAMENTO ─────────────────────────────────────────────────
            [
                'nome' => 'Battuta float in zona', 'fase' => 'potenziamento', 'metodologia' => 'analitico',
                'gesto' => 'Battuta', 'durata_min' => 12, 'n_gesti' => 30,
                'categoria_eta' => 'U15',
                'capacita' => ['Forza', 'Decision Making'],
            ],
            [
                'nome' => 'Attacco diagonale', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Attacco', 'durata_min' => 15, 'n_salti' => 40, 'n_gesti' => 30,
                'categoria_eta' => 'U17',
                'capacita' => ['Potenza', 'Coordinazione'],
            ],
            [
                'nome' => 'Muro 1 vs 1', 'fase' => 'potenziamento', 'metodologia' => 'analitico',
                'gesto' => 'Muro', 'durata_min' => 10, 'n_salti' => 30,
                'categoria_eta' => 'U17',
                'capacita' => ['Potenza', 'Anticipazione', 'Decision Making'],
            ],
            [
                'nome' => 'Ricezione W', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Ricezione', 'durata_min' => 15, 'n_gesti' => 40,
                'categoria_eta' => 'U15',
                'capacita' => ['Percezione', 'Anticipazione'],
            ],
            [
                'nome' => 'Gioco 6 vs 6', 'fase' => 'potenziamento', 'metodologia' => 'globale',
                'gesto' => null, 'durata_min' => 25,
                'categoria_eta' => 'Senior',
                'capacita' => ['Decision Making', 'Anticipazione', 'Attenzione'],
            ],
            [
                'nome' => 'Esercizio fast break attacco', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Attacco', 'durata_min' => 20, 'n_salti' => 60,
                'categoria_eta' => 'U19',
                'capacita' => ['Velocità', 'Decision Making', 'Potenza'],
            ],
            [
                'nome' => 'Alzata in zona 3', 'fase' => 'potenziamento', 'metodologia' => 'analitico',
                'gesto' => 'Alzata', 'durata_min' => 12, 'n_gesti' => 50,
                'categoria_eta' => 'U13',
                'capacita' => ['Coordinazione', 'Percezione', 'Decision Making'],
            ],
            [
                'nome' => 'Sistema difesa-alzata-attacco', 'fase' => 'potenziamento', 'metodologia' => 'globale',
                'gesto' => null, 'durata_min' => 30,
                'categoria_eta' => 'U19',
                'capacita' => ['Anticipazione', 'Decision Making', 'Attenzione'],
            ],
            // ── STRETCHING ───────────────────────────────────────────────────
            [
                'nome' => 'Stretching spalle e schiena', 'fase' => 'stretching', 'metodologia' => 'analitico',
                'gesto' => null, 'durata_min' => 10,
                'categoria_eta' => null,
                'capacita' => ['Equilibrio'],
            ],
            [
                'nome' => 'Mobilità anche e quadricipiti', 'fase' => 'stretching', 'metodologia' => 'analitico',
                'gesto' => null, 'durata_min' => 8,
                'categoria_eta' => null,
                'capacita' => ['Equilibrio'],
            ],
        ];

        foreach ($esercizi as $e) {
            $esercizio = Esercizio::firstOrCreate(
                ['nome' => $e['nome'], 'sport_id' => $sport->id],
                [
                    'sport_id'         => $sport->id,
                    'gesto_tecnico_id' => $e['gesto'] ? ($gesti[$e['gesto']] ?? null) : null,
                    'creato_da'        => $allenatore->id,
                    'fase'             => $e['fase'],
                    'metodologia'      => $e['metodologia'],
                    'durata_min'       => $e['durata_min'],
                    'n_salti'          => $e['n_salti'] ?? 0,
                    'n_gesti'          => $e['n_gesti'] ?? 0,
                    'categoria_eta'    => $e['categoria_eta'] ?? null,
                    'is_pubblico'      => true,   // esercizi seeded = catalogo generale
                ]
            );

            // Aggiorna categoria_eta e is_pubblico anche su record già esistenti
            $esercizio->update([
                'categoria_eta' => $e['categoria_eta'] ?? null,
                'is_pubblico'   => true,
            ]);

            $capIds = collect($e['capacita'] ?? [])->map(fn($c) => $capacita[$c] ?? null)->filter();
            $esercizio->capacita()->syncWithoutDetaching($capIds);
        }
    }
}
