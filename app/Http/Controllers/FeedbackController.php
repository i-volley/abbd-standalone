<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\FeedbackEsercizio;
use App\Models\Seduta;
use App\Models\SedutaEsercizio;
use App\Services\PassportWebhookService;

class FeedbackController extends Controller
{
    public function store(StoreFeedbackRequest $request, PassportWebhookService $passport)
    {
        $data = $request->validated();

        $esiste = Feedback::where('seduta_id', $data['seduta_id'])
            ->where('atleta_id', auth()->id())->exists();

        if ($esiste) {
            return back()->with('error', 'Hai già inviato il feedback per questa seduta.');
        }

        $seduta     = Seduta::findOrFail($data['seduta_id']);
        $inScadenza = !$seduta->scadenza_feedback || now()->lte($seduta->scadenza_feedback);

        $feedback = Feedback::create([
            'seduta_id'                   => $data['seduta_id'],
            'atleta_id'                   => auth()->id(),
            'rpe'                         => $data['rpe'],
            'qualita_prestazione'         => $data['qualita_prestazione'],
            'impegno_squadra'             => $data['impegno_squadra'],
            'miglioramento_fondamentale'  => $data['miglioramento_fondamentale'],
            'nota'                        => $data['nota'] ?? null,
            'inviato_in_scadenza'         => $inScadenza,
        ]);

        foreach ($data['gradimento_esercizio'] ?? [] as $pivotId => $voto) {
            $se = SedutaEsercizio::where('id', $pivotId)
                ->where('seduta_id', $feedback->seduta_id)
                ->where('voto_abilitato', true)
                ->first();

            if ($se) {
                FeedbackEsercizio::create([
                    'feedback_id'         => $feedback->id,
                    'seduta_esercizio_id' => $pivotId,
                    'atleta_id'           => auth()->id(),
                    'gradimento'          => $voto,
                ]);
            }
        }

        // --- Integrazione Passport/Gamification ---
        // Il feedback è stato compilato dall'atleta e contiene l'RPE: notifica
        // entrambi gli eventi al modulo Passport (no-op se il modulo è inattivo).
        $passport->feedbackCompiled(auth()->id(), [
            'seduta_id'           => $feedback->seduta_id,
            'feedback_id'         => $feedback->id,
            'inviato_in_scadenza' => $feedback->inviato_in_scadenza,
            'rpe'                 => $feedback->rpe,
        ]);

        $passport->rpeRegistered(auth()->id(), [
            'seduta_id'   => $feedback->seduta_id,
            'feedback_id' => $feedback->id,
            'rpe'         => $feedback->rpe,
        ]);

        return redirect()->route('atleta.sedute')->with('success', 'Feedback inviato. Grazie!');
    }
}
