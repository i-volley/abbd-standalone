@extends('layouts.allenatore')
@section('title', 'I miei Team')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>I miei Team</h2>
    <a href="{{ route('allenatore.teams.create') }}" class="btn btn-primary">+ Nuovo team</a>
</div>

@if(session('current_team_id'))
<div class="alert alert-info d-flex align-items-center gap-2 mb-4" style="font-size:.9rem">
    <span>👥</span>
    <span>
        Team attivo: <strong>{{ session('current_team_nome') }}</strong>.
        Clicca <strong>Entra</strong> su un altro team per cambiarlo.
    </span>
</div>
@endif

<div class="row g-3">
    @forelse($teams as $team)
    <div class="col-md-4">
        <div class="card shadow-sm h-100 {{ session('current_team_id') == $team->id ? 'border-primary' : '' }}">
            @if(session('current_team_id') == $team->id)
            <div class="card-header bg-primary text-white py-1" style="font-size:.75rem;font-weight:600">
                ✓ Team attivo
            </div>
            @endif
            <div class="card-body">
                <h5 class="mb-1">{{ $team->nome }}</h5>
                <p class="text-muted small mb-3">{{ $team->sport->nome }} · {{ $team->stagione }}</p>

                {{-- Entra: imposta contesto team --}}
                <a href="{{ route('allenatore.teams.entra', $team) }}"
                   class="btn btn-sm {{ session('current_team_id') == $team->id ? 'btn-primary' : 'btn-outline-primary' }} w-100 mb-2">
                    {{ session('current_team_id') == $team->id ? '✓ Team attivo' : 'Entra →' }}
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('allenatore.teams.edit', $team) }}"
                       class="btn btn-sm btn-outline-secondary">Modifica</a>
                    <a href="{{ route('allenatore.teams.show', $team) }}"
                       class="btn btn-sm btn-outline-secondary flex-grow-1">Gestisci atleti</a>
                    <form action="{{ route('allenatore.teams.destroy', $team) }}" method="POST"
                          data-confirm="Eliminare il team {{ addslashes($team->nome) }}?">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">×</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">
            Nessun team ancora.
            <a href="{{ route('allenatore.teams.create') }}">Crea il primo →</a>
        </div>
    </div>
    @endforelse
</div>
@endsection
