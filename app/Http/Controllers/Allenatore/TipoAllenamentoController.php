<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TipoAllenamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoAllenamentoController extends Controller
{
    /**
     * Risolve il team corrente. Priorità:
     * 1. Parametro esplicito (query string o form field)
     * 2. Sessione current_team_id
     * 3. Primo team dell'allenatore
     * Lancia 403 se il team non appartiene all'allenatore, 404 se non esiste.
     */
    private function resolveTeam(?int $teamId = null): Team
    {
        $teamId = $teamId
            ?? session('current_team_id')
            ?? Team::where('allenatore_id', Auth::id())->value('id');

        $team = Team::findOrFail($teamId);
        abort_unless($team->allenatore_id === Auth::id(), 403);
        return $team;
    }

    public function index(Request $request)
    {
        $teams = Team::where('allenatore_id', Auth::id())->orderBy('nome')->get();

        if ($teams->isEmpty()) {
            return view('allenatore.impostazioni.tipo-allenamento', [
                'tipi'        => collect(),
                'teams'       => collect(),
                'currentTeam' => null,
            ]);
        }

        $team = $this->resolveTeam($request->integer('team_id') ?: null);

        TipoAllenamento::creaPerTeam($team->id);
        $tipi = $team->tipiAllenamento()->orderBy('ordine')->orderBy('nome')->get();

        return view('allenatore.impostazioni.tipo-allenamento', compact('tipi', 'teams', 'team'));
    }

    public function store(Request $request)
    {
        $team = $this->resolveTeam($request->integer('team_id') ?: null);
        $request->validate(['nome' => 'required|string|max:100']);

        $maxOrdine = $team->tipiAllenamento()->max('ordine') ?? 0;
        TipoAllenamento::create([
            'team_id' => $team->id,
            'nome'    => $request->nome,
            'ordine'  => $maxOrdine + 1,
        ]);

        return redirect()->route('allenatore.tipo-allenamento.index', ['team_id' => $team->id])
            ->with('success', 'Tipo allenamento aggiunto.');
    }

    public function update(Request $request, TipoAllenamento $tipoAllenamento)
    {
        $team = $this->resolveTeam($request->integer('team_id') ?: null);
        abort_unless($tipoAllenamento->team_id === $team->id, 403);
        $request->validate(['nome' => 'required|string|max:100']);
        $tipoAllenamento->update(['nome' => $request->nome]);

        return redirect()->route('allenatore.tipo-allenamento.index', ['team_id' => $team->id])
            ->with('success', 'Tipo aggiornato.');
    }

    public function destroy(TipoAllenamento $tipoAllenamento)
    {
        // Ownership check: il tipo deve appartenere a un team dell'allenatore
        $team = Team::findOrFail($tipoAllenamento->team_id);
        abort_unless($team->allenatore_id === Auth::id(), 403);
        $tipoAllenamento->delete();

        return redirect()->route('allenatore.tipo-allenamento.index', ['team_id' => $team->id])
            ->with('success', 'Tipo eliminato.');
    }
}
