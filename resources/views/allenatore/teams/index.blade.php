@extends('layouts.allenatore')
@section('title', 'I miei Team')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>I miei Team</h2>
    <a href="{{ route('allenatore.teams.create') }}" class="btn btn-primary">+ Nuovo team</a>
</div>

<div class="row g-3">
    @forelse($teams as $team)
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>{{ $team->nome }}</h5>
                <p class="text-muted mb-1">{{ $team->sport->nome }} · {{ $team->stagione }}</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('allenatore.teams.show', $team) }}" class="btn btn-sm btn-outline-primary">Gestisci</a>
                    <form action="{{ route('allenatore.teams.destroy', $team) }}" method="POST"
                          onsubmit="return confirm('Eliminare il team?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">Nessun team ancora. Crea il primo!</div>
    </div>
    @endforelse
</div>
@endsection
