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
        $allenatore = User::where('email', 'allenatore@demo.it')->first() ?? User::first();

        if (!$sport || !$allenatore) return;

        $gesti    = GestoTecnico::where('sport_id', $sport->id)->pluck('id', 'nome');
        $capacita = Capacita::pluck('id', 'nome');

        // Struttura: campi base + assi metodologici FIPAV (docs/metodologia-eserciziario.md)
        $esercizi = [

            // ── RISCALDAMENTO ────────────────────────────────────────────────
            [
                'nome' => 'Bagher a coppie', 'fase' => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto' => 'Bagher', 'durata_min' => 10, 'n_gesti' => 50,
                'categoria_eta' => 'U13',
                'capacita' => ['Coordinazione', 'Percezione'],
                // assi FIPAV
                'obiettivo' => 'preparatoria', 'fase_seduta' => 'preparatoria',
                'componente' => 'tecnica', 'livello' => 'base',
                'ruoli' => ['ricevitore_attaccante', 'libero'],
            ],
            [
                'nome' => 'Palleggio muro', 'fase' => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto' => 'Palleggio', 'durata_min' => 8, 'n_gesti' => 60,
                'categoria_eta' => 'U12',
                'capacita' => ['Coordinazione', 'Attenzione'],
                'obiettivo' => null, 'fase_seduta' => 'preparatoria',
                'componente' => 'tecnica', 'livello' => 'base',
                'ruoli' => ['alzatore'],
            ],
            [
                'nome' => 'Saltelli e mobilità caviglie', 'fase' => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto' => null, 'durata_min' => 7,
                'categoria_eta' => 'U10',
                'capacita' => ['Coordinazione', 'Equilibrio'],
                'fase_seduta' => 'preparatoria', 'livello' => 'base',
                'ruoli' => [],   // tutti
            ],
            [
                'nome' => 'Corsa tecnica e coordinazione', 'fase' => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto' => null, 'durata_min' => 8,
                'categoria_eta' => 'Minivolley',
                'capacita' => ['Coordinazione', 'Velocità'],
                'fase_seduta' => 'preparatoria', 'livello' => 'base',
                'ruoli' => [],
            ],

            // ── POTENZIAMENTO — ANALITICO ────────────────────────────────────
            [
                'nome' => 'Battuta float in zona', 'fase' => 'potenziamento', 'metodologia' => 'analitico',
                'gesto' => 'Battuta', 'durata_min' => 12, 'n_gesti' => 30,
                'categoria_eta' => 'U15',
                'capacita' => ['Forza', 'Decision Making'],
                'obiettivo' => 'principale', 'fase_seduta' => 'centrale',
                'componente' => 'tecnica', 'livello' => 'medio',
                'ruoli' => [],   // tutti battono
            ],
            [
                'nome' => 'Muro 1 vs 1', 'fase' => 'potenziamento', 'metodologia' => 'analitico',
                'gesto' => 'Muro', 'durata_min' => 10, 'n_salti' => 30, 'n_giocatori' => '2',
                'categoria_eta' => 'U17',
                'capacita' => ['Potenza', 'Anticipazione', 'Decision Making'],
                'obiettivo' => 'principale', 'fase_seduta' => 'centrale',
                'componente' => 'tecnica', 'livello' => 'medio',
                'ruoli' => ['centrale', 'opposto'],
            ],
            [
                'nome' => 'Alzata in zona 3', 'fase' => 'potenziamento', 'metodologia' => 'analitico',
                'gesto' => 'Alzata', 'durata_min' => 12, 'n_gesti' => 50,
                'categoria_eta' => 'U13',
                'capacita' => ['Coordinazione', 'Percezione', 'Decision Making'],
                'obiettivo' => 'principale', 'fase_seduta' => 'centrale',
                'componente' => 'tecnica', 'livello' => 'base',
                'ruoli' => ['alzatore'],
            ],

            // ── POTENZIAMENTO — SINTETICO ────────────────────────────────────
            [
                'nome' => 'Attacco diagonale', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Attacco', 'durata_min' => 15, 'n_salti' => 40, 'n_gesti' => 30,
                'categoria_eta' => 'U17',
                'capacita' => ['Potenza', 'Coordinazione'],
                'obiettivo' => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco' => 'cambio_palla', 'componente' => 'tecnica',
                'rendimento' => 'positivita', 'livello' => 'medio',
                'n_giocatori' => '3',
                'ruoli' => ['ricevitore_attaccante', 'opposto'],
            ],
            [
                'nome' => 'Ricezione W', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Ricezione', 'durata_min' => 15, 'n_gesti' => 40, 'n_giocatori' => '4',
                'categoria_eta' => 'U15',
                'capacita' => ['Percezione', 'Anticipazione'],
                'obiettivo' => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco' => 'cambio_palla', 'componente' => 'tecnica',
                'rendimento' => 'positivita', 'livello' => 'medio',
                'ruoli' => ['ricevitore_attaccante', 'libero'],
            ],
            [
                'nome' => 'Esercizio fast break attacco', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Attacco', 'durata_min' => 20, 'n_salti' => 60, 'n_giocatori' => '4',
                'categoria_eta' => 'U19',
                'capacita' => ['Velocità', 'Decision Making', 'Potenza'],
                'obiettivo' => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco' => 'break_point', 'componente' => 'tattica',
                'rendimento' => 'efficienza', 'livello' => 'alto',
                'ruoli' => ['centrale', 'ricevitore_attaccante', 'opposto'],
            ],
            [
                'nome' => 'Battuta-ricezione con alzata', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Ricezione', 'durata_min' => 18, 'n_gesti' => 45, 'n_giocatori' => '5',
                'categoria_eta' => 'U17',
                'capacita' => ['Percezione', 'Anticipazione', 'Decision Making'],
                'obiettivo' => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco' => 'cambio_palla', 'componente' => 'tecnica',
                'rendimento' => 'positivita', 'livello' => 'medio',
                'ruoli' => ['alzatore', 'ricevitore_attaccante', 'libero'],
            ],
            [
                'nome' => 'Difesa e ricostruzione 3vs3', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Difesa', 'durata_min' => 20, 'n_giocatori' => '3vs3',
                'categoria_eta' => 'U19',
                'capacita' => ['Anticipazione', 'Decision Making', 'Attenzione'],
                'obiettivo' => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco' => 'break_point', 'componente' => 'tattica',
                'rendimento' => 'gestione_errore', 'livello' => 'alto',
                'ruoli' => ['libero', 'centrale'],
            ],
            [
                'nome' => 'Copertura e ricostruzione alzatore', 'fase' => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto' => 'Alzata', 'durata_min' => 15, 'n_giocatori' => '4',
                'categoria_eta' => 'Senior',
                'capacita' => ['Decision Making', 'Anticipazione'],
                'obiettivo' => 'secondario', 'fase_seduta' => 'centrale',
                'fase_gioco' => 'ricostruzione', 'componente' => 'tattica',
                'rendimento' => 'gestione_errore', 'livello' => 'alto',
                'ruoli' => ['alzatore'],
            ],

            // ── POTENZIAMENTO — GLOBALE ──────────────────────────────────────
            [
                'nome' => 'Gioco 6 vs 6', 'fase' => 'potenziamento', 'metodologia' => 'globale',
                'gesto' => null, 'durata_min' => 25, 'n_giocatori' => '6vs6',
                'categoria_eta' => 'Senior',
                'capacita' => ['Decision Making', 'Anticipazione', 'Attenzione'],
                'obiettivo' => 'permanente', 'fase_seduta' => 'centrale',
                'componente' => 'tattica', 'rendimento' => 'efficienza',
                'livello' => 'alto',
                'ruoli' => [],   // tutti
            ],
            [
                'nome' => 'Sistema difesa-alzata-attacco', 'fase' => 'potenziamento', 'metodologia' => 'globale',
                'gesto' => null, 'durata_min' => 30, 'n_giocatori' => '6vs6',
                'categoria_eta' => 'U19',
                'capacita' => ['Anticipazione', 'Decision Making', 'Attenzione'],
                'obiettivo' => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco' => 'break_point', 'componente' => 'tattica',
                'rendimento' => 'efficienza', 'livello' => 'alto',
                'ruoli' => [],
            ],
            [
                'nome' => 'Cambio palla tattico a obiettivo', 'fase' => 'potenziamento', 'metodologia' => 'globale',
                'gesto' => null, 'durata_min' => 25, 'n_giocatori' => '6vs6',
                'categoria_eta' => 'U19',
                'capacita' => ['Decision Making', 'Anticipazione'],
                'obiettivo' => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco' => 'cambio_palla', 'componente' => 'tattica',
                'rendimento' => 'positivita', 'livello' => 'alto',
                'ruoli' => [],
            ],

            // ── STRETCHING ───────────────────────────────────────────────────
            [
                'nome' => 'Stretching spalle e schiena', 'fase' => 'stretching', 'metodologia' => 'analitico',
                'gesto' => null, 'durata_min' => 10,
                'categoria_eta' => null,
                'capacita' => ['Equilibrio'],
                'fase_seduta' => 'finale', 'livello' => 'base',
                'ruoli' => [],
            ],
            [
                'nome' => 'Mobilità anche e quadricipiti', 'fase' => 'stretching', 'metodologia' => 'analitico',
                'gesto' => null, 'durata_min' => 8,
                'categoria_eta' => null,
                'capacita' => ['Equilibrio'],
                'fase_seduta' => 'finale', 'livello' => 'base',
                'ruoli' => [],
            ],
        ];

        foreach ($esercizi as $e) {
            $esercizio = Esercizio::firstOrCreate(
                ['nome' => $e['nome'], 'sport_id' => $sport->id],
                [
                    'sport_id'         => $sport->id,
                    'gesto_tecnico_id' => isset($e['gesto']) ? ($gesti[$e['gesto']] ?? null) : null,
                    'creato_da'        => $allenatore->id,
                    'fase'             => $e['fase'],
                    'metodologia'      => $e['metodologia'],
                    'durata_min'       => $e['durata_min'],
                    'n_salti'          => $e['n_salti'] ?? 0,
                    'n_gesti'          => $e['n_gesti'] ?? 0,
                    'categoria_eta'    => $e['categoria_eta'] ?? null,
                    'is_pubblico'      => true,
                    // assi FIPAV
                    'obiettivo'        => $e['obiettivo'] ?? null,
                    'fase_seduta'      => $e['fase_seduta'] ?? null,
                    'fase_gioco'       => $e['fase_gioco'] ?? null,
                    'componente'       => $e['componente'] ?? null,
                    'rendimento'       => $e['rendimento'] ?? null,
                    'livello'          => $e['livello'] ?? null,
                    'n_giocatori'      => $e['n_giocatori'] ?? null,
                ]
            );

            // Aggiorna sempre i campi (per re-seed su dati esistenti)
            $esercizio->update([
                'categoria_eta' => $e['categoria_eta'] ?? null,
                'is_pubblico'   => true,
                'obiettivo'     => $e['obiettivo'] ?? null,
                'fase_seduta'   => $e['fase_seduta'] ?? null,
                'fase_gioco'    => $e['fase_gioco'] ?? null,
                'componente'    => $e['componente'] ?? null,
                'rendimento'    => $e['rendimento'] ?? null,
                'livello'       => $e['livello'] ?? null,
                'n_giocatori'   => $e['n_giocatori'] ?? null,
            ]);

            $capIds = collect($e['capacita'] ?? [])->map(fn($c) => $capacita[$c] ?? null)->filter();
            $esercizio->capacita()->syncWithoutDetaching($capIds);

            $esercizio->syncRuoli($e['ruoli'] ?? []);
        }
    }
}
