<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\SessionTemplate;
use App\Services\ParadigmService;
use Illuminate\Http\Request;

class ParadigmaController extends Controller
{
    public function __construct(private ParadigmService $paradigm) {}

    public function settings()
    {
        $coach = auth()->user();
        return view('allenatore.paradigma.settings', compact('coach'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'paradigm'                  => 'required|in:traditional,ecological,hybrid',
            'paradigm_weight_ecological'=> 'required_if:paradigm,hybrid|integer|min:0|max:100',
            'feedback_style'            => 'required|in:prescriptive,interrogative,mixed',
            'ai_suggestion_tone'        => 'required|in:directive,explorative,neutral',
            'preferred_session_blocks'  => 'required|integer|min:2|max:12',
        ]);

        auth()->user()->update($data);

        return redirect()->route('allenatore.paradigma.settings')
            ->with('success', 'Paradigma aggiornato.');
    }

    public function listTemplates()
    {
        $templates = SessionTemplate::system()->with('blocks')->get()->groupBy('paradigm');
        $coach     = auth()->user();
        return view('allenatore.paradigma.templates', compact('templates', 'coach'));
    }

    public function previewTemplate(SessionTemplate $template)
    {
        $template->load('blocks');
        $coach = auth()->user();
        return view('allenatore.paradigma.template-preview', compact('template', 'coach'));
    }
}
