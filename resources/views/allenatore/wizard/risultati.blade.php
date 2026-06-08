@extends('layouts.allenatore')
@section('title', __('Risultati Wizard'))

@section('content')
@php
$metodBadge     = ['analitico' => 'bg-primary', 'sintetico' => 'bg-warning text-dark', 'globale' => 'bg-success'];
$faseGiocoBadge = ['cambio_palla' => 'bg-info text-dark', 'break_point' => 'bg-danger', 'ricostruzione' => 'bg-warning text-dark'];
$faseGiocoLab   = ['cambio_palla' => 'Cambio palla', 'break_point' => 'Break point', 'ricostruzione' => 'Ricostruzione'];
$ruoloLab       = ['alzatore' => 'ALZ', 'ricevitore_attaccante' => 'SCH', 'centrale' => 'CEN', 'opposto' => 'OPP', 'libero' => 'LIB'];
@endphp

{{-- ── DIAGNOSI ─────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid #0d6efd !important; border-left-width: 4px !important">
    <div class="card-body">
        <div class="d-flex align-items-start gap-3">
            <span style="font-size:2rem">🔍</span>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <h5 class="mb-0 fw-bold">{{ __('Diagnosi FIPAV') }}</h5>
                    <span class="badge {{ $metodBadge[$diagnosi['metodologia']] }}">{{ strtoupper($diagnosi['metodologia']) }}</span>
                    @if($diagnosi['componente'])
                        <span class="badge bg-secondary">{{ $diagnosi['componente'] }}</span>
                    @endif
                    <small class="text-muted">{{ $diagnosi['slide'] }}</small>
                </div>
                <p class="mb-2">{{ $diagnosi['spiegazione'] }}</p>
                <blockquote class="border-start border-primary ps-3 mb-0">
                    <small class="text-muted fst-italic">«{{ $diagnosi['citazione'] }}»</small>
                </blockquote>
            </div>
        </div>
    </div>
</div>

{{-- ── FILTRI AGGIUNTIVI ────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form action="{{ route('allenatore.wizard.risultati') }}" method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="sintomo" value="{{ $sintomo }}">
            <div class="col-auto">
                <label class="form-label small mb-1">{{ __('Gesto tecnico') }}</label>
                <select name="gesto_tecnico_id" class="form-select form-select-sm">
                    <option value="tutti">{{ __('Tutti') }}</option>
                    @foreach($gesti as $g)
                        <option value="{{ $g->id }}" {{ request('gesto_tecnico_id') == $g->id ? 'selected' : '' }}>{{ $g->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">{{ __('Fase di gioco') }}</label>
                <select name="fase_gioco" class="form-select form-select-sm">
                    @foreach(['tutti' => __('Tutte'), 'cambio_palla' => __('Cambio palla'), 'break_point' => __('Break point'), 'ricostruzione' => __('Ricostruzione')] as $v => $l)
                        <option value="{{ $v }}" {{ request('fase_gioco', 'tutti') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">{{ __('Ruolo') }}</label>
                <select name="ruolo" class="form-select form-select-sm">
                    <option value="tutti" {{ request('ruolo', 'tutti') === 'tutti' ? 'selected' : '' }}>{{ __('Tutti') }}</option>
                    @foreach(['alzatore' => __('Alzatore'), 'ricevitore_attaccante' => __('Schiacciatore'), 'centrale' => __('Centrale'), 'opposto' => __('Opposto'), 'libero' => __('Libero')] as $v => $l)
                        <option value="{{ $v }}" {{ request('ruolo') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">{{ __('Aggiorna') }}</button>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('allenatore.wizard.index') }}" class="btn btn-sm btn-outline-secondary">← {{ __('Nuova diagnosi') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- ── RISULTATI ─────────────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center gap-2 mb-3">
    <h5 class="mb-0 fw-bold">{{ __('Esercizi prescritti') }}</h5>
    <span class="badge bg-dark rounded-pill">{{ $esercizi->count() }}</span>
    @if($esercizi->isEmpty())
        <small class="text-muted">{{ __('Prova ad allargare i filtri') }}</small>
    @endif
</div>

@forelse($esercizi as $e)
<div class="card mb-2 shadow-sm border-0">
    <div class="card-body py-2 px-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                    <strong>{{ $e->nome }}</strong>
                    <span class="badge {{ $metodBadge[$e->metodologia] ?? 'bg-secondary' }} rounded-pill" style="font-size:.7rem">{{ strtoupper($e->metodologia) }}</span>
                    <span class="badge bg-secondary rounded-pill" style="font-size:.7rem">{{ $e->fase }}</span>
                    @if($e->fase_gioco)
                        <span class="badge {{ $faseGiocoBadge[$e->fase_gioco] ?? 'bg-secondary' }} rounded-pill" style="font-size:.7rem">{{ $faseGiocoLab[$e->fase_gioco] ?? $e->fase_gioco }}</span>
                    @endif
                    @foreach($e->ruoli as $r)
                        <span class="badge bg-dark rounded-pill" style="font-size:.65rem">{{ $ruoloLab[$r->ruolo] ?? $r->ruolo }}</span>
                    @endforeach
                    @if($e->categoria_eta)
                        <x-badge-categoria-eta :categoria="$e->categoria_eta" />
                    @endif
                </div>
                <div class="small text-muted d-flex gap-3 flex-wrap mb-1">
                    @if($e->gestoTecnico)<span>📌 {{ $e->gestoTecnico->nome }}</span>@endif
                    <span>⏱ {{ $e->durata_min }} min</span>
                    @if($e->n_salti > 0)<span>↕ {{ $e->n_salti }} salti</span>@endif
                    @if($e->n_gesti > 0)<span>✋ {{ $e->n_gesti }} gesti</span>@endif
                    @if($e->n_giocatori)<span>👥 {{ $e->n_giocatori }}</span>@endif
                    @if($e->livello)<span class="text-capitalize">Lv: {{ $e->livello }}</span>@endif
                </div>
                @if($e->descrizione)
                    <p class="small text-muted mb-1">{{ Str::limit($e->descrizione, 120) }}</p>
                @endif
                <div class="d-flex flex-wrap gap-1">
                    @foreach($e->capacita as $c)
                        <x-badge-capacita :capacita="$c" />
                    @endforeach
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <a href="{{ route('allenatore.esercizi.show', $e) }}" class="btn btn-sm btn-outline-primary">{{ __('Scheda') }}</a>
                <a href="{{ route('allenatore.esercizi.edit', $e) }}" class="btn btn-sm btn-outline-secondary">{{ __('Modifica') }}</a>
            </div>
        </div>
    </div>
</div>
@empty
<div class="alert alert-warning">
    <strong>Nessun esercizio trovato</strong> con questi filtri.
    <a href="{{ route('allenatore.esercizi.create') }}">Crea il primo esercizio prescritto</a> e taggalo con metodologia <strong>{{ $diagnosi['metodologia'] }}</strong>.
</div>
@endforelse

@endsection
