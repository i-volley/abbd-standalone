<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Macrociclo;
use App\Models\Stagione;
use Illuminate\Http\Request;

class MacrocicloController extends Controller
{
    public function create(Stagione $stagione)
    {
        return view('allenatore.macrocicli.create', compact('stagione'));
    }

    public function store(Request $request, Stagione $stagione)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'fase'        => 'required|in:preparazione,competizione,transizione',
            'obiettivi'   => 'nullable|string',
            'data_inizio' => 'required|date',
            'data_fine'   => 'required|date|after:data_inizio',
        ]);

        $stagione->macrocicli()->create($data);

        return redirect()->route('allenatore.stagioni.show', $stagione)->with('success', 'Macrociclo creato.');
    }

    public function show(Macrociclo $macrociclo)
    {
        $macrociclo->load('microcicli');
        return view('allenatore.macrocicli.show', compact('macrociclo'));
    }

    public function edit(Macrociclo $macrociclo)
    {
        return view('allenatore.macrocicli.edit', compact('macrociclo'));
    }

    public function update(Request $request, Macrociclo $macrociclo)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'fase'        => 'required|in:preparazione,competizione,transizione',
            'obiettivi'   => 'nullable|string',
            'data_inizio' => 'required|date',
            'data_fine'   => 'required|date|after:data_inizio',
        ]);

        $macrociclo->update($data);

        return redirect()->route('allenatore.macrocicli.show', $macrociclo)->with('success', 'Macrociclo aggiornato.');
    }

    public function destroy(Macrociclo $macrociclo)
    {
        $stagione = $macrociclo->stagione;
        $macrociclo->delete();

        return redirect()->route('allenatore.stagioni.show', $stagione)->with('success', 'Macrociclo eliminato.');
    }
}
