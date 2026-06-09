<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Capacita;
use App\Models\Esercizio;
use App\Models\GestoTecnico;
use App\Models\ParametroEsercizio;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EsercizioController extends Controller
{
    private function sportId(): int
    {
        return Team::where('allenatore_id', auth()->id())->value('sport_id')
            ?? Sport::where('slug', 'pallavolo')->value('id')
            ?? 1;
    }

    /** Parametri attivi raggruppati per tipo, per popolare i menu del form */
    private function parametri()
    {
        return ParametroEsercizio::attiviRaggruppati();
    }

    /** Regole di validazione per i campi parametrici (valori dinamici da DB) */
    private function regoleParametri(): array
    {
        $assi = ['obiettivo', 'fase_seduta', 'fase_gioco', 'componente', 'rendimento', 'livello'];
        $rules = [
            'fase'        => ['required', Rule::in(ParametroEsercizio::valoriValidi('fase'))],
            'metodologia' => ['required', Rule::in(ParametroEsercizio::valoriValidi('metodologia'))],
        ];
        foreach ($assi as $asse) {
            $rules[$asse] = ['nullable', Rule::in(ParametroEsercizio::valoriValidi($asse))];
        }
        return $rules;
    }

    public function index()
    {
        $sportId  = $this->sportId();
        $base     = Esercizio::with(['gestoTecnico', 'capacita', 'ruoli'])->where('sport_id', $sportId);

        $miei     = (clone $base)->where('creato_da', auth()->id())->orderBy('nome')->get();
        $catalogo = (clone $base)->where('is_pubblico', true)->orderBy('nome')->get();
        $gesti    = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $categorie       = Esercizio::categorieEta();
        $ruoliDisponibili = Esercizio::ruoliDisponibili();
        $distretti        = Esercizio::distretti();

        return view('allenatore.esercizi.index', compact('miei', 'catalogo', 'gesti', 'categorie', 'ruoliDisponibili', 'distretti'));
    }

    public function create()
    {
        $sportId          = $this->sportId();
        $gesti            = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $capacita         = Capacita::all();
        $categorie        = Esercizio::categorieEta();
        $ruoliDisponibili = Esercizio::ruoliDisponibili();
        $distretti        = Esercizio::distretti();
        $parametri        = $this->parametri();

        return view('allenatore.esercizi.create', compact('gesti', 'capacita', 'categorie', 'ruoliDisponibili', 'distretti', 'parametri'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'              => 'required|string|max:255',
            'gesto_tecnico_id'  => 'nullable|exists:gesti_tecnici,id',
            'n_salti'           => 'integer|min:0',
            'n_gesti'           => 'integer|min:0',
            'durata_min'        => 'integer|min:1',
            'video_url'         => 'nullable|url',
            'descrizione'       => 'nullable|string|max:2000',
            'categoria_eta'     => 'nullable|in:' . implode(',', Esercizio::categorieEta()),
            'is_pubblico'       => 'boolean',
            'capacita_ids'      => 'nullable|array',
            'capacita_ids.*'    => 'exists:capacita,id',
            'n_giocatori'       => 'nullable|string|max:10',
            'ruoli'                  => 'nullable|array',
            'ruoli.*'                => 'in:' . implode(',', Esercizio::ruoliDisponibili()),
            'prevenzione_distretto'  => 'nullable|in:' . implode(',', Esercizio::distretti()),
            'campo_visivo'           => 'nullable|string',
            // fase, metodologia + assi FIPAV: valori dinamici da parametri_esercizio
            ...$this->regoleParametri(),
        ]);

        // campo_visivo arriva come stringa JSON dal form — decodifico per il cast array del model
        if (isset($data['campo_visivo']) && is_string($data['campo_visivo'])) {
            $data['campo_visivo'] = $data['campo_visivo'] ? json_decode($data['campo_visivo'], true) : null;
        }

        $esercizio = Esercizio::create([
            ...$data,
            'sport_id'    => $this->sportId(),
            'creato_da'   => auth()->id(),
            'is_pubblico' => $request->boolean('is_pubblico'),
        ]);

        if (!empty($data['capacita_ids'])) {
            $esercizio->capacita()->sync($data['capacita_ids']);
        }
        $esercizio->syncRuoli($data['ruoli'] ?? []);

        return redirect()->route('allenatore.esercizi.index')->with('success', 'Esercizio creato.');
    }

    public function show(Esercizio $esercizio)
    {
        $esercizio->load(['gestoTecnico', 'capacita', 'ruoli']);
        return view('allenatore.esercizi.show', compact('esercizio'));
    }

    public function edit(Esercizio $esercizio)
    {
        $sportId          = $this->sportId();
        $gesti            = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $capacita         = Capacita::all();
        $categorie        = Esercizio::categorieEta();
        $ruoliDisponibili = Esercizio::ruoliDisponibili();
        $distretti        = Esercizio::distretti();
        $esercizio->load(['gestoTecnico', 'capacita', 'ruoli']);
        $parametri        = $this->parametri();

        return view('allenatore.esercizi.edit', compact('esercizio', 'gesti', 'capacita', 'categorie', 'ruoliDisponibili', 'distretti', 'parametri'));
    }

    public function update(Request $request, Esercizio $esercizio)
    {
        $data = $request->validate([
            'nome'              => 'required|string|max:255',
            'gesto_tecnico_id'  => 'nullable|exists:gesti_tecnici,id',
            'n_salti'           => 'integer|min:0',
            'n_gesti'           => 'integer|min:0',
            'durata_min'        => 'integer|min:1',
            'video_url'         => 'nullable|url',
            'descrizione'       => 'nullable|string|max:2000',
            'categoria_eta'     => 'nullable|in:' . implode(',', Esercizio::categorieEta()),
            'is_pubblico'       => 'boolean',
            'capacita_ids'      => 'nullable|array',
            'capacita_ids.*'    => 'exists:capacita,id',
            'n_giocatori'       => 'nullable|string|max:10',
            'ruoli'             => 'nullable|array',
            'ruoli.*'           => 'in:' . implode(',', Esercizio::ruoliDisponibili()),
            'prevenzione_distretto'  => 'nullable|in:' . implode(',', Esercizio::distretti()),
            'campo_visivo'           => 'nullable|string',
            // fase, metodologia + assi FIPAV: valori dinamici da parametri_esercizio
            ...$this->regoleParametri(),
        ]);

        // campo_visivo arriva come stringa JSON dal form — decodifico per il cast array del model
        if (isset($data['campo_visivo']) && is_string($data['campo_visivo'])) {
            $data['campo_visivo'] = $data['campo_visivo'] ? json_decode($data['campo_visivo'], true) : null;
        }

        $esercizio->update([
            ...$data,
            'is_pubblico' => $request->boolean('is_pubblico'),
        ]);
        $esercizio->capacita()->sync($data['capacita_ids'] ?? []);
        $esercizio->syncRuoli($data['ruoli'] ?? []);

        return redirect()->route('allenatore.esercizi.index')->with('success', 'Esercizio aggiornato.');
    }

    public function destroy(Esercizio $esercizio)
    {
        $esercizio->delete();
        return redirect()->route('allenatore.esercizi.index')->with('success', 'Esercizio eliminato.');
    }

    public function cerca(Request $request)
    {
        $sportId = $this->sportId();
        $query   = Esercizio::with(['gestoTecnico', 'capacita', 'ruoli'])->where('sport_id', $sportId);

        if ($request->filled('metodologia')) {
            $query->whereIn('metodologia', (array) $request->metodologia);
        }
        if ($request->filled('gesto_tecnico_id')) {
            $query->whereIn('gesto_tecnico_id', (array) $request->gesto_tecnico_id);
        }
        if ($request->filled('fase')) {
            $query->whereIn('fase', (array) $request->fase);
        }
        if ($request->filled('categoria_eta')) {
            $query->where('categoria_eta', $request->categoria_eta);
        }
        if ($request->filled('capacita_ids')) {
            foreach ((array) $request->capacita_ids as $cid) {
                $query->whereHas('capacita', fn($q) => $q->where('capacita.id', $cid));
            }
        }
        // Assi metodologici FIPAV
        if ($request->filled('ruolo')) {
            $query->whereHas('ruoli', fn($q) => $q->whereIn('ruolo', (array) $request->ruolo));
        }
        if ($request->filled('fase_gioco')) {
            $query->whereIn('fase_gioco', (array) $request->fase_gioco);
        }
        if ($request->filled('componente')) {
            $query->where('componente', $request->componente);
        }
        if ($request->filled('obiettivo')) {
            $query->where('obiettivo', $request->obiettivo);
        }
        if ($request->filled('prevenzione_distretto')) {
            $query->where('prevenzione_distretto', $request->prevenzione_distretto);
        }
        if ($request->filled('paradigm_primary')) {
            $query->where('paradigm_primary', $request->paradigm_primary);
        }
        if ($request->filled('exercise_category')) {
            $query->where('exercise_category', $request->exercise_category);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) => $sq->where('nome', 'like', "%$q%")->orWhere('descrizione', 'like', "%$q%"));
        }

        $sedutaId = $request->integer('seduta_id');

        if ($sedutaId) {
            // Contesto seduta builder: mostra tutti gli esercizi accessibili
            $esercizi = (clone $query)
                ->where(fn($q) => $q->where('creato_da', auth()->id())->orWhere('is_pubblico', true))
                ->orderBy('nome')->get();

            $aggiuntiIds = \App\Models\SedutaEsercizio::where('seduta_id', $sedutaId)
                ->pluck('esercizio_id')->toArray();

            return view('allenatore.esercizi._partial-risultati', compact('esercizi', 'sedutaId', 'aggiuntiIds'));
        }

        // Contesto catalogo: due sezioni
        $miei     = (clone $query)->where('creato_da', auth()->id())->orderBy('nome')->get();
        $catalogo = (clone $query)->where('is_pubblico', true)->orderBy('nome')->get();

        return view('allenatore.esercizi._partial-catalogo', compact('miei', 'catalogo'));
    }
}
