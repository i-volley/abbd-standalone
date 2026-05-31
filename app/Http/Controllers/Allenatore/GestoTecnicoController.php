<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\CategoriaGesto;
use App\Models\GestoTecnico;
use App\Models\Sport;
use Illuminate\Http\Request;

class GestoTecnicoController extends Controller
{
    public function index()
    {
        return redirect()->route('allenatore.sports.index');
    }

    public function create()
    {
        return redirect()->route('allenatore.sports.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'         => 'required|string|max:100',
            'categoria_id' => 'required|exists:categorie_gesto,id',
            'ordinamento'  => 'nullable|integer|min:1',
            'sport_id'     => 'required|exists:sports,id',
        ]);

        GestoTecnico::create([
            'nome'         => $data['nome'],
            'categoria_id' => $data['categoria_id'],
            'ordinamento'  => $data['ordinamento'] ?? 99,
            'sport_id'     => $data['sport_id'],
        ]);

        return redirect()->route('allenatore.sports.index', ['open_sport' => $data['sport_id']])
            ->with('success', 'Gesto tecnico "' . $data['nome'] . '" aggiunto.');
    }

    public function edit(GestoTecnico $gestoTecnico)
    {
        $categorie = CategoriaGesto::where('sport_id', $gestoTecnico->sport_id)
            ->orderBy('ordinamento')->get();

        return view('allenatore.gesti-tecnici.edit', compact('gestoTecnico', 'categorie'));
    }

    public function update(Request $request, GestoTecnico $gestoTecnico)
    {
        $data = $request->validate([
            'nome'         => 'required|string|max:100',
            'categoria_id' => 'nullable|exists:categorie_gesto,id',
            'ordinamento'  => 'nullable|integer|min:1',
        ]);

        $gestoTecnico->update([
            'nome'         => $data['nome'],
            'categoria_id' => $data['categoria_id'] ?? null,
            'ordinamento'  => $data['ordinamento'] ?? $gestoTecnico->ordinamento,
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
