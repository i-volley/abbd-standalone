<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\GestoTecnico;
use App\Models\Sport;
use Illuminate\Http\Request;

class GestoTecnicoController extends Controller
{
    public function index()
    {
        // Reindirizza alla pagina impostazioni unificata
        return redirect()->route('allenatore.sports.index');
    }

    public function create()
    {
        // Form inline nelle impostazioni — questa rotta non è più necessaria
        return redirect()->route('allenatore.sports.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:100',
            'categoria'   => 'required|in:fondamentale_base,fondamentale_gioco',
            'ordinamento' => 'nullable|integer|min:1',
            'sport_id'    => 'required|exists:sports,id',
        ]);

        GestoTecnico::create([
            'nome'        => $data['nome'],
            'categoria'   => $data['categoria'],
            'ordinamento' => $data['ordinamento'] ?? 99,
            'sport_id'    => $data['sport_id'],
        ]);

        return redirect()->route('allenatore.sports.index', ['open_sport' => $data['sport_id']])
            ->with('success', 'Gesto tecnico "' . $data['nome'] . '" aggiunto.');
    }

    public function edit(GestoTecnico $gestoTecnico)
    {
        $sports = Sport::orderBy('nome')->get();
        return view('allenatore.gesti-tecnici.edit', compact('gestoTecnico', 'sports'));
    }

    public function update(Request $request, GestoTecnico $gestoTecnico)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:100',
            'categoria'   => 'required|in:fondamentale_base,fondamentale_gioco',
            'ordinamento' => 'nullable|integer|min:1',
        ]);

        $gestoTecnico->update([
            'nome'        => $data['nome'],
            'categoria'   => $data['categoria'],
            'ordinamento' => $data['ordinamento'] ?? $gestoTecnico->ordinamento,
        ]);

        return redirect()->route('allenatore.sports.index', ['open_sport' => $gestoTecnico->sport_id])
            ->with('success', 'Gesto tecnico aggiornato.');
    }

    public function destroy(GestoTecnico $gestoTecnico)
    {
        $sportId = $gestoTecnico->sport_id;
        $gestoTecnico->delete();

        return redirect()->route('allenatore.sports.index', ['open_sport' => $sportId])
            ->with('success', 'Gesto tecnico eliminato.');
    }
}
