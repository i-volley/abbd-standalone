<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Seduta;
use App\Models\Team;
use App\Services\InsightService;
use Illuminate\Support\Facades\DB;

class AllenatoreDashboardController extends Controller
{
    public function index()
    {
        // Nessun team → redirect diretto alla creazione (evita 500 da variabili mancanti)
        $hasTeam = Team::where('allenatore_id', auth()->id())->exists();
        if (!$hasTeam) {
            return redirect()->route('allenatore.teams.create')
                ->with('info', 'Benvenuto! Crea il tuo primo team per iniziare.');
        }

        $team = Team::where('allenatore_id', auth()->id())->with('sport')->first();

        $stats          = [];
        $ultimeFeedback = collect();
        $rpePerSeduta   = collect();

        if ($team) {
            $stats['totale_sedute']   = Seduta::where('team_id', $team->id)->count();
            $stats['sedute_visibili'] = Seduta::where('team_id', $team->id)->where('visibile_atleti', true)->count();
            $stats['totale_feedback'] = Feedback::whereHas('seduta', fn($q) => $q->where('team_id', $team->id))->count();

            $medie = Feedback::whereHas('seduta', fn($q) => $q->where('team_id', $team->id))
                ->selectRaw('AVG(rpe) as avg_rpe, AVG(qualita_prestazione) as avg_qualita, AVG(impegno_squadra) as avg_impegno')
                ->first();

            $stats['avg_rpe']      = round($medie->avg_rpe ?? 0, 1);
            $stats['avg_qualita']  = round($medie->avg_qualita ?? 0, 1);
            $stats['avg_impegno']  = round($medie->avg_impegno ?? 0, 1);

            $ultimeFeedback = Feedback::whereHas('seduta', fn($q) => $q->where('team_id', $team->id))
                ->with(['seduta', 'atleta'])
                ->latest()
                ->take(10)
                ->get();

            $rpePerSeduta = Seduta::where('team_id', $team->id)
                ->where('visibile_atleti', true)
                ->withAvg('feedback', 'rpe')
                ->orderBy('data')
                ->take(10)
                ->get();
        }

        $insights = (new InsightService())->forAllenatore(auth()->id());

        return view('allenatore.dashboard', compact('team', 'stats', 'ultimeFeedback', 'rpePerSeduta', 'insights'));
    }
}
