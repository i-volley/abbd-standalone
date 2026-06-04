<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\TipoAllenamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoAllenamentoController extends Controller
{
    private function currentTeam()
    {
        $teamId = session('current_team_id');
        abort_unless($teamId, 403);
        $team = \App\Models\Team::findOrFail($teamId);
        abort_unless($team->allenatore_id === Auth::id(), 403);
        return $team;
    }

    public function index()
    {
        $team = $this->currentTeam();
        // Crea predefiniti se il team non ne ha ancora
        if ($team->tipiAllenamento()->count() === 0) {
            TipoAllenamento::creaPerTeam($team->id);
        }
        $tipi = $team->tipiAllenamento()->orderBy('ordine')->orderBy('nome')->get();
        return view('allenatore.impostazioni.tipo-allenamento', compact('tipi'));
    }

    public function store(Request $request)
    {
        $team = $this->currentTeam();
        $request->validate(['nome' => 'required|string|max:100']);

        $maxOrdine = $team->tipiAllenamento()->max('ordine') ?? 0;
        TipoAllenamento::create([
            'team_id' => $team->id,
            'nome'    => $request->nome,
            'ordine'  => $maxOrdine + 1,
        ]);

        return back()->with('success', 'Tipo allenamento aggiunto.');
    }

    public function update(Request $request, TipoAllenamento $tipoAllenamento)
    {
        $team = $this->currentTeam();
        abort_unless($tipoAllenamento->team_id === $team->id, 403);
        $request->validate(['nome' => 'required|string|max:100']);
        $tipoAllenamento->update(['nome' => $request->nome]);
        return back()->with('success', 'Tipo aggiornato.');
    }

    public function destroy(TipoAllenamento $tipoAllenamento)
    {
        $team = $this->currentTeam();
        abort_unless($tipoAllenamento->team_id === $team->id, 403);
        $tipoAllenamento->delete();
        return back()->with('success', 'Tipo eliminato.');
    }
}
