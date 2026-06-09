@extends('layouts.allenatore')
@section('title', __('Tipi Allenamento'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">⚙️ {{ __('Tipi di allenamento') }}</h2>
    <a href="{{ route('allenatore.parametri.index') }}" class="btn btn-outline-secondary btn-sm">← {{ __('Impostazioni') }}</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($teams->isEmpty())
    <div class="alert alert-warning">
        {{ __('Nessun team trovato.') }}
        <a href="{{ route('allenatore.teams.create') }}">{{ __('Crea il primo team') }}</a>.
    </div>
@else

{{-- Selettore team --}}
@if($teams->count() > 1)
<div class="mb-3">
    <label class="form-label fw-semibold small">{{ __('Team') }}</label>
    <div class="d-flex flex-wrap gap-2">
        @foreach($teams as $t)
        <a href="{{ route('allenatore.tipo-allenamento.index', ['team_id' => $t->id]) }}"
           class="btn btn-sm {{ isset($team) && $team->id === $t->id ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $t->nome }}
        </a>
        @endforeach
    </div>
</div>
@endif

@if(isset($team))
<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent fw-semibold">
        {{ __('Tipi configurati per') }}: <strong>{{ $team->nome }}</strong>
    </div>
    <div class="card-body p-0">
        @forelse($tipi as $tipo)
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <form action="{{ route('allenatore.tipo-allenamento.update', $tipo) }}" method="POST"
                  class="d-flex align-items-center gap-2 flex-grow-1 me-3">
                @csrf @method('PATCH')
                <input type="hidden" name="team_id" value="{{ $team->id }}">
                <input type="text" name="nome" value="{{ $tipo->nome }}"
                       class="form-control form-control-sm" style="max-width:250px">
                <button class="btn btn-sm btn-outline-primary">{{ __('Salva') }}</button>
            </form>
            <form action="{{ route('allenatore.tipo-allenamento.destroy', $tipo) }}" method="POST"
                  data-confirm="Delete type «{{ addslashes($tipo->nome) }}»?">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">×</button>
            </form>
        </div>
        @empty
        <p class="text-muted px-3 py-2 mb-0">{{ __('Nessun tipo configurato.') }}</p>
        @endforelse
    </div>

    <div class="card-footer bg-light">
        <form action="{{ route('allenatore.tipo-allenamento.store') }}" method="POST"
              class="d-flex gap-2 align-items-center">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="text" name="nome" class="form-control form-control-sm"
                   placeholder="{{ __('Nuovo tipo (es. Crossfit, Tennis...)') }}" required maxlength="100" style="max-width:300px">
            <button class="btn btn-success btn-sm">{{ __('+ Aggiungi') }}</button>
        </form>
    </div>
</div>
@endif

<div class="alert alert-info small">
    <strong>{{ __('Tipi predefiniti') }}</strong>: Training, Weight Room, Pool, Beach Court — already present when the team is created. You can rename them or add new ones.
</div>

@endif
@endsection
