@extends('layouts.allenatore')
@section('title', __('Modifica Team'))

@section('content')
<h2 class="mb-4">{{ __('Modifica') }} — {{ $team->nome }}</h2>

<form action="{{ route('allenatore.teams.update', $team) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3" style="max-width:500px">

        <div class="col-12">
            <label class="form-label">{{ __('Nome team *') }}</label>
            <input type="text" name="nome" class="form-control"
                   value="{{ old('nome', $team->nome) }}" required>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Sport') }}</label>
            <select name="sport_id" class="form-select">
                @foreach($sports as $s)
                    <option value="{{ $s->id }}" {{ $team->sport_id == $s->id ? 'selected' : '' }}>
                        {{ $s->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Stagione') }}</label>
            <input type="text" name="stagione" class="form-control"
                   value="{{ old('stagione', $team->stagione) }}"
                   placeholder="{{ __('es. 2024-2025') }}">
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ __('Salva modifiche') }}</button>
            <a href="{{ route('allenatore.teams.index') }}" class="btn btn-outline-secondary">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>
@endsection
