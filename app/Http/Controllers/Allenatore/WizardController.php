<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Esercizio;
use App\Models\GestoTecnico;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\Request;

class WizardController extends Controller
{
    /**
     * Mappa sintomo → metodologia + componente
     * Fonte: Manuale FIPAV Primo Grado, Metodologia 2-4/5/6/7
     */
    private const DIAGNOSI = [
        'errori_tecnici' => [
            'metodologia' => 'analitico',
            'componente'  => 'tecnica',
            'slide'       => 'Metodologia 2-4',
            'citazione'   => 'Numero tendenzialmente elevato di azioni che terminano con un errore tecnico — indice della necessità di utilizzo del metodo analitico.',
            'spiegazione' => 'Gli errori tecnici esecutivi indicano che il gesto non è ancora consolidato. L\'esercizio analitico isola il segmento corporeo o la tecnica specifica per costruire la base motoria corretta.',
        ],
        'ritmo_velocita' => [
            'metodologia' => 'sintetico',
            'componente'  => 'tecnica',
            'slide'       => 'Metodologia 2-5',
            'citazione'   => 'Incidenza significativa di problematiche legate al ritmo attentivo imposto dalla velocità del gioco — indice della necessità di utilizzo del metodo sintetico.',
            'spiegazione' => 'Il gesto è corretto da fermo ma crolla sotto velocità. L\'esercizio sintetico allena la sequenza motoria specifica al ritmo reale dell\'azione, senza le variabili della gara.',
        ],
        'complessita_situazionale' => [
            'metodologia' => 'globale',
            'componente'  => 'tecnica',
            'slide'       => 'Metodologia 2-6',
            'citazione'   => 'Gestione del rapporto tra intensità di esercizio e numero di variabili situazionali implicate nelle azioni — necessità dell\'esercitazione globale.',
            'spiegazione' => 'Il giocatore esegue bene in esercizio ma non trasferisce in partita. L\'esercizio globale mette il fondamentale in situazione di gara reale, con tutte le variabili percettive e decisionali.',
        ],
        'scelte_tattiche' => [
            'metodologia' => 'globale',
            'componente'  => 'tattica',
            'slide'       => 'Metodologia 2-7',
            'citazione'   => 'Adattamenti della tecnica individuale funzionali alle situazioni di gioco — sviluppo della velocità dei processi di elaborazione e risposta motoria situazionale.',
            'spiegazione' => 'Le difficoltà sono di lettura e decisione, non di esecuzione tecnica. L\'esercizio globale tattico sviluppa la capacità di scelta rapida nelle situazioni di gara.',
        ],
    ];

    private function sportId(): int
    {
        return Team::accessibleBy(auth()->id())->value('sport_id')
            ?? Sport::where('slug', 'pallavolo')->value('id')
            ?? 1;
    }

    public function index()
    {
        $sportId = $this->sportId();
        $gesti   = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();

        return view('allenatore.wizard.index', compact('gesti'));
    }

    public function risultati(Request $request)
    {
        $sintomo    = $request->input('sintomo');
        $diagnosi   = self::DIAGNOSI[$sintomo] ?? null;

        if (!$diagnosi) {
            return redirect()->route('allenatore.wizard.index')
                             ->with('error', 'Seleziona un sintomo valido.');
        }

        $sportId = $this->sportId();
        $query   = Esercizio::with(['gestoTecnico', 'capacita', 'ruoli'])
                            ->where('sport_id', $sportId)
                            ->where(fn($q) => $q->where('creato_da', auth()->id())
                                               ->orWhere('is_pubblico', true))
                            ->where('metodologia', $diagnosi['metodologia']);

        if ($diagnosi['componente']) {
            // Includi esercizi con componente corrispondente O senza componente specificata
            $query->where(fn($q) => $q->where('componente', $diagnosi['componente'])
                                      ->orWhereNull('componente'));
        }

        if ($request->filled('gesto_tecnico_id') && $request->gesto_tecnico_id !== 'tutti') {
            $query->where('gesto_tecnico_id', $request->gesto_tecnico_id);
        }

        if ($request->filled('fase_gioco') && $request->fase_gioco !== 'tutti') {
            $query->where(fn($q) => $q->where('fase_gioco', $request->fase_gioco)
                                      ->orWhereNull('fase_gioco'));
        }

        if ($request->filled('ruolo') && $request->ruolo !== 'tutti') {
            $query->where(fn($q) =>
                $q->whereHas('ruoli', fn($r) => $r->where('ruolo', $request->ruolo))
                  ->orWhereDoesntHave('ruoli')  // esercizi senza ruolo = tutti i ruoli
            );
        }

        $esercizi = $query->orderBy('nome')->get();

        $gesti    = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $ruoliDisponibili = Esercizio::ruoliDisponibili();

        return view('allenatore.wizard.risultati', compact(
            'diagnosi', 'sintomo', 'esercizi', 'gesti', 'ruoliDisponibili'
        ));
    }
}
