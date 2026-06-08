@extends('layouts.allenatore')
@section('title', __('Nuova Seduta'))

@section('content')
<h2 class="mb-4">{{ __('Nuova Seduta') }}</h2>

<form action="{{ route('allenatore.sedute.store') }}" method="POST" id="formSeduta">
@csrf
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">{{ __('Titolo *') }}</label>
        <input type="text" name="titolo" class="form-control" required value="{{ old('titolo') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('Data *') }}</label>
        <input type="date" name="data" class="form-control" required value="{{ old('data', request('data', date('Y-m-d'))) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('Luogo') }}</label>
        <input type="text" name="luogo" class="form-control" placeholder="{{ __('es. Palestra A') }}"
               value="{{ old('luogo', request('luogo')) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">{{ __('Team *') }}</label>
        <select name="team_id" class="form-select" required>
            @foreach($teams as $t)
                <option value="{{ $t->id }}"
                    {{ old('team_id', $defaultTeamId ?? '') == $t->id ? 'selected' : '' }}>
                    {{ $t->nome }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Parametri seduta --}}
<div class="row g-3 mb-3">
    <div class="col-md-1">
        <label class="form-label">{{ __('N. campi') }}</label>
        <input type="number" name="n_campi" class="form-control" min="1" max="6"
               value="{{ old('n_campi', 1) }}" title="{{ __('Campi di gioco simultanei (1-6)') }}">
    </div>
    <div class="col-md-1">
        <label class="form-label">{{ __('N. atlete') }}</label>
        <input type="number" name="n_atlete" class="form-control" min="1" max="100"
               value="{{ old('n_atlete') }}" placeholder="es. 12">
    </div>
    <div class="col-md-5">
        <label class="form-label">{{ __('Obiettivo principale') }}</label>
        <input type="text" name="obiettivo_principale" class="form-control"
               value="{{ old('obiettivo_principale') }}"
               placeholder="{{ __('es. Ricezione + contrattacco') }}">
    </div>
    <div class="col-md-5">
        <label class="form-label">{{ __('Obiettivo secondario') }}</label>
        <input type="text" name="obiettivo_secondario" class="form-control"
               value="{{ old('obiettivo_secondario') }}"
               placeholder="{{ __('es. Gestione del punto da seconda linea') }}">
    </div>
</div>

{{-- Selettore template (opzionale) --}}
<div class="mb-3">
    <label class="form-label fw-semibold">📋 {{ __('Template di seduta') }} <small class="text-muted fw-normal">({{ __('opzionale — guida non vincolante nel costruttore') }})</small></label>
    @if($templates->isNotEmpty())
    @php
        $myTpls  = $templates->where('is_system', false);
        $sysTpls = $templates->where('is_system', true);
    @endphp
    <div class="d-flex gap-2 align-items-start flex-wrap">
        <select name="session_template_id" id="tpl-select" class="form-select" style="max-width:380px">
            <option value="">– {{ __('nessun template') }} –</option>
            @if($myTpls->isNotEmpty())
            <optgroup label="{{ __('I miei template') }}">
                @foreach($myTpls as $tpl)
                <option value="{{ $tpl->id }}" {{ old('session_template_id') == $tpl->id ? 'selected' : '' }}>
                    {{ $tpl->name }}@if($tpl->blocks->sum('suggested_duration_minutes')) ({{ $tpl->blocks->sum('suggested_duration_minutes') }}')@endif
                </option>
                @endforeach
            </optgroup>
            @endif
            @if($sysTpls->isNotEmpty())
            <optgroup label="{{ __('Template di sistema') }}">
                @foreach($sysTpls as $tpl)
                <option value="{{ $tpl->id }}" {{ old('session_template_id') == $tpl->id ? 'selected' : '' }}>
                    {{ $tpl->name }}
                </option>
                @endforeach
            </optgroup>
            @endif
        </select>
        <div id="tpl-preview" class="flex-grow-1" style="display:none;min-width:220px"></div>
    </div>
    <div class="mt-1">
        <a href="{{ route('allenatore.paradigma.template-custom.create') }}" class="small text-muted" target="_blank">
            + {{ __('Crea nuovo template') }}
        </a>
    </div>
    @else
    <div class="d-flex align-items-center gap-3 p-2 border rounded" style="background:#f8f9fa">
        <span class="text-muted small">{{ __('Nessun template disponibile.') }}</span>
        <a href="{{ route('allenatore.paradigma.template-custom.create') }}" class="btn btn-sm btn-outline-primary" target="_blank">
            + {{ __('Crea il primo template') }}
        </a>
        <small class="text-muted">{{ __('(apre in nuova scheda — ricarica questa pagina dopo)') }}</small>
    </div>
    @endif
</div>

{{-- Collegamento unità didattica (opzionale) --}}
@if($unitaDidattiche->isNotEmpty())
<div class="row g-3 mb-3">
    <div class="col-md-7">
        <label class="form-label">{{ __('Collega a microciclo') }} <small class="text-muted">({{ __('opzionale') }})</small></label>
        <select name="unita_didattica_id" class="form-select">
            <option value="">{{ __('– nessuna –') }}</option>
            @foreach($unitaDidattiche as $u)
                <option value="{{ $u->id }}"
                    {{ (old('unita_didattica_id', request('unita_didattica_id')) == $u->id) ? 'selected' : '' }}>
                    {{ $u->titolo }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-5">
        <label class="form-label">{{ __('Obiettivo di questa seduta') }}</label>
        <input type="text" name="obiettivo_seduta" class="form-control"
               value="{{ old('obiettivo_seduta') }}"
               placeholder="{{ __('Obiettivo principale (variabile)') }}">
    </div>
</div>
@endif

<button type="submit" class="btn btn-outline-secondary mb-4">{{ __('Crea seduta bozza e apri costruttore') }}</button>
</form>

<p class="text-muted">{{ __('Dopo aver creato la bozza potrai aggiungere gli esercizi.') }}</p>
@endsection

@push('scripts')
@if($templates->isNotEmpty())
<script>
(function() {
    const sel = document.getElementById('tpl-select');
    if (!sel) return;
    const TEMPLATES = @json($templates->keyBy('id')->map(fn($t) => [
        'name'   => $t->name,
        'blocks' => $t->blocks->map(fn($b) => [
            'block_name'                 => $b->block_name,
            'block_type'                 => $b->block_type,
            'suggested_duration_minutes' => $b->suggested_duration_minutes,
        ])->values(),
    ]));

    const TYPE_COLORS = {
        warmup:'#f59e0b', technical:'#3b82f6', tactical:'#06b6d4',
        ecological_constraint:'#10b981', game_form:'#ef4444',
        cooldown:'#6b7280', free:'#1e293b'
    };

    const preview = document.getElementById('tpl-preview');

    function renderPreview(id) {
        if (!id || !TEMPLATES[id]) { preview.style.display = 'none'; preview.innerHTML = ''; return; }
        const tpl = TEMPLATES[id];
        let html = '<div class="d-flex flex-wrap gap-1 align-items-center">';
        tpl.blocks.forEach(function(b, i) {
            const col = TYPE_COLORS[b.block_type] || '#64748b';
            if (i > 0) html += '<span style="color:#adb5bd;font-size:.7rem">→</span>';
            html += `<span style="display:inline-flex;align-items:center;gap:.25rem;background:${col}18;border:1px solid ${col}55;border-radius:.3rem;padding:.15rem .4rem;font-size:.7rem">
                <span style="width:.55rem;height:.55rem;border-radius:50%;background:${col};flex-shrink:0"></span>
                <span>${b.block_name}</span>
                ${b.suggested_duration_minutes ? `<span style="color:#6b7280">${b.suggested_duration_minutes}'</span>` : ''}
            </span>`;
        });
        html += '</div>';
        preview.innerHTML = html;
        preview.style.display = '';
    }

    sel.addEventListener('change', function() { renderPreview(this.value); });
    renderPreview(sel.value);
})();
</script>
@endif
@endpush
