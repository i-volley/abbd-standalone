<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::where('allenatore_id', auth()->id())->with('sport')->get();
        return view('allenatore.teams.index', compact('teams'));
    }

    public function create()
    {
        $sports = Sport::where('attivo', true)->get();
        return view('allenatore.teams.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'nome'     => 'required|string|max:255',
            'stagione' => 'required|string|max:20',
        ]);

        Team::create([...$data, 'allenatore_id' => auth()->id()]);

        return redirect()->route('allenatore.teams.index')->with('success', 'Team creato.');
    }

    public function show(Team $team)
    {
        $team->load(['sport', 'atleti']);
        $atleti = User::role('atleta')->get();
        return view('allenatore.teams.show', compact('team', 'atleti'));
    }

    public function edit(Team $team)
    {
        $sports = Sport::where('attivo', true)->get();
        return view('allenatore.teams.edit', compact('team', 'sports'));
    }

    public function update(Request $request, Team $team)
    {
        $data = $request->validate([
            'nome'     => 'required|string|max:255',
            'stagione' => 'required|string|max:20',
        ]);

        $team->update($data);

        return redirect()->route('allenatore.teams.index')->with('success', 'Team aggiornato.');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('allenatore.teams.index')->with('success', 'Team eliminato.');
    }

    /** Imposta il team attivo in sessione e reindirizza al suo hub */
    public function entra(Team $team)
    {
        // Verifica che il team appartenga all'allenatore loggato
        abort_unless($team->allenatore_id === auth()->id(), 403);

        session(['current_team_id' => $team->id, 'current_team_nome' => $team->nome]);

        return redirect()->route('allenatore.teams.hub', $team)
            ->with('success', "Team «{$team->nome}» selezionato.");
    }

    /** Hub del team: riepilogo + calendario sedute */
    public function hub(Team $team)
    {
        abort_unless($team->allenatore_id === auth()->id(), 403);

        $team->load(['sport', 'atleti']);

        // Tutte le sedute del team (per il calendario JS)
        // Formato: { "YYYY-MM-DD": [{id, titolo, stato, url}] }
        $sedute = \App\Models\Seduta::where('team_id', $team->id)
            ->orderBy('data')
            ->get(['id', 'titolo', 'data', 'stato']);

        $sedutePerData = $sedute
            ->groupBy(fn($s) => $s->data->format('Y-m-d'))
            ->map(fn($gruppo) => $gruppo->map(fn($s) => [
                'id'     => $s->id,
                'titolo' => $s->titolo,
                'stato'  => $s->stato,
                'url'    => route('allenatore.sedute.show', $s->id),
            ])->values());

        // Prossime 3 sedute (per la lista rapida)
        $prossime = $sedute->filter(fn($s) => $s->data->gte(today()))->take(3);

        // Ultima seduta completata
        $ultimaCompletata = $sedute->where('stato', 'completata')->sortByDesc('data')->first();

        return view('allenatore.teams.hub', compact('team', 'sedutePerData', 'prossime', 'ultimaCompletata'));
    }

    public function aggiungiAtleta(Request $request, Team $team)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $team->atleti()->syncWithoutDetaching([$request->user_id]);
        return back()->with('success', 'Atleta aggiunto.');
    }

    public function rimuoviAtleta(Team $team, User $atleta)
    {
        $team->atleti()->detach($atleta->id);
        return back()->with('success', 'Atleta rimosso.');
    }
}
