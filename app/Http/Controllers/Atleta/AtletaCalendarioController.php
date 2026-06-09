<?php

namespace App\Http\Controllers\Atleta;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Seduta;
use App\Models\Stagione;
use Carbon\Carbon;

class AtletaCalendarioController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $team = $user->teams()->first();

        if (!$team) {
            return view('atleta.calendario.index', ['settimane' => collect(), 'stagione' => null]);
        }

        // Stagione attiva o ultima del team
        $stagione = Stagione::where('team_id', $team->id)
            ->where('attiva', true)
            ->with('giorniAllenamento.tipoAllenamento')
            ->first()
            ?? Stagione::where('team_id', $team->id)
                ->with('giorniAllenamento.tipoAllenamento')
                ->orderByDesc('data_inizio')
                ->first();

        if (!$stagione || $stagione->giorniAllenamento->isEmpty()) {
            return view('atleta.calendario.index', ['settimane' => collect(), 'stagione' => $stagione]);
        }

        $oggi = Carbon::today();
        $fine = $stagione->data_fine->min($oggi->copy()->addWeeks(8));

        // Sedute pubbliche nel range
        $sedute = Seduta::where('team_id', $team->id)
            ->where('visibile_atleti', true)
            ->whereBetween('data', [$oggi, $fine])
            ->orderBy('data')
            ->get()
            ->keyBy(fn($s) => $s->data->format('Y-m-d'));

        $feedbackInviati = Feedback::where('atleta_id', $user->id)
            ->whereIn('seduta_id', $sedute->pluck('id'))
            ->pluck('seduta_id')
            ->toArray();

        // giorno_settimana: 0=Dom, 1=Lun, ..., 6=Sab (uguale a Carbon dayOfWeek)
        $giornoMap = $stagione->giorniAllenamento->groupBy('giorno_settimana');

        // Genera slot giorno per giorno da oggi a $fine
        $slots = collect();
        $cursor = $oggi->copy();
        while ($cursor->lte($fine)) {
            $dow = $cursor->dayOfWeek;
            if ($giornoMap->has($dow)) {
                $key    = $cursor->format('Y-m-d');
                $seduta = $sedute->get($key);
                foreach ($giornoMap[$dow] as $giorno) {
                    $slots->push([
                        'data'             => $cursor->copy(),
                        'giorno'           => $giorno,
                        'seduta'           => $seduta,
                        'feedback_inviato' => $seduta && in_array($seduta->id, $feedbackInviati),
                    ]);
                }
            }
            $cursor->addDay();
        }

        // Raggruppa per inizio settimana (lunedì)
        $settimane = $slots->groupBy(
            fn($s) => $s['data']->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d')
        );

        return view('atleta.calendario.index', compact('settimane', 'stagione'));
    }
}
