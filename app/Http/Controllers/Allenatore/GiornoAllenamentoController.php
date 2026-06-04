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
    private function validateGiorno(Request $request): array
    {
        return $request->validate([
            'giorno_settimana'   => 'required|integer|min:0|max:6',
            'titolo_base'        => 'required|string|max:120',
            'tipo_allenamento_id'=> 'nullable|integer|exists:tipo_allenamenti,id',
            'ora_inizio'         => 'required|date_format:H:i',
            'ora_fine'           => 'nullable|date_format:H:i|after:ora_inizio',
            'ora_ritrovo'        => 'nullable|date_format:H:i',
            'note_ritrovo'       => 'nullable|string|max:255',
            'luogo'              => 'nullable|string|max:255',
            'indirizzo'          => 'nullable|string|max:255',
            'citta'              => 'nullable|string|max:100',
            'lat'                => 'nullable|numeric|between:-90,90',
            'lng'                => 'nullable|numeric|between:-180,180',
            'note'               => 'nullable|string|max:255',
        ]);
    }

    private function campi(): array
    {
        return [
            'giorno_settimana', 'titolo_base', 'tipo_allenamento_id',
            'ora_inizio', 'ora_fine', 'ora_ritrovo', 'note_ritrovo',
            'luogo', 'indirizzo', 'citta', 'lat', 'lng', 'note',
        ];
    }

    /** Aggiunge un giorno ricorrente alla stagione */
    public function store(Request $request, Stagione $stagione)
    {
        abort_unless($stagione->team->allenatore_id === auth()->id(), 403);
        $data = $this->validateGiorno($request);

        GiornoAllenamento::firstOrCreate(
            [
                'stagione_id'      => $stagione->id,
                'giorno_settimana' => $data['giorno_settimana'],
                'ora_inizio'       => $data['ora_inizio'],
            ],
            array_merge(array_diff_key($data, array_flip(['giorno_settimana', 'ora_inizio'])),
                ['stagione_id' => $stagione->id])
        );

        return back()->with('success', 'Giorno di allenamento aggiunto.');
    }

    /** Modifica un giorno ricorrente esistente */
    public function update(Request $request, Stagione $stagione, GiornoAllenamento $giorno)
    {
        abort_unless($stagione->team->allenatore_id === auth()->id(), 403);
        abort_unless($giorno->stagione_id === $stagione->id, 403);

        $this->validateGiorno($request);
        $giorno->update($request->only($this->campi()));

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
