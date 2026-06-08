@extends('layouts.allenatore')
@section('title', __('Tipologia di allenamento'))

@push('styles')
<style>
.paradigm-card {
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color .2s, box-shadow .2s;
}
.paradigm-card:hover { box-shadow: 0 0 0 3px rgba(0,0,0,.08); }
.paradigm-card.selected-traditional { border-color: #3b82f6; }
.paradigm-card.selected-ecological  { border-color: #10b981; }
.paradigm-card.selected-hybrid      { border-color: #f59e0b; }
#hybrid-weight-section { display: none; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h2>{{ __('Tipologia di allenamento') }}</h2>
    <p class="text-muted">{{ __('Scegli il tuo approccio metodologico. Non esclude nessun esercizio — cambia template, filtri, domande di feedback e tono AI.') }}</p>
</div>

<form action="{{ route('allenatore.paradigma.update') }}" method="POST">
@csrf

{{-- ── Selezione tipologia ─────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @php
        $paradigms = [
            'traditional' => ['label'=>'🔵 Tradizionale','color'=>'primary','desc'=>'Tecnica prescrittiva, drill analitici, feedback correttivo. Struttura FIPAV classica.'],
            'ecological'  => ['label'=>'🟢 Ecologico',   'color'=>'success','desc'=>'Constraints-Led Approach, variabilità, Representative Learning Design. Apprendimento per scoperta.'],
            'hybrid'      => ['label'=>'🟡 Ibrido',      'color'=>'warning','desc'=>'Mix configurabile dei due approcci. Bilancia drill analitici e vincoli ecologici.'],
        ];
    @endphp

    @foreach($paradigms as $value => $p)
    <div class="col-md-4">
        <div class="card paradigm-card {{ ($coach->paradigm ?? 'traditional') === $value ? 'selected-'.$value : '' }}"
             onclick="selectParadigm('{{ $value }}')">
            <div class="card-body">
                <h5 class="card-title">{{ $p['label'] }}</h5>
                <p class="card-text small text-muted">{{ $p['desc'] }}</p>
                <span class="badge bg-{{ $p['color'] }}">{{ $value }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<input type="hidden" name="paradigm" id="paradigm-input" value="{{ $coach->paradigm ?? 'traditional' }}">

{{-- ── Peso ecologico (solo hybrid) ───────────────────────────────────────── --}}
<div id="hybrid-weight-section" class="card mb-4 border-warning">
    <div class="card-body">
        <label class="form-label fw-semibold">{{ __('Peso approccio ecologico') }}:
            <span id="weight-display">{{ $coach->paradigm_weight_ecological ?? 0 }}</span>%
        </label>
        <input type="range" name="paradigm_weight_ecological" class="form-range"
               min="0" max="100" step="5"
               value="{{ $coach->paradigm_weight_ecological ?? 0 }}"
               oninput="document.getElementById('weight-display').textContent=this.value">
        <div class="d-flex justify-content-between small text-muted mt-1">
            <span>0% = tutto tradizionale</span>
            <span>100% = tutto ecologico</span>
        </div>
    </div>
</div>

{{-- ── Stile feedback ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ __('Stile feedback agli atleti') }}</label>
        <select name="feedback_style" class="form-select">
            <option value="prescriptive"  {{ ($coach->feedback_style ?? 'prescriptive')  === 'prescriptive'  ? 'selected' : '' }}>
                Prescrittivo — "Fai così"
            </option>
            <option value="interrogative" {{ ($coach->feedback_style ?? '') === 'interrogative' ? 'selected' : '' }}>
                Interrogativo — "Cosa hai percepito?"
            </option>
            <option value="mixed"         {{ ($coach->feedback_style ?? '') === 'mixed' ? 'selected' : '' }}>
                Misto — combina entrambi
            </option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ __('Tono suggerimenti AI') }}</label>
        <select name="ai_suggestion_tone" class="form-select">
            <option value="directive"   {{ ($coach->ai_suggestion_tone ?? 'directive')   === 'directive'   ? 'selected' : '' }}>
                Direttivo — soluzioni tecniche precise
            </option>
            <option value="explorative" {{ ($coach->ai_suggestion_tone ?? '') === 'explorative' ? 'selected' : '' }}>
                Esplorativo — domande e scenari aperti
            </option>
            <option value="neutral"     {{ ($coach->ai_suggestion_tone ?? '') === 'neutral' ? 'selected' : '' }}>
                Neutro — bilanciato
            </option>
        </select>
    </div>
</div>

{{-- ── Blocchi preferiti ───────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label fw-semibold">{{ __('Numero blocchi preferito per seduta') }}</label>
        <input type="number" name="preferred_session_blocks" class="form-control"
               min="2" max="12" value="{{ $coach->preferred_session_blocks ?? 6 }}">
        <div class="form-text">{{ __('Il template suggerito userà questo numero di blocchi.') }}</div>
    </div>
</div>

<div class="d-flex align-items-center gap-3">
    <button type="submit" class="btn btn-primary">{{ __('Salva tipologia') }}</button>
    <a href="{{ route('allenatore.paradigma.templates') }}" class="btn btn-outline-secondary">
        📋 {{ __('Vedi template disponibili') }}
    </a>
    <a href="{{ route('allenatore.paradigma.template-custom.index') }}" class="btn btn-outline-success">
        📝 {{ __('I miei template') }}
    </a>
</div>
</form>

{{-- ── Preview domande feedback ────────────────────────────────────────────── --}}
<div class="card mt-4">
    <div class="card-header">{{ __('Domande feedback attive per la tua tipologia') }}</div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush" id="feedback-questions-preview">
            @foreach($coach->getActiveFeedbackQuestions() as $q)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="small">{{ $q->question_text }}</span>
                <div class="d-flex gap-1">
                    <span class="badge bg-{{ $q->paradigm === 'both' ? 'secondary' : ($q->paradigm === 'traditional' ? 'primary' : 'success') }}">
                        {{ $q->paradigm === 'both' ? 'Sempre' : ($q->paradigm === 'traditional' ? 'Tradizionale' : 'Ecologico') }}
                    </span>
                    <span class="badge bg-light text-dark">{{ $q->question_type }}</span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectParadigm(val) {
    document.getElementById('paradigm-input').value = val;
    document.querySelectorAll('.paradigm-card').forEach(c => {
        c.classList.remove('selected-traditional','selected-ecological','selected-hybrid');
    });
    event.currentTarget.classList.add('selected-' + val);
    document.getElementById('hybrid-weight-section').style.display =
        val === 'hybrid' ? '' : 'none';
}

// Mostra slider hybrid se già selezionato
if (document.getElementById('paradigm-input').value === 'hybrid') {
    document.getElementById('hybrid-weight-section').style.display = '';
}
</script>
@endpush
