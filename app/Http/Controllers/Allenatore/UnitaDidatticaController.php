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
        $accessibleTeamIds = Team::accessibleBy(auth()->id())->pluck('id');
        $query = UnitaDidattica::whereIn('team_id', $accessibleTeamIds)
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
        $teams         = Team::accessibleBy(auth()->id())->get();
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

        $this->validaDateStagione($data);

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
        $teams     = Team::accessibleBy(auth()->id())->get();
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

        $this->validaDateStagione($data);

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

    // ── Helper: valida che le date siano dentro la stagione attiva del team ──────

    private function validaDateStagione(array $data): void
    {
        if (empty($data['data_inizio']) && empty($data['data_fine'])) {
            return;
        }

        $stagione = Stagione::where('team_id', $data['team_id'])
            ->orderByDesc('attiva')
            ->orderByDesc('data_inizio')
            ->first();

        if (!$stagione) {
            return; // Nessuna stagione → nessun vincolo
        }

        $errors = [];
        $range  = $stagione->data_inizio->format('d/m/Y') . ' – ' . $stagione->data_fine->format('d/m/Y');

        if (!empty($data['data_inizio'])) {
            $di = \Carbon\Carbon::parse($data['data_inizio']);
            if ($di->lt($stagione->data_inizio) || $di->gt($stagione->data_fine)) {
                $errors['data_inizio'] = "Data inizio fuori dalla stagione ({$range}).";
            }
        }
        if (!empty($data['data_fine'])) {
            $df = \Carbon\Carbon::parse($data['data_fine']);
            if ($df->lt($stagione->data_inizio) || $df->gt($stagione->data_fine)) {
                $errors['data_fine'] = "Data fine fuori dalla stagione ({$range}).";
            }
        }

        if ($errors) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
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
