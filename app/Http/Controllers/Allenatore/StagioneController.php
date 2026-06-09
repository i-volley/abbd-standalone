<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Seduta;
use App\Models\Stagione;
use App\Models\Team;
use App\Models\TipoAllenamento;
use App\Models\UnitaDidattica;
use Illuminate\Http\Request;

class StagioneController extends Controller
{
    public function index()
    {
        $accessibleTeamIds = Team::accessibleBy(auth()->id())->pluck('id');
        $query = Stagione::whereIn('team_id', $accessibleTeamIds)
            ->with('team');

        // Filtra per team attivo in sessione
        if (session('current_team_id')) {
            $query->whereHas('team', fn($q) => $q->where('id', session('current_team_id')));
        }

        $stagioni    = $query->orderByDesc('data_inizio')->get();
        $currentTeam = session('current_team_id') ? Team::find(session('current_team_id')) : null;

        return view('allenatore.stagioni.index', compact('stagioni', 'currentTeam'));
    }

    public function create()
    {
        $teams         = Team::accessibleBy(auth()->id())->get();
        $defaultTeamId = session('current_team_id');
        return view('allenatore.stagioni.create', compact('teams', 'defaultTeamId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'team_id'     => 'required|exists:teams,id',
            'nome'        => 'required|string|max:255',
            'data_inizio' => 'required|date',
            'data_fine'   => 'required|date|after:data_inizio',
        ]);

        Stagione::create([...$data, 'attiva' => $request->boolean('attiva')]);

        return redirect()->route('allenatore.stagioni.index')->with('success', 'Stagione creata.');
    }

    public function show(Stagione $stagione)
    {
        $stagione->load([
            'macrocicli'       => fn($q) => $q->orderBy('data_inizio'),
            'giorniAllenamento',
        ]);

        // Sedute del team nell'arco della stagione (per il calendario)
        $sedute = Seduta::where('team_id', $stagione->team_id)
            ->whereBetween('data', [$stagione->data_inizio, $stagione->data_fine])
            ->orderBy('data')
            ->get(['id', 'data', 'titolo', 'stato']);

        // Unità didattiche del team con data_inizio nella stagione
        $unitaDidattiche = UnitaDidattica::where('team_id', $stagione->team_id)
            ->whereNotNull('data_inizio')
            ->whereBetween('data_inizio', [$stagione->data_inizio, $stagione->data_fine])
            ->orderBy('data_inizio')
            ->get(['id', 'titolo', 'data_inizio', 'data_fine', 'colore']);

        // Tipi allenamento del team (per il form giorni)
        TipoAllenamento::creaPerTeam($stagione->team_id); // crea predefiniti se mancano
        $tipiAllenamento = TipoAllenamento::where('team_id', $stagione->team_id)
            ->orderBy('ordine')->orderBy('nome')->get();

        return view('allenatore.stagioni.show', compact('stagione', 'sedute', 'unitaDidattiche', 'tipiAllenamento'));
    }

    public function edit(Stagione $stagione)
    {
        $teams = Team::accessibleBy(auth()->id())->get();
        return view('allenatore.stagioni.edit', compact('stagione', 'teams'));
    }

    public function update(Request $request, Stagione $stagione)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'data_inizio' => 'required|date',
            'data_fine'   => 'required|date|after:data_inizio',
        ]);

        $stagione->update([...$data, 'attiva' => $request->boolean('attiva')]);

        return redirect()->route('allenatore.stagioni.index')->with('success', 'Stagione aggiornata.');
    }

    public function destroy(Stagione $stagione)
    {
        $stagione->delete();
        return redirect()->route('allenatore.stagioni.index')->with('success', 'Stagione eliminata.');
    }
}
