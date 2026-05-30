<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Esercizio;
use App\Models\Seduta;
use App\Models\SedutaEsercizio;
use App\Models\Team;
use App\Notifications\SedutaPubblicataNotification;
use Illuminate\Http\Request;

class SeduteController extends Controller
{
    private function teamId(): int
    {
        return Team::where('allenatore_id', auth()->id())->value('id') ?? 0;
    }

    public function index()
    {
        $sedute = Seduta::where('allenatore_id', auth()->id())
            ->with('team')->orderByDesc('data')->paginate(20);

        return view('allenatore.sedute.index', compact('sedute'));
    }

    public function create()
    {
        $teams = Team::where('allenatore_id', auth()->id())->get();
        return view('allenatore.sedute.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'team_id'           => 'required|exists:teams,id',
            'titolo'            => 'required|string|max:255',
            'data'              => 'required|date',
            'microciclo_id'     => 'nullable|exists:microcicli,id',
            'scadenza_feedback' => 'nullable|date',
            'visibile_atleti'   => 'boolean',
            'note_allenatore'   => 'nullable|string',
        ]);

        $seduta = Seduta::create([
            ...$data,
            'allenatore_id'   => auth()->id(),
            'visibile_atleti' => $request->boolean('visibile_atleti'),
        ]);

        return redirect()->route('allenatore.sedute.show', $seduta)->with('success', 'Seduta creata.');
    }

    public function show(Seduta $seduta)
    {
        $seduta->load(['sedutaEsercizi.esercizio.capacita', 'team', 'feedback.atleta']);
        return view('allenatore.sedute.show', compact('seduta'));
    }

    public function edit(Seduta $seduta)
    {
        $seduta->load(['sedutaEsercizi.esercizio.capacita']);
        $teams = Team::where('allenatore_id', auth()->id())->get();
        return view('allenatore.sedute.edit', compact('seduta', 'teams'));
    }

    public function update(Request $request, Seduta $seduta)
    {
        $data = $request->validate([
            'titolo'            => 'required|string|max:255',
            'data'              => 'required|date',
            'scadenza_feedback' => 'nullable|date',
            'visibile_atleti'   => 'boolean',
            'note_allenatore'   => 'nullable|string',
        ]);

        $seduta->update([...$data, 'visibile_atleti' => $request->boolean('visibile_atleti')]);

        return redirect()->route('allenatore.sedute.show', $seduta)->with('success', 'Seduta aggiornata.');
    }

    public function destroy(Seduta $seduta)
    {
        $seduta->delete();
        return redirect()->route('allenatore.sedute.index')->with('success', 'Seduta eliminata.');
    }

    public function pubblica(Seduta $seduta)
    {
        $seduta->update(['stato' => 'pubblicata']);
        return back()->with('success', 'Seduta pubblicata.');
    }

    public function toggleVisibilita(Request $request, Seduta $seduta)
    {
        $visibile = !$seduta->visibile_atleti;
        $seduta->update(['visibile_atleti' => $visibile]);

        if ($visibile) {
            $seduta->load('team.atleti');
            foreach ($seduta->team->atleti as $atleta) {
                try {
                    $atleta->notify(new SedutaPubblicataNotification($seduta));
                } catch (\Exception $e) {
                    // Notifica fallita — non blocca il flusso
                }
            }
        }

        return back()->with('success', $visibile ? 'Seduta resa visibile. Atleti notificati.' : 'Seduta nascosta agli atleti.');
    }

    public function aggiungiEsercizio(Request $request, Seduta $seduta)
    {
        $request->validate(['esercizio_id' => 'required|exists:esercizi,id']);

        $maxOrdine = SedutaEsercizio::where('seduta_id', $seduta->id)->max('ordinamento') ?? 0;

        $se = SedutaEsercizio::create([
            'seduta_id'    => $seduta->id,
            'esercizio_id' => $request->esercizio_id,
            'ordinamento'  => $maxOrdine + 1,
        ]);

        $esercizio = Esercizio::find($request->esercizio_id);
        $durata    = SedutaEsercizio::where('seduta_id', $seduta->id)
            ->join('esercizi', 'esercizi.id', '=', 'seduta_esercizi.esercizio_id')
            ->sum('esercizi.durata_min');

        $seduta->update(['durata_tot_min' => $durata]);

        return response()->json(['pivot_id' => $se->id, 'durata_tot' => $durata]);
    }

    public function rimuoviEsercizio(Seduta $seduta, int $pivot)
    {
        SedutaEsercizio::where('id', $pivot)->where('seduta_id', $seduta->id)->delete();

        $durata = SedutaEsercizio::where('seduta_id', $seduta->id)
            ->join('esercizi', 'esercizi.id', '=', 'seduta_esercizi.esercizio_id')
            ->sum('esercizi.durata_min');

        $seduta->update(['durata_tot_min' => $durata]);

        return response()->json(['durata_tot' => $durata]);
    }

    public function aggiornaOrdine(Request $request, Seduta $seduta)
    {
        $request->validate(['ordine' => 'required|array', 'ordine.*' => 'integer']);

        foreach ($request->ordine as $pos => $pivotId) {
            SedutaEsercizio::where('id', $pivotId)->where('seduta_id', $seduta->id)
                ->update(['ordinamento' => $pos]);
        }

        return response()->json(['ok' => true]);
    }

    public function toggleVotoEsercizio(Seduta $seduta, int $pivot)
    {
        $se = SedutaEsercizio::where('id', $pivot)->where('seduta_id', $seduta->id)->firstOrFail();
        $se->update(['voto_abilitato' => !$se->voto_abilitato]);

        return response()->json(['voto_abilitato' => $se->voto_abilitato]);
    }
}
