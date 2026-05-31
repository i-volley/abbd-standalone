@extends('layouts.allenatore')
@section('title', 'Unità Didattiche')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Unità Didattiche</h2>
        <p class="text-muted small mb-0">Gruppi di sedute con obiettivo permanente condiviso — Manuale FIPAV, Metodologia 1-6</p>
    </div>
    <a href="{{ route('allenatore.unita-didattiche.create') }}" class="btn btn-primary">+ Nuova unità</a>
</div>

@forelse($unita as $u)
@php
$progLabel = \App\Models\UnitaDidattica::progressioni()[$u->progressione] ?? $u->progressione;
$colori = ['analitico_globale' => 'bg-primary', 'sintetico_globale' => 'bg-warning text-dark', 'libera' => 'bg-secondary'];
@endphp
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h6 class="mb-0 fw-bold">
                        <a href="{{ route('allenatore.unita-didattiche.show', $u) }}" class="text-decoration-none">{{ $u->titolo }}</a>
                    </h6>
                    <span class="badge {{ $colori[$u->progressione] ?? 'bg-secondary' }} rounded-pill" style="font-size:.7rem">{{ $progLabel }}</span>
                    @if($u->data_inizio)
                        <small class="text-muted">📅 {{ $u->data_inizio->format('d/m/Y') }}</small>
                    @endif
                </div>
                <p class="small text-muted mb-2">🎯 {{ Str::limit($u->obiettivo_permanente, 100) }}</p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-light text-dark border">{{ $u->sedute->count() }} sedute</span>
                    @if($u->team)
                        <span class="badge bg-light text-dark border">{{ $u->team->nome }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <a href="{{ route('allenatore.unita-didattiche.show', $u) }}" class="btn btn-sm btn-outline-primary">Vedi</a>
                <a href="{{ route('allenatore.unita-didattiche.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
            </div>
        </div>
    </div>
</div>
@empty
<div class="alert alert-light border text-center py-5">
    <p class="mb-2 text-muted">Nessuna unità didattica creata.</p>
    <a href="{{ route('allenatore.unita-didattiche.create') }}" class="btn btn-primary">Crea la prima unità</a>
</div>
@endforelse

{{ $unita->links() }}
@endsection
