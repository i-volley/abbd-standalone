<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Microciclo;
use App\Models\Team;
use App\Models\UnitaDidattica;
use Illuminate\Http\Request;

class UnitaDidatticaController extends Controller
{
    private function teamId(): int
    {
        return Team::where('allenatore_id', auth()->id())->value('id') ?? 0;
    }

    public function index()
    {
        $unita = UnitaDidattica::where('allenatore_id', auth()->id())
            ->with(['sedute', 'team'])
            ->orderByDesc('data_inizio')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('allenatore.unita-didattiche.index', compact('unita'));
    }

    public function create()
    {
        $teams      = Team::where('allenatore_id', auth()->id())->get();
        $microcicli = Microciclo::whereHas('macrociclo.stagione.team', fn($q) =>
            $q->where('allenatore_id', auth()->id())
        )->orderByDesc('data_inizio')->get();
        $progressioni = UnitaDidattica::progressioni();

        return view('allenatore.unita-didattiche.create', compact('teams', 'microcicli', 'progressioni'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'team_id'              => 'required|exists:teams,id',
            'titolo'               => 'required|string|max:255',
            'obiettivo_permanente' => 'required|string|max:1000',
            'progressione'         => 'required|in:analitico_globale,sintetico_globale,libera',
            'microciclo_id'        => 'nullable|exists:microcicli,id',
            'data_inizio'          => 'nullable|date',
            'note'                 => 'nullable|string',
        ]);

        $unita = UnitaDidattica::create([
            ...$data,
            'allenatore_id' => auth()->id(),
        ]);

        return redirect()->route('allenatore.unita-didattiche.show', $unita)
                         ->with('success', 'Unità didattica creata.');
    }

    public function show(UnitaDidattica $unitaDidattica)
    {
        $unitaDidattica->load(['sedute.sedutaEsercizi.esercizio', 'team', 'microciclo']);
        $sequenza = UnitaDidattica::sequenzaMetodologie($unitaDidattica->progressione);

        return view('allenatore.unita-didattiche.show', compact('unitaDidattica', 'sequenza'));
    }

    public function edit(UnitaDidattica $unitaDidattica)
    {
        $teams      = Team::where('allenatore_id', auth()->id())->get();
        $microcicli = Microciclo::whereHas('macrociclo.stagione.team', fn($q) =>
            $q->where('allenatore_id', auth()->id())
        )->orderByDesc('data_inizio')->get();
        $progressioni = UnitaDidattica::progressioni();

        return view('allenatore.unita-didattiche.edit', compact('unitaDidattica', 'teams', 'microcicli', 'progressioni'));
    }

    public function update(Request $request, UnitaDidattica $unitaDidattica)
    {
        $data = $request->validate([
            'team_id'              => 'required|exists:teams,id',
            'titolo'               => 'required|string|max:255',
            'obiettivo_permanente' => 'required|string|max:1000',
            'progressione'         => 'required|in:analitico_globale,sintetico_globale,libera',
            'microciclo_id'        => 'nullable|exists:microcicli,id',
            'data_inizio'          => 'nullable|date',
            'note'                 => 'nullable|string',
        ]);

        $unitaDidattica->update($data);

        return redirect()->route('allenatore.unita-didattiche.show', $unitaDidattica)
                         ->with('success', 'Unità didattica aggiornata.');
    }

    public function destroy(UnitaDidattica $unitaDidattica)
    {
        // Le sedute diventano orfane (unita_didattica_id → null via nullOnDelete)
        $unitaDidattica->delete();
        return redirect()->route('allenatore.unita-didattiche.index')
                         ->with('success', 'Unità eliminata.');
    }
}
