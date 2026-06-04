<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\CampoSeduta;
use App\Models\Esercizio;
use App\Models\GestoTecnico;
use App\Models\Seduta;
use App\Models\SedutaEsercizio;
use App\Models\Team;
use App\Notifications\SedutaPubblicataNotification;
use Illuminate\Http\Request;

class SeduteController extends Controller
{
    private function teamId(): int
    {
        return session('current_team_id')
            ?? Team::where('allenatore_id', auth()->id())->value('id')
            ?? 0;
    }

    public function index()
    {
        $query = Seduta::where('allenatore_id', auth()->id())->with('team');

        if (session('current_team_id')) {
            $query->where('team_id', session('current_team_id'));
        }

        $sedute      = $query->orderByDesc('data')->paginate(20);
        $currentTeam = session('current_team_id')
            ? Team::find(session('current_team_id'))
            : null;

        return view('allenatore.sedute.index', compact('sedute', 'currentTeam'));
    }

    public function create()
    {
        $teams           = Team::where('allenatore_id', auth()->id())->get();
        $unitaDidattiche = \App\Models\UnitaDidattica::where('allenatore_id', auth()->id())
                            ->when(session('current_team_id'), fn($q) => $q->where('team_id', session('current_team_id')))
                            ->orderByDesc('created_at')->get();

        $defaultTeamId = session('current_team_id');

        return view('allenatore.sedute.create', compact('teams', 'unitaDidattiche', 'defaultTeamId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'team_id'              => 'required|exists:teams,id',
            'titolo'               => 'required|string|max:255',
            'data'                 => 'required|date',
            'luogo'                => 'nullable|string|max:255',
            'microciclo_id'        => 'nullable|exists:microcicli,id',
            'unita_didattica_id'   => 'nullable|exists:unita_didattiche,id',
            'obiettivo_seduta'     => 'nullable|string|max:500',
            'n_atlete'             => 'nullable|integer|min:1|max:100',
            'obiettivo_principale' => 'nullable|string|max:255',
            'obiettivo_secondario' => 'nullable|string|max:255',
            'n_campi'              => 'nullable|integer|min:1|max:6',
            'scadenza_feedback'    => 'nullable|date',
            'visibile_atleti'      => 'boolean',
            'note_allenatore'      => 'nullable|string',
        ]);

        $nCampi = (int) ($data['n_campi'] ?? 1);
        unset($data['n_campi']);

        $seduta = Seduta::create([
            ...$data,
            'allenatore_id'   => auth()->id(),
            'visibile_atleti' => $request->boolean('visibile_atleti'),
        ]);

        // Crea campi richiesti
        $palette = CampoSeduta::palette();
        for ($i = 0; $i < $nCampi; $i++) {
            $seduta->campi()->create([
                'nome'   => 'Campo ' . ($i + 1),
                'colore' => $palette[$i % count($palette)],
                'ordine' => $i,
            ]);
        }

        return redirect()->route('allenatore.sedute.show', $seduta)->with('success', 'Seduta creata.');
    }

    public function show(Seduta $seduta)
    {
        $seduta->load([
            'sedutaEsercizi.esercizio.capacita',
            'sedutaEsercizi.campo',
            'sedutaEsercizi.fondamentale',
            'campi',
            'team',
            'feedback.atleta',
        ]);

        $gestiFondamentali = GestoTecnico::orderBy('nome')->get();

        return view('allenatore.sedute.show', compact('seduta', 'gestiFondamentali'));
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
            'titolo'               => 'required|string|max:255',
            'data'                 => 'required|date',
            'luogo'                => 'nullable|string|max:255',
            'n_atlete'             => 'nullable|integer|min:1|max:100',
            'obiettivo_principale' => 'nullable|string|max:255',
            'obiettivo_secondario' => 'nullable|string|max:255',
            'scadenza_feedback'    => 'nullable|date',
            'visibile_atleti'      => 'boolean',
            'note_allenatore'      => 'nullable|string',
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

    // ── CAMPI ────────────────────────────────────────────────────────────────

    public function aggiungiCampo(Request $request, Seduta $seduta)
    {
        $request->validate(['nome' => 'required|string|max:100']);

        $count  = $seduta->campi()->count();
        $palette = CampoSeduta::palette();

        $campo = $seduta->campi()->create([
            'nome'   => $request->nome,
            'colore' => $palette[$count % count($palette)],
            'ordine' => $count,
        ]);

        return response()->json([
            'id'     => $campo->id,
            'nome'   => $campo->nome,
            'colore' => $campo->colore,
        ]);
    }

    public function rimuoviCampo(Seduta $seduta, CampoSeduta $campo)
    {
        abort_unless($campo->seduta_id === $seduta->id, 403);
        $campo->delete();
        return response()->json(['ok' => true]);
    }

    // ── ESERCIZI ─────────────────────────────────────────────────────────────

    public function aggiungiEsercizio(Request $request, Seduta $seduta)
    {
        $request->validate([
            'esercizio_id' => 'required|exists:esercizi,id',
            'track'        => 'nullable|in:completo,alzatore,ricevitore_attaccante,centrale,opposto,libero',
            'campo_id'     => 'nullable|exists:campi_seduta,id',
        ]);

        $maxOrdine = SedutaEsercizio::where('seduta_id', $seduta->id)->max('ordinamento') ?? 0;

        $se = SedutaEsercizio::create([
            'seduta_id'    => $seduta->id,
            'esercizio_id' => $request->esercizio_id,
            'ordinamento'  => $maxOrdine + 1,
            'track'        => $request->input('track', 'completo'),
            'campo_id'     => $request->input('campo_id'),
        ]);

        $durata = SedutaEsercizio::where('seduta_id', $seduta->id)
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

    public function aggiornaMetriche(Request $request, Seduta $seduta, int $pivot)
    {
        $se = SedutaEsercizio::where('id', $pivot)->where('seduta_id', $seduta->id)->firstOrFail();

        $data = $request->validate([
            'serie'            => 'nullable|integer|min:1|max:999',
            'ripetizioni'      => 'nullable|integer|min:1|max:999',
            'recupero_sec'     => 'nullable|integer|min:0|max:9999',
            'n_salti'          => 'nullable|integer|min:0|max:9999',
            'minuti_lavoro'    => 'nullable|integer|min:0|max:999',
            'carico_percepito' => 'nullable|integer|min:1|max:10',
            'fondamentale_id'  => 'nullable|exists:gesti_tecnici,id',
            'campo_id'         => 'nullable|exists:campi_seduta,id',
        ]);

        // null out empty strings so nullable fields clear properly
        foreach ($data as $k => $v) {
            if ($v === '') {
                $data[$k] = null;
            }
        }

        $se->update($data);

        return response()->json(['ok' => true]);
    }
}
