@extends('layouts.allenatore')
@section('title', __('Modifica Unità Didattica'))

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<h2 class="mb-4">{{ __('Modifica') }}: {{ $unitaDidattica->titolo }}</h2>

<form action="{{ route('allenatore.unita-didattiche.update', $unitaDidattica) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">{{ __('Titolo *') }}</label>
            <input type="text" name="titolo" class="form-control" value="{{ old('titolo', $unitaDidattica->titolo) }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('Obiettivo permanente') }} *</label>
            <textarea name="obiettivo_permanente" class="form-control" rows="3" required>{{ old('obiettivo_permanente', $unitaDidattica->obiettivo_permanente) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Progressione metodologica *') }}</label>
            <select name="progressione" class="form-select" required>
                @foreach($progressioni as $val => $lab)
                    <option value="{{ $val }}" {{ old('progressione', $unitaDidattica->progressione) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Team *') }}</label>
            <select name="team_id" class="form-select" required>
                @foreach($teams as $t)
                    <option value="{{ $t->id }}" {{ old('team_id', $unitaDidattica->team_id) == $t->id ? 'selected' : '' }}>{{ $t->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Data inizio') }}</label>
            <input type="date" name="data_inizio" class="form-control" value="{{ old('data_inizio', $unitaDidattica->data_inizio?->format('Y-m-d')) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Microciclo') }}</label>
            <select name="microciclo_id" class="form-select">
                <option value="">{{ __('– non specificato –') }}</option>
                @foreach($microcicli as $m)
                    <option value="{{ $m->id }}" {{ old('microciclo_id', $unitaDidattica->microciclo_id) == $m->id ? 'selected' : '' }}>
                        {{ __('Settimana') }} {{ $m->numero }} — {{ $m->data_inizio->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('Note') }}</label>
            <textarea name="note" class="form-control" rows="2">{{ old('note', $unitaDidattica->note) }}</textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ __('Salva modifiche') }}</button>
            <a href="{{ route('allenatore.unita-didattiche.show', $unitaDidattica) }}" class="btn btn-outline-secondary ms-2">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>

<hr class="mt-4">
<form action="{{ route('allenatore.unita-didattiche.destroy', $unitaDidattica) }}" method="POST"
      data-confirm="Eliminare questa unità? Le sedute collegate diventeranno indipendenti.">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Elimina unità didattica') }}</button>
</form>
</div>
</div>
@endsection
