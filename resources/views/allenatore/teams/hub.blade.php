@extends('layouts.allenatore')
@section('title', $team->nome)

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="mb-0">{{ $team->nome }}</h2>
        <small class="text-muted">{{ $team->sport->nome }} · Stagione {{ $team->stagione }}</small>
    </div>
    <a href="{{ route('allenatore.teams.show', $team) }}" class="btn btn-sm btn-outline-secondary">
        Gestisci atleti
    </a>
</div>

{{-- ── ACCESSO RAPIDO ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <a href="{{ route('allenatore.stagioni.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2" style="transition:.15s;cursor:pointer"
                 onmouseover="this.style.boxShadow='0 .5rem 1.5rem rgba(0,0,0,.12)'"
                 onmouseout="this.style.boxShadow=''">
                <div style="font-size:2rem">📅</div>
                <div class="fw-semibold mt-1">Pianificazione</div>
                <small class="text-muted">Stagioni · Macrocicli · Microcicli</small>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('allenatore.sedute.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2" style="transition:.15s;cursor:pointer"
                 onmouseover="this.style.boxShadow='0 .5rem 1.5rem rgba(0,0,0,.12)'"
                 onmouseout="this.style.boxShadow=''">
                <div style="font-size:2rem">🏐</div>
                <div class="fw-semibold mt-1">Sedute</div>
                <small class="text-muted">Allenamenti · Feedback</small>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('allenatore.unita-didattiche.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2" style="transition:.15s;cursor:pointer"
                 onmouseover="this.style.boxShadow='0 .5rem 1.5rem rgba(0,0,0,.12)'"
                 onmouseout="this.style.boxShadow=''">
                <div style="font-size:2rem">📚</div>
                <div class="fw-semibold mt-1">Unità Didattiche</div>
                <small class="text-muted">Obiettivi · Progressione</small>
            </div>
        </a>
    </div>
</div>

{{-- ── ATLETI ───────────────────────────────────────────────────────────────── --}}
@if($team->atleti->isNotEmpty())
<div class="mb-4">
    <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.72rem;letter-spacing:.08em">
        Atleti ({{ $team->atleti->count() }})
    </h6>
    <div class="d-flex flex-wrap gap-2">
        @foreach($team->atleti as $atleta)
        <span class="badge bg-light text-dark border" style="font-size:.8rem">{{ $atleta->name }}</span>
        @endforeach
    </div>
</div>
@endif

{{-- ── SEDUTE RECENTI ──────────────────────────────────────────────────────── --}}
@if($seduteRecenti->isNotEmpty())
<h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.72rem;letter-spacing:.08em">
    Sedute recenti
</h6>
@php
    $statoColore = ['bozza' => '#94a3b8', 'pubblicata' => '#3b82f6', 'completata' => '#10b981'];
@endphp
<div class="row g-2 mb-3">
    @foreach($seduteRecenti as $s)
    <div class="col-md-6">
        <a href="{{ route('allenatore.sedute.show', $s) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm" style="border-left:3px solid {{ $statoColore[$s->stato] ?? '#64748b' }} !important;border-left-style:solid !important">
                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="fw-semibold text-dark d-block">{{ $s->titolo }}</small>
                        <small class="text-muted">{{ $s->data->format('d/m/Y') }}</small>
                    </div>
                    <span class="badge rounded-pill" style="background:{{ $statoColore[$s->stato] ?? '#64748b' }};font-size:.65rem">
                        {{ ucfirst($s->stato) }}
                    </span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
<a href="{{ route('allenatore.sedute.index') }}" class="btn btn-sm btn-outline-primary">
    Tutte le sedute →
</a>
@else
<div class="alert alert-light border">
    Nessuna seduta ancora.
    <a href="{{ route('allenatore.sedute.create') }}">Crea la prima →</a>
</div>
@endif

@endsection
