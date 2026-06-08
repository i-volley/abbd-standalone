<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Stagione;
use App\Models\Team;
use App\Models\UnitaDidattica;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UnitaDidatticaController extends Controller
{
    public function index()
    {
        $query = UnitaDidattica::where('allenatore_id', auth()->id())
            ->with(['sedute', 'team']);

        if (session('current_team_id')) {
            $query->where('team_id', session('current_team_id'));
        }

        $unita       = $query->orderByDesc('data_inizio')->orderByDesc('created_at')->paginate(15);
        $currentTeam = session('current_team_id') ? Team::find(session('current_team_id')) : null;

        return view('allenatore.unita-didattiche.index', compact('unita', 'currentTeam'));
    }

    public function create()
    {
        $teams         = Team::where('allenatore_id', auth()->id())->get();
        $defaultTeamId = session('current_team_id');
        $teamsData     = $this->buildTeamsData($teams);

        return view('allenatore.unita-didattiche.create', compact('teams', 'defaultTeamId', 'teamsData'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'team_id'              => 'required|exists:teams,id',
            'titolo'               => 'required|string|max:255',
            'obiettivo_permanente' => 'required|string|max:1000',
            'data_inizio'          => 'nullable|date',
            'data_fine'            => 'nullable|date|after_or_equal:data_inizio',
            'colore'               => 'nullable|string|max:7',
            'note'                 => 'nullable|string',
        ]);

        $unita = UnitaDidattica::create([...$data, 'allenatore_id' => auth()->id()]);

        return redirect()->route('allenatore.unita-didattiche.show', $unita)
                         ->with('success', 'Unità didattica creata.');
    }

    public function show(UnitaDidattica $unitaDidattica)
    {
        $unitaDidattica->load(['sedute.sedutaEsercizi.esercizio', 'team']);

        return view('allenatore.unita-didattiche.show', compact('unitaDidattica'));
    }

    public function edit(UnitaDidattica $unitaDidattica)
    {
        $teams     = Team::where('allenatore_id', auth()->id())->get();
        $teamsData = $this->buildTeamsData($teams);

        return view('allenatore.unita-didattiche.edit', compact('unitaDidattica', 'teams', 'teamsData'));
    }

    public function update(Request $request, UnitaDidattica $unitaDidattica)
    {
        $data = $request->validate([
            'team_id'              => 'required|exists:teams,id',
            'titolo'               => 'required|string|max:255',
            'obiettivo_permanente' => 'required|string|max:1000',
            'data_inizio'          => 'nullable|date',
            'data_fine'            => 'nullable|date|after_or_equal:data_inizio',
            'colore'               => 'nullable|string|max:7',
            'note'                 => 'nullable|string',
        ]);

        $unitaDidattica->update($data);

        return redirect()->route('allenatore.unita-didattiche.show', $unitaDidattica)
                         ->with('success', 'Unità didattica aggiornata.');
    }

    public function destroy(UnitaDidattica $unitaDidattica)
    {
        $unitaDidattica->delete();
        return redirect()->route('allenatore.unita-didattiche.index')
                         ->with('success', 'Unità eliminata.');
    }

    // ── Helper: dati stagione+macrocicli per ogni team (per il calendar widget JS) ──

    private function buildTeamsData(Collection $teams): array
    {
        $data = [];
        foreach ($teams as $t) {
            $stagione = Stagione::where('team_id', $t->id)
                ->orderByDesc('attiva')
                ->orderByDesc('data_inizio')
                ->first();

            $data[(string) $t->id] = [
                'stagione' => $stagione ? [
                    'da'   => $stagione->data_inizio->format('Y-m-d'),
                    'a'    => $stagione->data_fine->format('Y-m-d'),
                    'nome' => $stagione->nome,
                ] : null,
                'macrocicli' => $stagione
                    ? $stagione->macrocicli()
                        ->orderBy('data_inizio')
                        ->get()
                        ->map(fn($m) => [
                            'nome'   => $m->nome,
                            'colore' => $m->colore ?? '#4f46e5',
                            'da'     => $m->data_inizio->format('Y-m-d'),
                            'a'      => $m->data_fine->format('Y-m-d'),
                        ])
                        ->values()
                        ->toArray()
                    : [],
            ];
        }
        return $data;
    }
}
