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
            'titolo_base'      => 'required|string|max:120',
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
                'titolo_base' => $request->titolo_base,
                'ora_fine'    => $request->ora_fine,
                'luogo'       => $request->luogo,
                'note'        => $request->note,
            ]
        );

        return back()->with('success', 'Giorno di allenamento aggiunto.');
    }

    /** Modifica un giorno ricorrente esistente */
    public function update(Request $request, Stagione $stagione, GiornoAllenamento $giorno)
    {
        abort_unless($stagione->team->allenatore_id === auth()->id(), 403);
        abort_unless($giorno->stagione_id === $stagione->id, 403);

        $request->validate([
            'giorno_settimana' => 'required|integer|min:0|max:6',
            'titolo_base'      => 'required|string|max:120',
            'ora_inizio'       => 'required|date_format:H:i',
            'ora_fine'         => 'nullable|date_format:H:i|after:ora_inizio',
            'luogo'            => 'nullable|string|max:255',
            'note'             => 'nullable|string|max:255',
        ]);

        $giorno->update($request->only(['giorno_settimana', 'titolo_base', 'ora_inizio', 'ora_fine', 'luogo', 'note']));

        return back()->with('success', 'Giorno di allenamento aggiornato.');
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
     * Genera sedute bozza per UN singolo giorno programmato nel range scelto.
     * Usa il titolo_base configurato sul giorno stesso.
     * Idempotente: salta date con seduta già esistente con stesso titolo+data.
     */
    public function generaGiorno(Request $request, Stagione $stagione, GiornoAllenamento $giorno)
    {
        abort_unless($stagione->team->allenatore_id === auth()->id(), 403);
        abort_unless($giorno->stagione_id === $stagione->id, 403);

        $request->validate([
            'da' => 'required|date',
            'a'  => 'required|date|after_or_equal:da',
        ]);

        $da      = Carbon::parse($request->da)->startOfDay();
        $a       = Carbon::parse($request->a)->endOfDay();
        $teamId  = $stagione->team_id;
        $allenId = auth()->id();
        $dow     = $giorno->giorno_settimana;
        $label   = GiornoAllenamento::labelGiorni()[$dow];

        // Titolo: "Sala Pesi Lunedì 09:00" (titolo_base + giorno + ora)
        $titoloSeduta = trim($giorno->titolo_base) . ' ' . $label . ' ' . substr($giorno->ora_inizio, 0, 5);

        $create = 0;
        $skip   = 0;

        $period = CarbonPeriod::create($da, $a);
        foreach ($period as $data) {
            if ($data->dayOfWeek !== $dow) continue;

            $dataStr = $data->toDateString();

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
                'luogo'           => $giorno->luogo,
                'note_allenatore' => $giorno->note,
            ]);
            $create++;
        }

        $msg = "«{$titoloSeduta}»: create {$create} sedute.";
        if ($skip > 0) $msg .= " Saltate (già esistenti): {$skip}.";

        return back()->with('success', $msg);
    }
}
