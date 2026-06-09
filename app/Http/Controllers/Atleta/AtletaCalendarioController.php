<?php

namespace App\Http\Controllers\Atleta;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Seduta;
use App\Models\Stagione;

class AtletaCalendarioController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $team = $user->teams()->first();

        if (!$team) {
            return view('atleta.calendario.index', [
                'sedutePerData'   => collect(),
                'giorniSettimana' => collect(),
                'stagioneDates'   => null,
            ]);
        }

        // Stagione attiva o ultima del team
        $stagione = Stagione::where('team_id', $team->id)
            ->where('attiva', true)
            ->with('giorniAllenamento')
            ->first()
            ?? Stagione::where('team_id', $team->id)
                ->with('giorniAllenamento')
                ->orderByDesc('data_inizio')
                ->first();

        // Tutte le sedute visibili all'atleta (senza limite di range — servono per tutta la stagione)
        $sedute = Seduta::where('team_id', $team->id)
            ->where('visibile_atleti', true)
            ->orderBy('data')
            ->get();

        $feedbackInviati = Feedback::where('atleta_id', $user->id)
            ->whereIn('seduta_id', $sedute->pluck('id'))
            ->pluck('seduta_id')
            ->toArray();

        $sedutePerData = $sedute
            ->groupBy(fn($s) => $s->data->format('Y-m-d'))
            ->map(fn($group) => $group->map(fn($s) => [
                'titolo'           => $s->titolo,
                'url'              => route('atleta.sedute.show', $s),
                'feedback_inviato' => in_array($s->id, $feedbackInviati),
            ])->values());

        $stagioneDates = $stagione ? [
            'nome' => $stagione->nome,
            'da'   => $stagione->data_inizio->format('Y-m-d'),
            'a'    => $stagione->data_fine->format('Y-m-d'),
        ] : null;

        // Giorni della settimana con allenamento (0=Dom..6=Sab)
        $giorniSettimana = $stagione
            ? $stagione->giorniAllenamento->pluck('giorno_settimana')->unique()->values()
            : collect();

        return view('atleta.calendario.index', compact('sedutePerData', 'giorniSettimana', 'stagioneDates'));
    }
}
