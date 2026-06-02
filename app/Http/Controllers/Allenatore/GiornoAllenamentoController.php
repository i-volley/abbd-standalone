<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\GiornoAllenamento;
use App\Models\Seduta;
use App\Models\Stagione;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class GiornoAllenamentoController extends Controller
{
    /** Aggiunge un giorno ricorrente alla stagione */
    public function store(Request $request, Stagione $stagione)
    {
        // Verifica proprietà allenatore
        abort_unless($stagione->team->allenatore_id === auth()->id(), 403);

        $request->validate([
            'giorno_settimana' => 'required|integer|min:0|max:6',
            'ora_inizio'       => 'required|date_format:H:i',
            'ora_fine'         => 'nullable|date_format:H:i|after:ora_inizio',
            'luogo'            => 'nullable|string|max:255',
            'note'             => 'nullable|string|max:255',
        ]);

        GiornoAllenamento::firstOrCreate(
            [
                'stagione_id'      => $stagione->id,
                'giorno_settimana' => $request->giorno_settimana,
                'ora_inizio'       => $request->ora_inizio,
            ],
            [
                'ora_fine' => $request->ora_fine,
                'luogo'    => $request->luogo,
                'note'     => $request->note,
            ]
        );

        return back()->with('success', 'Giorno di allenamento aggiunto.');
    }

    /** Elimina un giorno ricorrente */
    public function destroy(Stagione $stagione, GiornoAllenamento $giorno)
    {
        abort_unless($stagione->team->allenatore_id === auth()->id(), 403);
        abort_unless($giorno->stagione_id === $stagione->id, 403);

        $giorno->delete();
        return back()->with('success', 'Giorno rimosso.');
    }

    /**
     * Genera sedute bozza per tutti i giorni programmati nel range scelto.
     * Idempotente: salta le date che hanno già una seduta con lo stesso titolo.
     */
    public function genera(Request $request, Stagione $stagione)
    {
        abort_unless($stagione->team->allenatore_id === auth()->id(), 403);

        $request->validate([
            'da'           => 'required|date',
            'a'            => 'required|date|after_or_equal:da',
            'titolo_base'  => 'required|string|max:120',
            'salta_esistenti' => 'boolean',
        ]);

        $da          = Carbon::parse($request->da)->startOfDay();
        $a           = Carbon::parse($request->a)->endOfDay();
        $titolo      = trim($request->titolo_base);
        $salta       = $request->boolean('salta_esistenti', true);
        $teamId      = $stagione->team_id;
        $allenId     = auth()->id();

        // Giorni ricorrenti configurati
        $giorniConfig = $stagione->giorniAllenamento;
        if ($giorniConfig->isEmpty()) {
            return back()->with('error', 'Nessun giorno di allenamento configurato. Aggiungine almeno uno.');
        }

        // Mappa giorno_settimana -> [{ora_inizio, ora_fine, note}]
        $byGiorno = $giorniConfig->groupBy('giorno_settimana');

        // Sedute già esistenti nel range (per skip)
        $esistenti = Seduta::where('team_id', $teamId)
            ->whereBetween('data', [$da->toDateString(), $a->toDateString()])
            ->pluck('data')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $create = 0;
        $skip   = 0;

        // Itera ogni giorno del range
        $period = CarbonPeriod::create($da, $a);
        foreach ($period as $giorno) {
            $dow = $giorno->dayOfWeek; // 0=Dom, 1=Lun, ..., 6=Sab
            if (!$byGiorno->has($dow)) continue;

            foreach ($byGiorno[$dow] as $cfg) {
                $dataStr = $giorno->toDateString();

                // Titolo univoco: "Allenamento Lunedì 18:00" — ogni seduta ha nome distinto
                $labelGiorno  = GiornoAllenamento::labelGiorni()[$dow];
                $titoloSeduta = $titolo . ' ' . $labelGiorno . ' ' . substr($cfg->ora_inizio, 0, 5);

                if ($salta && in_array($dataStr, $esistenti)) {
                    $skip++;
                    continue;
                }

                // Controlla duplicato stesso titolo+data
                $exists = Seduta::where('team_id', $teamId)
                    ->whereDate('data', $dataStr)
                    ->where('titolo', $titoloSeduta)
                    ->exists();

                if ($exists) { $skip++; continue; }

                Seduta::create([
                    'team_id'         => $teamId,
                    'allenatore_id'   => $allenId,
                    'titolo'          => $titoloSeduta,
                    'data'            => $dataStr,
                    'stato'           => 'bozza',
                    'luogo'           => $cfg->luogo,
                    'note_allenatore' => $cfg->note,
                ]);
                $create++;
            }
        }

        $msg = "Sedute create: {$create}.";
        if ($skip > 0) $msg .= " Saltate (già esistenti): {$skip}.";

        return back()->with('success', $msg);
    }
}
