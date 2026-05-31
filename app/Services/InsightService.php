<?php

namespace App\Services;

use App\Models\Seduta;
use Illuminate\Support\Collection;

/**
 * Analizza il feedback delle sedute recenti e produce suggerimenti diagnostici
 * secondo il metodo FIPAV (Manuale Primo Grado, Metodologia 2-4/5/6/7).
 */
class InsightService
{
    /** Finestra temporale di analisi in giorni */
    private const GIORNI = 30;

    /** Soglie per la classificazione del sintomo */
    private const SOGLIA_BASSA    = 2.5;   // avg miglioramento < 2.5 → errori tecnici
    private const SOGLIA_MEDIA    = 3.5;   // avg miglioramento 2.5-3.5
    private const SOGLIA_RPE_ALTO = 7;     // RPE > 7 → ritmo/velocità

    /**
     * Restituisce array di insight per un allenatore.
     * Ogni insight ha:
     *   - fondamentale (string|null)
     *   - sintomo (string: errori_tecnici|ritmo_velocita|complessita_situazionale)
     *   - metodologia_consigliata (string)
     *   - avg_miglioramento (float)
     *   - avg_rpe (float)
     *   - num_sedute (int)
     *   - wizard_url_params (array per generare link wizard)
     */
    public function forAllenatore(int $allenatore_id): Collection
    {
        $cutoff = now()->subDays(self::GIORNI);

        // Carica sedute completate degli ultimi N giorni con feedback
        $sedute = Seduta::where('allenatore_id', $allenatore_id)
            ->where('stato', 'completata')
            ->where('data', '>=', $cutoff)
            ->with([
                'feedback',
                'sedutaEsercizi.esercizio.gestoTecnico',
            ])
            ->get();

        if ($sedute->isEmpty()) {
            return collect();
        }

        // Raccoglie dati per fondamentale
        // struttura: [ gesto_nome => ['miglioramento' => [...], 'rpe' => [...], 'sedute' => set] ]
        $byFondamentale = [];

        foreach ($sedute as $seduta) {
            if ($seduta->feedback->isEmpty()) continue;

            $avgRpe         = $seduta->feedback->avg('rpe') ?? 0;
            $avgMiglioramento = $seduta->feedback->avg('miglioramento_fondamentale') ?? 0;

            // Per ogni esercizio nella seduta, associa al suo fondamentale
            foreach ($seduta->sedutaEsercizi as $se) {
                $key = $se->esercizio->gestoTecnico?->nome ?? '__generico';
                if (!isset($byFondamentale[$key])) {
                    $byFondamentale[$key] = ['miglioramento' => [], 'rpe' => [], 'sedute' => []];
                }
                $byFondamentale[$key]['miglioramento'][] = $avgMiglioramento;
                $byFondamentale[$key]['rpe'][]           = $avgRpe;
                $byFondamentale[$key]['sedute'][$seduta->id] = true;
            }
        }

        $insights = collect();

        foreach ($byFondamentale as $fondamentale => $dati) {
            $numSedute      = count($dati['sedute']);
            if ($numSedute < 1) continue;

            $avgMigl = array_sum($dati['miglioramento']) / count($dati['miglioramento']);
            $avgRpe  = array_sum($dati['rpe']) / count($dati['rpe']);

            // Diagnosi del sintomo (Metodologia 2-4/5/6/7)
            if ($avgMigl < self::SOGLIA_BASSA) {
                $sintomo             = 'errori_tecnici';
                $metodologia         = 'analitico';
                $descrizione         = 'Media miglioramento fondamentale bassa — probabili errori tecnici esecutivi.';
                $priorita            = 3;
            } elseif ($avgMigl < self::SOGLIA_MEDIA && $avgRpe > self::SOGLIA_RPE_ALTO) {
                $sintomo             = 'ritmo_velocita';
                $metodologia         = 'sintetico';
                $descrizione         = 'RPE alto con miglioramento parziale — difficoltà sotto ritmo di gioco.';
                $priorita            = 2;
            } elseif ($avgMigl < self::SOGLIA_MEDIA) {
                $sintomo             = 'complessita_situazionale';
                $metodologia         = 'globale';
                $descrizione         = 'Miglioramento parziale — difficoltà nella gestione della complessità situazionale.';
                $priorita            = 1;
            } else {
                // Risultati buoni: nessun insight necessario
                continue;
            }

            $insights->push([
                'fondamentale'           => $fondamentale === '__generico' ? null : $fondamentale,
                'fondamentale_label'     => $fondamentale === '__generico' ? 'Generale' : $fondamentale,
                'sintomo'                => $sintomo,
                'metodologia_consigliata'=> $metodologia,
                'descrizione'            => $descrizione,
                'avg_miglioramento'      => round($avgMigl, 1),
                'avg_rpe'                => round($avgRpe, 1),
                'num_sedute'             => $numSedute,
                'priorita'               => $priorita,
                'wizard_params'          => http_build_query([
                    'sintomo'     => $sintomo,
                    'fase_gioco'  => 'tutti',
                    'ruolo'       => 'tutti',
                ]),
            ]);
        }

        return $insights->sortByDesc('priorita')->values();
    }
}
