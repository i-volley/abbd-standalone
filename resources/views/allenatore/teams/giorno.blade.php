@extends('layouts.allenatore')
@section('title', $giorno->translatedFormat('d M Y'))

@section('content')
@php \Carbon\Carbon::setLocale('it'); @endphp

<div class="d-flex align-items-center gap-2 mb-1">
    <a href="{{ route('allenatore.teams.hub', $team) }}"
       class="btn btn-sm btn-outline-secondary px-2" title="Torna al calendario">‹</a>
    <h4 class="mb-0">{{ $giorno->translatedFormat('l d F Y') }}</h4>
</div>
<p class="text-muted mb-3" style="font-size:.9rem">{{ $team->nome }}</p>

{{-- Periodo / macrociclo del giorno --}}
@if($macrociclo)
<div class="mb-3">
    <span class="badge" style="background:{{ $macrociclo->colore ?? '#4f46e5' }};font-size:.8rem">
        {{ $macrociclo->nome }}
    </span>
    <small class="text-muted ms-1">{{ ucfirst($macrociclo->fase) }}</small>
</div>
@endif

{{-- Sedute del giorno --}}
@forelse($sedute as $s)
<div class="card shadow-sm mb-2">
    <div class="card-body d-flex justify-content-between align-items-center py-3">
        <div>
            <a href="{{ route('allenatore.sedute.show', $s) }}"
               class="fw-semibold text-decoration-none d-block">{{ $s->titolo }}</a>
            <div class="mt-1 d-flex align-items-center gap-2">
                <x-stato-seduta :stato="$s->stato" />
                @if($s->visibile_atleti)
                    <span class="badge bg-success" style="font-size:.7rem">Visibile</span>
                @else
                    <span class="badge bg-light text-dark border" style="font-size:.7rem">Nascosta</span>
                @endif
            </div>
        </div>
        <a href="{{ route('allenatore.sedute.show', $s) }}" class="btn btn-sm btn-outline-primary">Apri</a>
    </div>
</div>
@empty
<div class="alert alert-light border text-center text-muted py-4">
    Nessuna seduta in questo giorno.
</div>
@endforelse

{{-- Nuova seduta su questa data --}}
<a href="{{ route('allenatore.sedute.create', ['data' => $giorno->toDateString()]) }}"
   class="btn btn-primary w-100 mt-2">
    + Nuova seduta il {{ $giorno->format('d/m') }}
</a>
@endsection
