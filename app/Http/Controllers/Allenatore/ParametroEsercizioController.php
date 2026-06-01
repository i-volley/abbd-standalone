<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\ParametroEsercizio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ParametroEsercizioController extends Controller
{
    public function index()
    {
        $tipi       = ParametroEsercizio::tipi();
        $parametri  = ParametroEsercizio::orderBy('ordinamento')->orderBy('etichetta')
                        ->get()->groupBy('tipo');

        return view('allenatore.impostazioni.parametri', compact('tipi', 'parametri'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'      => ['required', Rule::in(array_keys(ParametroEsercizio::tipi()))],
            'etichetta' => 'required|string|max:120',
            'valore'    => 'nullable|string|max:60',
            'colore'    => 'nullable|string|max:9',
        ]);

        // Valore macchina: se non fornito, derivato dall'etichetta (slug con underscore)
        $valore = $data['valore'] ?: Str::of($data['etichetta'])->slug('_')->toString();

        // Evita duplicati tipo+valore
        if (ParametroEsercizio::where('tipo', $data['tipo'])->where('valore', $valore)->exists()) {
            return back()->withErrors(['valore' => 'Esiste già un parametro con questo valore per questo tipo.']);
        }

        $ordinamento = (int) ParametroEsercizio::where('tipo', $data['tipo'])->max('ordinamento') + 1;

        ParametroEsercizio::create([
            'tipo'        => $data['tipo'],
            'valore'      => $valore,
            'etichetta'   => $data['etichetta'],
            'colore'      => $data['colore'] ?: null,
            'ordinamento' => $ordinamento,
            'attivo'      => true,
            'di_sistema'  => false,
        ]);

        return back()->with('success', 'Parametro aggiunto.');
    }

    public function update(Request $request, ParametroEsercizio $parametro)
    {
        $data = $request->validate([
            'etichetta'   => 'required|string|max:120',
            'colore'      => 'nullable|string|max:9',
            'ordinamento' => 'nullable|integer|min:0',
            'attivo'      => 'boolean',
        ]);

        $parametro->update([
            'etichetta'   => $data['etichetta'],
            'colore'      => $data['colore'] ?: null,
            'ordinamento' => $data['ordinamento'] ?? $parametro->ordinamento,
            'attivo'      => $request->boolean('attivo'),
        ]);

        return back()->with('success', 'Parametro aggiornato.');
    }

    public function destroy(ParametroEsercizio $parametro)
    {
        if ($parametro->di_sistema) {
            return back()->withErrors(['parametro' => 'I parametri di sistema FIPAV non sono eliminabili. Puoi disattivarli.']);
        }

        $parametro->delete();
        return back()->with('success', 'Parametro eliminato.');
    }
}
