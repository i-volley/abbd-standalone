<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\GestoTecnico;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\Request;

class GestoTecnicoController extends Controller
{
    private function sportId(): int
    {
        return Team::where('allenatore_id', auth()->id())->value('sport_id') ?? 1;
    }

    public function index()
    {
        $sportId = $this->sportId();
        $gesti   = GestoTecnico::where('sport_id', $sportId)->orderBy('ordinamento')->get();

        return view('allenatore.gesti-tecnici.index', compact('gesti'));
    }

    public function create()
    {
        return view('allenatore.gesti-tecnici.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'       => 'required|string|max:100',
            'categoria'  => 'required|in:fondamentale_base,fondamentale_gioco',
            'ordinamento'=> 'integer|min:0',
        ]);

        GestoTecnico::create([...$data, 'sport_id' => $this->sportId()]);

        return redirect()->route('allenatore.gesti-tecnici.index')->with('success', 'Gesto tecnico creato.');
    }

    public function edit(GestoTecnico $gestoTecnico)
    {
        return view('allenatore.gesti-tecnici.edit', compact('gestoTecnico'));
    }

    public function update(Request $request, GestoTecnico $gestoTecnico)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:100',
            'categoria'   => 'required|in:fondamentale_base,fondamentale_gioco',
            'ordinamento' => 'integer|min:0',
        ]);

        $gestoTecnico->update($data);

        return redirect()->route('allenatore.gesti-tecnici.index')->with('success', 'Gesto tecnico aggiornato.');
    }

    public function destroy(GestoTecnico $gestoTecnico)
    {
        $gestoTecnico->delete();
        return redirect()->route('allenatore.gesti-tecnici.index')->with('success', 'Gesto tecnico eliminato.');
    }
}
