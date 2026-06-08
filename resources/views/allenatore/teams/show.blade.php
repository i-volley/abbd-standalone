@extends('layouts.allenatore')
@section('title', $team->nome)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-1">
    <div>
        <h2 class="mb-0">{{ $team->nome }}</h2>
        <p class="text-muted mb-0">{{ $team->sport->nome }} · {{ $team->stagione }}</p>
    </div>
    <a href="{{ route('allenatore.teams.edit', $team) }}" class="btn btn-sm btn-outline-secondary">{{ __('Modifica team') }}</a>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">{{ __('Atleti nel team') }} ({{ $team->atleti->count() }})</div>
            <div class="card-body">
                @forelse($team->atleti as $atleta)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $atleta->name }} <small class="text-muted">{{ $atleta->email }}</small></span>
                    <form action="{{ route('allenatore.teams.atleti.remove', [$team, $atleta]) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">{{ __('Rimuovi') }}</button>
                    </form>
                </div>
                @empty
                <p class="text-muted">{{ __('Nessun atleta nel team.') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">{{ __('Aggiungi atleta') }}</div>
            <div class="card-body">
                <form action="{{ route('allenatore.teams.atleti.add', $team) }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2">
                        <select name="user_id" class="form-select" required>
                            <option value="">{{ __('Scegli atleta...') }}</option>
                            @foreach($atleti as $a)
                                @unless($team->atleti->contains($a))
                                <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->email }})</option>
                                @endunless
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">{{ __('Aggiungi') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
