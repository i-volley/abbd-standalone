@extends('layouts.allenatore')
@section('title', $unitaDidattica->titolo)

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            @if($unitaDidattica->colore)
            <span class="rounded-pill d-inline-block flex-shrink-0"
                  style="width:.85rem;height:.85rem;background:{{ $unitaDidattica->colore }};box-shadow:0 0 0 1px rgba(0,0,0,.12)"></span>
            @endif
            <h2 class="mb-0">{{ $unitaDidattica->titolo }}</h2>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            @if($unitaDidattica->data_inizio)
                <small class="text-muted">📅 {{ $unitaDidattica->data_inizio->format('d/m/Y') }}
                    @if($unitaDidattica->data_fine)
                        → {{ $unitaDidattica->data_fine->format('d/m/Y') }}
                    @endif
                </small>
            @endif
            @if($unitaDidattica->team)
                <small class="text-muted">🏐 {{ $unitaDidattica->team->nome }}</small>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('allenatore.sedute.create') }}?unita_didattica_id={{ $unitaDidattica->id }}" class="btn btn-success">{{ __('+ Aggiungi seduta') }}</a>
        <a href="{{ route('allenatore.unita-didattiche.edit', $unitaDidattica) }}" class="btn btn-outline-secondary">{{ __('Modifica') }}</a>
    </div>
</div>

{{-- Obiettivo permanente --}}
<div class="alert alert-primary border-start border-4 border-primary rounded-0 mb-4">
    <small class="fw-semibold text-uppercase text-primary" style="font-size:.7rem;letter-spacing:.07em">{{ __('Obiettivo permanente (costante)') }}</small>
    <p class="mb-0 mt-1">{{ $unitaDidattica->obiettivo_permanente }}</p>
</div>

{{-- Sedute dell'unità --}}
<h5 class="fw-bold mb-3">{{ __('Sedute') }} ({{ $unitaDidattica->sedute->count() }})</h5>

@forelse($unitaDidattica->sedute as $i => $s)
<div class="card shadow-sm border-0 mb-2">
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
                    <x-stato-seduta :stato="$s->stato" />
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
<p class="text-muted">{{ __('Nessuna seduta ancora.') }}
    <a href="{{ route('allenatore.sedute.create') }}?unita_didattica_id={{ $unitaDidattica->id }}">{{ __('Aggiungi la prima') }}</a>.
</p>
@endforelse

@if($unitaDidattica->note)
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header py-2 bg-transparent">
        <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem">{{ __('Note') }}</small>
    </div>
    <div class="card-body">{{ $unitaDidattica->note }}</div>
</div>
@endif
@endsection
