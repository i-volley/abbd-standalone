@extends('layouts.allenatore')
@section('title', __('Modifica Seduta'))

@section('content')
<h2 class="mb-4">{{ __('Modifica Seduta') }}: {{ $seduta->titolo }}</h2>

<form action="{{ route('allenatore.sedute.update', $seduta) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">{{ __('Titolo') }}</label>
            <input type="text" name="titolo" class="form-control" value="{{ old('titolo', $seduta->titolo) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Data *') }}</label>
            <input type="date" name="data" class="form-control" value="{{ old('data', $seduta->data->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Scadenza feedback') }}</label>
            <input type="datetime-local" name="scadenza_feedback" class="form-control"
                   value="{{ old('scadenza_feedback', $seduta->scadenza_feedback?->format('Y-m-d\TH:i')) }}">
        </div>

        {{-- Template guida --}}
        <div class="col-12">
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
                        <option value="{{ $tpl->id }}"
                            {{ old('session_template_id', $seduta->session_template_id) == $tpl->id ? 'selected' : '' }}>
                            {{ $tpl->name }}@if($tpl->blocks->sum('suggested_duration_minutes')) ({{ $tpl->blocks->sum('suggested_duration_minutes') }}')@endif
                        </option>
                        @endforeach
                    </optgroup>
                    @endif
                    @if($sysTpls->isNotEmpty())
                    <optgroup label="{{ __('Template di sistema') }}">
                        @foreach($sysTpls as $tpl)
                        <option value="{{ $tpl->id }}"
                            {{ old('session_template_id', $seduta->session_template_id) == $tpl->id ? 'selected' : '' }}>
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
            </div>
            @endif
        </div>

        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="visibile_atleti" class="form-check-input" id="visibile"
                       {{ $seduta->visibile_atleti ? 'checked' : '' }}>
                <label class="form-check-label" for="visibile">{{ __('Visibile agli atleti') }}</label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('Note allenatore') }}</label>
            <textarea name="note_allenatore" class="form-control" rows="3">{{ old('note_allenatore', $seduta->note_allenatore) }}</textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>
            <a href="{{ route('allenatore.sedute.show', $seduta) }}" class="btn btn-outline-secondary ms-2">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>
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
