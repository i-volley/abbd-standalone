<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Stagione;
use App\Models\Team;
use Illuminate\Http\Request;

class StagioneController extends Controller
{
    public function index()
    {
        $stagioni = Stagione::whereHas('team', fn($q) => $q->where('allenatore_id', auth()->id()))
            ->with('team')->orderByDesc('data_inizio')->get();

        return view('allenatore.stagioni.index', compact('stagioni'));
    }

    public function create()
    {
        $teams = Team::where('allenatore_id', auth()->id())->get();
        return view('allenatore.stagioni.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'team_id'     => 'required|exists:teams,id',
            'nome'        => 'required|string|max:255',
            'data_inizio' => 'required|date',
            'data_fine'   => 'required|date|after:data_inizio',
            'attiva'      => 'boolean',
        ]);

        Stagione::create([...$data, 'attiva' => $request->boolean('attiva')]);

        return redirect()->route('allenatore.stagioni.index')->with('success', 'Stagione creata.');
    }

    public function show(Stagione $stagione)
    {
        $stagione->load('macrocicli');
        return view('allenatore.stagioni.show', compact('stagione'));
    }

    public function edit(Stagione $stagione)
    {
        $teams = Team::where('allenatore_id', auth()->id())->get();
        return view('allenatore.stagioni.edit', compact('stagione', 'teams'));
    }

    public function update(Request $request, Stagione $stagione)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'data_inizio' => 'required|date',
            'data_fine'   => 'required|date|after:data_inizio',
            'attiva'      => 'boolean',
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
