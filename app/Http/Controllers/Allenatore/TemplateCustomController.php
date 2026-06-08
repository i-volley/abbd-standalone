<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\SessionTemplate;
use App\Models\SessionTemplateBlock;
use Illuminate\Http\Request;

class TemplateCustomController extends Controller
{
    public function index()
    {
        $templates = SessionTemplate::where('created_by', auth()->id())
            ->where('is_system', false)
            ->with('blocks')
            ->latest()
            ->get();

        return view('allenatore.paradigma.template-custom.index', compact('templates'));
    }

    public function create()
    {
        return view('allenatore.paradigma.template-custom.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                                 => 'required|string|max:100',
            'paradigm'                             => 'required|in:traditional,ecological,hybrid',
            'description'                          => 'nullable|string|max:1000',
            'blocks'                               => 'required|array|min:1',
            'blocks.*.block_type'                  => 'required|in:warmup,technical,tactical,ecological_constraint,game_form,cooldown,free',
            'blocks.*.block_name'                  => 'required|string|max:100',
            'blocks.*.block_description'           => 'nullable|string|max:500',
            'blocks.*.suggested_duration_minutes'  => 'nullable|integer|min:1|max:999',
        ]);

        $template = SessionTemplate::create([
            'name'        => $data['name'],
            'paradigm'    => $data['paradigm'],
            'description' => $data['description'] ?? null,
            'is_system'   => false,
            'created_by'  => auth()->id(),
        ]);

        foreach ($data['blocks'] as $i => $b) {
            SessionTemplateBlock::create([
                'session_template_id'        => $template->id,
                'position'                   => $i + 1,
                'block_type'                 => $b['block_type'],
                'block_name'                 => $b['block_name'],
                'block_description'          => $b['block_description'] ?? null,
                'suggested_duration_minutes' => $b['suggested_duration_minutes'] ?? null,
            ]);
        }

        return redirect()->route('allenatore.paradigma.template-custom.index')
            ->with('success', 'Template creato.');
    }

    public function edit(SessionTemplate $template)
    {
        abort_if($template->is_system || $template->created_by !== auth()->id(), 403);
        $template->load('blocks');

        return view('allenatore.paradigma.template-custom.edit', compact('template'));
    }

    public function update(Request $request, SessionTemplate $template)
    {
        abort_if($template->is_system || $template->created_by !== auth()->id(), 403);

        $data = $request->validate([
            'name'                                 => 'required|string|max:100',
            'paradigm'                             => 'required|in:traditional,ecological,hybrid',
            'description'                          => 'nullable|string|max:1000',
            'blocks'                               => 'required|array|min:1',
            'blocks.*.block_type'                  => 'required|in:warmup,technical,tactical,ecological_constraint,game_form,cooldown,free',
            'blocks.*.block_name'                  => 'required|string|max:100',
            'blocks.*.block_description'           => 'nullable|string|max:500',
            'blocks.*.suggested_duration_minutes'  => 'nullable|integer|min:1|max:999',
        ]);

        $template->update([
            'name'        => $data['name'],
            'paradigm'    => $data['paradigm'],
            'description' => $data['description'] ?? null,
        ]);

        $template->blocks()->delete();

        foreach ($data['blocks'] as $i => $b) {
            SessionTemplateBlock::create([
                'session_template_id'        => $template->id,
                'position'                   => $i + 1,
                'block_type'                 => $b['block_type'],
                'block_name'                 => $b['block_name'],
                'block_description'          => $b['block_description'] ?? null,
                'suggested_duration_minutes' => $b['suggested_duration_minutes'] ?? null,
            ]);
        }

        return redirect()->route('allenatore.paradigma.template-custom.index')
            ->with('success', 'Template aggiornato.');
    }

    public function destroy(SessionTemplate $template)
    {
        abort_if($template->is_system || $template->created_by !== auth()->id(), 403);
        $template->delete();

        return redirect()->route('allenatore.paradigma.template-custom.index')
            ->with('success', 'Template eliminato.');
    }
}
