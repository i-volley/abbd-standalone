@extends('layouts.allenatore')
@section('title', $esercizio->nome)

@section('content')
@php
$labFaseGioco  = ['cambio_palla' => 'Cambio palla', 'break_point' => 'Break point', 'ricostruzione' => 'Ricostruzione'];
$labFaseGiocoBadge = ['cambio_palla' => 'bg-info text-dark', 'break_point' => 'bg-danger', 'ricostruzione' => 'bg-warning text-dark'];
$labRuoli      = ['alzatore' => 'Alzatore', 'ricevitore_attaccante' => 'Schiacciatore', 'centrale' => 'Centrale', 'opposto' => 'Opposto', 'libero' => 'Libero'];
$metodBadge    = ['analitico' => 'bg-primary', 'sintetico' => 'bg-warning text-dark', 'globale' => 'bg-success'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">{{ $esercizio->nome }}</h2>
        <div class="d-flex flex-wrap gap-1">
            <span class="badge {{ $metodBadge[$esercizio->metodologia] ?? 'bg-secondary' }}">{{ strtoupper($esercizio->metodologia) }}</span>
            <span class="badge bg-secondary">{{ $esercizio->fase }}</span>
            @if($esercizio->categoria_eta)
                <x-badge-categoria-eta :categoria="$esercizio->categoria_eta" />
            @endif
            @if($esercizio->fase_gioco)
                <span class="badge {{ $labFaseGiocoBadge[$esercizio->fase_gioco] ?? 'bg-secondary' }}">
                    {{ $labFaseGioco[$esercizio->fase_gioco] }}
                </span>
            @endif
            @foreach($esercizio->ruoli as $r)
                <span class="badge bg-dark rounded-pill" style="font-size:.7rem">{{ $labRuoli[$r->ruolo] ?? $r->ruolo }}</span>
            @endforeach
        </div>
    </div>
    <a href="{{ route('allenatore.esercizi.edit', $esercizio) }}" class="btn btn-outline-secondary">{{ __('Modifica') }}</a>
</div>

<div class="row g-4">
    {{-- ── PARAMETRI BASE ─────────────────────────────────────────────────── --}}
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2 bg-transparent">
                <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem;letter-spacing:.07em">{{ __('Parametri') }}</small>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">{{ __('Fase seduta') }}</dt>
                    <dd class="col-7"><span class="badge bg-secondary">{{ $esercizio->fase }}</span></dd>

                    <dt class="col-5">{{ __('Metodologia') }}</dt>
                    <dd class="col-7">
                        <span class="badge {{ $metodBadge[$esercizio->metodologia] ?? 'bg-secondary' }}">
                            {{ ucfirst($esercizio->metodologia) }}
                        </span>
                    </dd>

                    <dt class="col-5">{{ __('Gesto tecnico') }}</dt>
                    <dd class="col-7">{{ $esercizio->gestoTecnico?->nome ?? '—' }}</dd>

                    <dt class="col-5">{{ __('Durata') }}</dt>
                    <dd class="col-7">{{ $esercizio->durata_min }} min</dd>

                    @if($esercizio->n_salti > 0)
                    <dt class="col-5">{{ __('N. Salti') }}</dt>
                    <dd class="col-7">{{ $esercizio->n_salti }}</dd>
                    @endif

                    @if($esercizio->n_gesti > 0)
                    <dt class="col-5">{{ __('N. Gesti') }}</dt>
                    <dd class="col-7">{{ $esercizio->n_gesti }}</dd>
                    @endif

                    @if($esercizio->n_giocatori)
                    <dt class="col-5">{{ __('N. Giocatori') }}</dt>
                    <dd class="col-7">{{ $esercizio->n_giocatori }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- ── ASSI METODOLOGICI ──────────────────────────────────────────────── --}}
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2 bg-transparent">
                <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem;letter-spacing:.07em">{{ __('Assi metodologici FIPAV') }}</small>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    @if($esercizio->obiettivo)
                    <dt class="col-5">{{ __('Obiettivo') }}</dt>
                    <dd class="col-7">{{ ucfirst($esercizio->obiettivo) }}</dd>
                    @endif

                    @if($esercizio->fase_seduta)
                    <dt class="col-5">{{ __('Fase (didattica)') }}</dt>
                    <dd class="col-7">{{ ucfirst($esercizio->fase_seduta) }}</dd>
                    @endif

                    @if($esercizio->fase_gioco)
                    <dt class="col-5">{{ __('Fase di gioco') }}</dt>
                    <dd class="col-7">
                        <span class="badge {{ $labFaseGiocoBadge[$esercizio->fase_gioco] ?? 'bg-secondary' }}">
                            {{ $labFaseGioco[$esercizio->fase_gioco] }}
                        </span>
                    </dd>
                    @endif

                    @if($esercizio->componente)
                    <dt class="col-5">{{ __('Componente') }}</dt>
                    <dd class="col-7">{{ ucfirst($esercizio->componente) }}</dd>
                    @endif

                    @if($esercizio->rendimento)
                    <dt class="col-5">{{ __('Rendimento') }}</dt>
                    <dd class="col-7">{{ str_replace('_', ' ', ucfirst($esercizio->rendimento)) }}</dd>
                    @endif

                    @if($esercizio->livello)
                    <dt class="col-5">{{ __('Livello') }}</dt>
                    <dd class="col-7">{{ ucfirst($esercizio->livello) }}</dd>
                    @endif

                    <dt class="col-5">{{ __('Ruoli') }}</dt>
                    <dd class="col-7">
                        @if($esercizio->ruoli->isEmpty())
                            <span class="text-muted">{{ __('Tutti') }}</span>
                        @else
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($esercizio->ruoli as $r)
                                    <span class="badge bg-dark rounded-pill" style="font-size:.7rem">{{ $labRuoli[$r->ruolo] ?? $r->ruolo }}</span>
                                @endforeach
                            </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- ── CAPACITÀ + VIDEO ───────────────────────────────────────────────── --}}
    <div class="col-md-6">
        @if($esercizio->capacita->count())
        <div class="card shadow-sm mb-3">
            <div class="card-header py-2 bg-transparent">
                <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem;letter-spacing:.07em">{{ __('Capacità allenate') }}</small>
            </div>
            <div class="card-body d-flex flex-wrap gap-2">
                @foreach($esercizio->capacita as $c)
                    <x-badge-capacita :capacita="$c" />
                @endforeach
            </div>
        </div>
        @endif

        @if($esercizio->video_url)
        <div class="card shadow-sm">
            <div class="card-body">
                <a href="{{ $esercizio->video_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                    {{ __('▶ Guarda il video') }}
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- ── CAMPO DI GIOCO + NOTE ─────────────────────────────────────────── --}}
    @if($esercizio->campo_visivo || $esercizio->descrizione)
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-2 bg-transparent">
                <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem;letter-spacing:.07em">
                    {{ __('🏐 Campo di gioco') }}
                    @if($esercizio->descrizione) {{ __('· Note metodologiche') }} @endif
                </small>
            </div>
            <div class="card-body p-0">
                <div class="d-flex flex-column flex-md-row gap-0">
                    {{-- Preview campo --}}
                    @if($esercizio->campo_visivo)
                    <div style="flex:0 0 auto;width:100%;max-width:480px;min-width:200px">
                        @include('allenatore.esercizi._campo-preview', [
                            'campoPreview' => $esercizio->campo_visivo,
                            'previewKey'   => 'show-' . $esercizio->id,
                        ])
                    </div>
                    @endif
                    {{-- Note --}}
                    @if($esercizio->descrizione)
                    <div class="p-3 flex-grow-1" style="white-space:pre-wrap;font-size:.92rem;min-width:0">{{ $esercizio->descrizione }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
