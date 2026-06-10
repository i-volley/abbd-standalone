<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Macrociclo;
use App\Models\Microciclo;
use Illuminate\Http\Request;

class MicrocicloController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TODO — Integrazione Passport: fine microciclo (microcycle.end)
    |--------------------------------------------------------------------------
    | Un microciclo ha solo `data_inizio`, non un evento di chiusura esplicito.
    | La fine va rilevata via scheduler (vedi routes/console.php) chiamando
    | PassportWebhookService::microcycleEnd($atletaId, ['microciclo_id' => ...]).
    | Call-site documentato come TODO — vedi agent5.done.
    */
    public function create(Macrociclo $macrociclo)
    {
        return view('allenatore.microcicli.create', compact('macrociclo'));
    }

    public function store(Request $request, Macrociclo $macrociclo)
    {
        $data = $request->validate([
            'numero'      => 'required|integer|min:1',
            'data_inizio' => 'required|date',
            'intensita'   => 'required|in:bassa,media,alta,scarico',
            'note'        => 'nullable|string',
        ]);

        $macrociclo->microcicli()->create($data);

        return redirect()->route('allenatore.macrocicli.show', $macrociclo)->with('success', 'Microciclo creato.');
    }

    public function edit(Microciclo $microciclo)
    {
        $microciclo->load('macrociclo');
        return view('allenatore.microcicli.edit', compact('microciclo'));
    }

    public function update(Request $request, Microciclo $microciclo)
    {
        $data = $request->validate([
            'numero'      => 'required|integer|min:1',
            'data_inizio' => 'required|date',
            'intensita'   => 'required|in:bassa,media,alta,scarico',
            'note'        => 'nullable|string',
        ]);

        $microciclo->update($data);

        return redirect()->route('allenatore.macrocicli.show', $microciclo->macrociclo)
            ->with('success', 'Microciclo aggiornato.');
    }

    public function destroy(Microciclo $microciclo)
    {
        $macrociclo = $microciclo->macrociclo;
        $microciclo->delete();

        return redirect()->route('allenatore.macrocicli.show', $macrociclo)->with('success', 'Microciclo eliminato.');
    }
}
