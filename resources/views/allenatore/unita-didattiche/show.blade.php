@extends('layouts.allenatore')
@section('title', $unitaDidattica->titolo)

@section('content')
@php
$metodBadge = ['analitico'=>'bg-primary','sintetico'=>'bg-warning text-dark','globale'=>'bg-success'];
$progLabel  = \App\Models\UnitaDidattica::progressioni()[$unitaDidattica->progressione] ?? '';
@endphp

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="mb-1">{{ $unitaDidattica->titolo }}</h2>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span class="badge bg-secondary">{{ $progLabel }}</span>
            @if($unitaDidattica->data_inizio)
                <small class="text-muted">📅 {{ $unitaDidattica->data_inizio->format('d/m/Y') }}</small>
            @endif
            @if($unitaDidattica->team)
                <small class="text-muted">🏐 {{ $unitaDidattica->team->nome }}</small>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('allenatore.sedute.create') }}?unita_didattica_id={{ $unitaDidattica->id }}" class="btn btn-success">+ Aggiungi seduta</a>
        <a href="{{ route('allenatore.unita-didattiche.edit', $unitaDidattica) }}" class="btn btn-outline-secondary">Modifica</a>
    </div>
</div>

{{-- Obiettivo permanente --}}
<div class="alert alert-primary border-start border-4 border-primary rounded-0 mb-4">
    <small class="fw-semibold text-uppercase text-primary" style="font-size:.7rem;letter-spacing:.07em">Obiettivo permanente (costante)</small>
    <p class="mb-0 mt-1">{{ $unitaDidattica->obiettivo_permanente }}</p>
</div>

{{-- Progressione metodologica attesa --}}
@if(count($sequenza) > 0)
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-2">
        <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">Progressione attesa</small>
        <div class="d-flex gap-2 mt-2 flex-wrap align-items-center">
            @foreach($sequenza as $i => $metod)
                <div class="text-center">
                    <div class="badge {{ $metodBadge[$metod] ?? 'bg-secondary' }} px-3 py-2" style="font-size:.85rem">
                        Seduta {{ $i + 1 }}<br><span style="font-size:.7rem">{{ strtoupper($metod) }}</span>
                    </div>
                </div>
                @if(!$loop->last)
                    <span class="text-muted fs-5">→</span>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Sedute dell'unità --}}
<h5 class="fw-bold mb-3">Sedute ({{ $unitaDidattica->sedute->count() }})</h5>

@forelse($unitaDidattica->sedute as $i => $s)
@php
$metodologiePrev = $sequenza[$i] ?? null;
$metodologie = $s->sedutaEsercizi->map(fn($se) => $se->esercizio?->metodologia)->unique()->filter()->values();
$inLinea = !$metodologiePrev || $metodologie->contains($metodologiePrev);
@endphp
<div class="card shadow-sm border-0 mb-2 {{ !$inLinea ? 'border-warning border-opacity-50' : '' }}">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-3">
            <div class="text-center" style="min-width:2rem">
                <div class="fw-bold text-muted" style="font-size:.8rem">{{ $i + 1 }}</div>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <strong>
                        <a href="{{ route('allenatore.sedute.show', $s) }}" class="text-decoration-none">{{ $s->titolo }}</a>
                    </strong>
                    <small class="text-muted">{{ $s->data->format('d/m/Y') }}</small>
                    @foreach($metodologie as $m)
                        <span class="badge {{ $metodBadge[$m] ?? 'bg-secondary' }} rounded-pill" style="font-size:.65rem">{{ strtoupper($m) }}</span>
                    @endforeach
                    @if(!$inLinea)
                        <span class="badge bg-warning text-dark rounded-pill" style="font-size:.65rem" title="Metodologia attesa: {{ $metodologiePrev }}">
                            ⚠️ Non in linea con progressione
                        </span>
                    @endif
                </div>
                @if($s->obiettivo_seduta)
                    <small class="text-muted">🎯 {{ $s->obiettivo_seduta }}</small>
                @endif
            </div>
            <div>
                <small class="text-muted">{{ $s->sedutaEsercizi->count() }} es. · {{ $s->durata_tot_min }} min</small>
            </div>
        </div>
    </div>
</div>
@empty
<p class="text-muted">Nessuna seduta collegata. <a href="{{ route('allenatore.sedute.create') }}?unita_didattica_id={{ $unitaDidattica->id }}">Aggiungi la prima</a>.</p>
@endforelse

@if($unitaDidattica->note)
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header py-2 bg-transparent">
        <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem">Note</small>
    </div>
    <div class="card-body">{{ $unitaDidattica->note }}</div>
</div>
@endif
@endsection
