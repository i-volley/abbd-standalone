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
        $sportId   = $this->sportId();
        $esercizi  = Esercizio::with(['gestoTecnico', 'capacita'])
            ->where('sport_id', $sportId)->orderBy('nome')->paginate(20);
        $gesti     = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $capacita  = Capacita::all();

        return view('allenatore.esercizi.index', compact('esercizi', 'gesti', 'capacita'));
    }

    public function create()
    {
        $sportId  = $this->sportId();
        $gesti    = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();
        $capacita = Capacita::all();

        return view('allenatore.esercizi.create', compact('gesti', 'capacita'));
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
            'capacita_ids'      => 'nullable|array',
            'capacita_ids.*'    => 'exists:capacita,id',
        ]);

        $esercizio = Esercizio::create([
            ...$data,
            'sport_id'  => $this->sportId(),
            'creato_da' => auth()->id(),
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

        return view('allenatore.esercizi.edit', compact('esercizio', 'gesti', 'capacita'));
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
            'capacita_ids'      => 'nullable|array',
            'capacita_ids.*'    => 'exists:capacita,id',
        ]);

        $esercizio->update($data);
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

        if ($request->filled('gesto_tecnico_id')) {
            $query->whereIn('gesto_tecnico_id', (array) $request->gesto_tecnico_id);
        }

        if ($request->filled('categoria')) {
            $query->whereHas('gestoTecnico', fn($q) => $q->where('categoria', $request->categoria));
        }

        if ($request->filled('fase')) {
            $query->whereIn('fase', (array) $request->fase);
        }

        if ($request->filled('metodologia')) {
            $query->whereIn('metodologia', (array) $request->metodologia);
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

        $esercizi     = $query->orderBy('nome')->get();
        $sedutaId     = $request->integer('seduta_id');
        $aggiuntiIds  = [];

        if ($sedutaId) {
            $aggiuntiIds = \App\Models\SedutaEsercizio::where('seduta_id', $sedutaId)
                ->pluck('esercizio_id')->toArray();
        }

        return view('allenatore.esercizi._partial-risultati', compact('esercizi', 'sedutaId', 'aggiuntiIds'));
    }
}
