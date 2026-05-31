<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\CategoriaGesto;
use App\Models\Sport;
use Illuminate\Http\Request;

class CategoriaGestoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'sport_id'    => 'required|exists:sports,id',
            'nome'        => 'required|string|max:80',
            'colore'      => 'required|string|max:20',
            'ordinamento' => 'nullable|integer|min:1',
        ]);

        $data['ordinamento'] ??= CategoriaGesto::where('sport_id', $data['sport_id'])->max('ordinamento') + 1;

        CategoriaGesto::create($data);

        return redirect()->route('allenatore.sports.index', ['open_sport' => $data['sport_id']])
            ->with('success', 'Categoria "' . $data['nome'] . '" aggiunta.');
    }

    public function update(Request $request, CategoriaGesto $categoriaGesto)
    {
        $data = $request->validate([
            'nome'   => 'required|string|max:80',
            'colore' => 'required|string|max:20',
        ]);

        $categoriaGesto->update($data);

        return redirect()->route('allenatore.sports.index', ['open_sport' => $categoriaGesto->sport_id])
            ->with('success', 'Categoria aggiornata.');
    }

    public function destroy(CategoriaGesto $categoriaGesto)
    {
        $sportId = $categoriaGesto->sport_id;

        // Stacca i gesti tecnici da questa categoria
        $categoriaGesto->gestiTecnici()->update(['categoria_id' => null]);
        $categoriaGesto->delete();

        return redirect()->route('allenatore.sports.index', ['open_sport' => $sportId])
            ->with('success', 'Categoria eliminata.');
    }
}
