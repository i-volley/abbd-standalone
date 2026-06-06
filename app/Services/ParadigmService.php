<?php

namespace App\Services;

use App\Models\FeedbackQuestion;
use App\Models\SessionTemplate;
use App\Models\User;
use Illuminate\Support\Collection;

class ParadigmService
{
    /**
     * Template di sistema appropriato per il paradigma del coach.
     */
    public function getSessionTemplate(User $coach): ?SessionTemplate
    {
        return SessionTemplate::system()
            ->forParadigm($coach->paradigm ?? 'traditional')
            ->with('blocks')
            ->first();
    }

    /**
     * Domande di feedback adattate al paradigma.
     * Hybrid: bilancia in base a paradigm_weight_ecological.
     */
    public function getAdaptedFeedbackQuestions(User $coach): Collection
    {
        $paradigm = $coach->paradigm ?? 'traditional';

        if ($paradigm === 'hybrid') {
            $weight = $coach->paradigm_weight_ecological ?? 0;
            // < 30% → tradizionale, > 70% → ecologico, else → entrambi
            $paradigm = $weight < 30 ? 'traditional' : ($weight > 70 ? 'ecological' : 'both');
            return FeedbackQuestion::active()
                ->whereIn('paradigm', ['both', 'traditional', 'ecological'])
                ->orderBy('position')
                ->get();
        }

        return FeedbackQuestion::active()
            ->forParadigm($paradigm)
            ->orderBy('position')
            ->get();
    }

    /**
     * Contesto per il prompt AI — include paradigma, tono, note metodologiche.
     */
    public function getAISuggestionContext(User $coach): array
    {
        $paradigm = $coach->paradigm ?? 'traditional';
        $weight   = $coach->paradigm_weight_ecological ?? 0;

        $notes = match($paradigm) {
            'traditional' => 'Coach uses prescriptive technique model. Suggest drill-based, repetitive exercises with clear technical cues.',
            'ecological'  => 'Coach uses ecological dynamics / CLA. Suggest constraint manipulation, variable practice, representative tasks. Avoid prescribing movement solutions.',
            'hybrid'      => 'Coach uses mixed approach. Balance analytical drills with constraints-led tasks. Weight ecological: ' . $weight . '%.',
            default       => '',
        };

        return [
            'paradigm'          => $paradigm,
            'tone'              => $coach->ai_suggestion_tone ?? 'directive',
            'methodology_notes' => $notes,
        ];
    }

    /**
     * Ordina gli esercizi per rilevanza paradigmatica senza escludere nulla.
     */
    public function filterExercisesForCoach(User $coach, Collection $exercises): Collection
    {
        $paradigm = $coach->paradigm ?? 'traditional';

        return $exercises->sortBy(function ($ex) use ($paradigm) {
            if ($ex->paradigm_primary === $paradigm) return 0;
            if ($ex->paradigm_primary === 'neutral')  return 1;
            return 2;
        })->values();
    }

    public function getParadigmLabel(string $paradigm): string
    {
        return match($paradigm) {
            'traditional' => 'Tradizionale',
            'ecological'  => 'Ecologico',
            'hybrid'      => 'Ibrido',
            default       => $paradigm,
        };
    }

    public function getBlockTypeLabel(string $blockType, string $paradigm = 'traditional'): string
    {
        return \App\Models\SessionTemplateBlock::blockTypeLabel($blockType, $paradigm);
    }

    /** Etichette per constraint_type */
    public static function constraintLabel(string $type): string
    {
        return match($type) {
            'organism'    => 'Organismo',
            'task'        => 'Compito',
            'environment' => 'Ambiente',
            'none'        => '—',
            default       => $type,
        };
    }

    /** Etichette per exercise_category */
    public static function categoryLabel(string $cat): string
    {
        return match($cat) {
            'analytic'    => 'Analitico',
            'situational' => 'Situazionale',
            'game_form'   => 'Forma di gioco',
            'free_play'   => 'Gioco libero',
            default       => $cat,
        };
    }

    /** Etichette per representativeness */
    public static function representativenessLabel(string $r): string
    {
        return match($r) {
            'low'    => 'Bassa',
            'medium' => 'Media',
            'high'   => 'Alta',
            default  => $r,
        };
    }
}
