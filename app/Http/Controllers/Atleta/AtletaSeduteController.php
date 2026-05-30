<?php

namespace App\Http\Controllers\Atleta;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Seduta;

class AtletaSeduteController extends Controller
{
    private function teamId(): int
    {
        return auth()->user()->teams()->value('teams.id') ?? 0;
    }

    public function index()
    {
        $teamId = $this->teamId();
        $sedute = Seduta::where('team_id', $teamId)
            ->where('visibile_atleti', true)
            ->orderByDesc('data')
            ->get();

        $feedbackInviati = Feedback::where('atleta_id', auth()->id())
            ->whereIn('seduta_id', $sedute->pluck('id'))
            ->pluck('seduta_id')->toArray();

        return view('atleta.sedute.index', compact('sedute', 'feedbackInviati'));
    }

    public function show(Seduta $seduta)
    {
        abort_unless($seduta->visibile_atleti && $seduta->team_id === $this->teamId(), 403);

        $seduta->load(['sedutaEsercizi.esercizio.capacita']);

        $haFeedback = Feedback::where('seduta_id', $seduta->id)
            ->where('atleta_id', auth()->id())->exists();

        return view('atleta.sedute.show', compact('seduta', 'haFeedback'));
    }

    public function storico()
    {
        $teamId = $this->teamId();
        $feedback = Feedback::where('atleta_id', auth()->id())
            ->with('seduta')
            ->latest()
            ->paginate(20);

        return view('atleta.storico.index', compact('feedback'));
    }
}
