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
