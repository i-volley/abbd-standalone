<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Capacita;
use App\Models\Esercizio;
use App\Models\GestoTecnico;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\Request;

class EsercizioController extends Controller
{
    private function sportId(): int
    {
        return Team::where('allenatore_id', auth()->id())->value('sport_id') ?? 1;
    }

    public function index()
    {
        $sportId  = $this->sportId();
        $base     = Esercizio::with(['gestoTecnico', 'capacita'])->where('sport_id', $sportId);

        $miei     = (clone $base)->where('creato_da', auth()->id())->orderBy('nome')->get();
        $catalogo = (clone $base)->where('is_pubblico', true)->orderBy('nome')->get();
        $gesti    = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $categorie = Esercizio::categorieEta();

        return view('allenatore.esercizi.index', compact('miei', 'catalogo', 'gesti', 'categorie'));
    }

    public function create()
    {
        $sportId  = $this->sportId();
        $gesti    = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $capacita = Capacita::all();
        $categorie = Esercizio::categorieEta();

        return view('allenatore.esercizi.create', compact('gesti', 'capacita', 'categorie'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'              => 'required|string|max:255',
            'fase'              => 'required|in:riscaldamento,potenziamento,stretching',
            'metodologia'       => 'required|in:analitico,sintetico,globale',
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
        ]);

        $esercizio = Esercizio::create([
            ...$data,
            'sport_id'    => $this->sportId(),
            'creato_da'   => auth()->id(),
            'is_pubblico' => $request->boolean('is_pubblico'),
        ]);

        if (!empty($data['capacita_ids'])) {
            $esercizio->capacita()->sync($data['capacita_ids']);
        }

        return redirect()->route('allenatore.esercizi.index')->with('success', 'Esercizio creato.');
    }

    public function show(Esercizio $esercizio)
    {
        $esercizio->load(['gestoTecnico', 'capacita']);
        return view('allenatore.esercizi.show', compact('esercizio'));
    }

    public function edit(Esercizio $esercizio)
    {
        $sportId  = $this->sportId();
        $gesti    = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $capacita = Capacita::all();
        $categorie = Esercizio::categorieEta();

        return view('allenatore.esercizi.edit', compact('esercizio', 'gesti', 'capacita', 'categorie'));
    }

    public function update(Request $request, Esercizio $esercizio)
    {
        $data = $request->validate([
            'nome'              => 'required|string|max:255',
            'fase'              => 'required|in:riscaldamento,potenziamento,stretching',
            'metodologia'       => 'required|in:analitico,sintetico,globale',
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
        ]);

        $esercizio->update([
            ...$data,
            'is_pubblico' => $request->boolean('is_pubblico'),
        ]);
        $esercizio->capacita()->sync($data['capacita_ids'] ?? []);

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
        $query   = Esercizio::with(['gestoTecnico', 'capacita'])->where('sport_id', $sportId);

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
