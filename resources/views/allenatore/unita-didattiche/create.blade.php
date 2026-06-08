@extends('layouts.allenatore')
@section('title', __('Nuova Unità Didattica'))

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<h2 class="mb-1">{{ __('Nuova unità didattica') }}</h2>
<p class="text-muted small mb-4">
    Dal Manuale FIPAV Primo Grado, Metodologia 1-6: <em>«Obiettivo permanente costante — obiettivo principale variabile»</em>
</p>

<form action="{{ route('allenatore.unita-didattiche.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">{{ __('Titolo *') }}</label>
            <input type="text" name="titolo" class="form-control" value="{{ old('titolo') }}"
                   placeholder="Es. Blocco ricezione-attacco settimana 3" required>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Obiettivo permanente') }} * <small class="text-muted">({{ __('fisso per tutte le sedute di questa unità') }})</small></label>
            <textarea name="obiettivo_permanente" class="form-control" rows="3" required
                      placeholder="Es. Stabilizzare la ricezione in zona 1-6 con orientamento alla zona 2-3 dell'alzatore">{{ old('obiettivo_permanente') }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('Progressione metodologica *') }}</label>
            <select name="progressione" class="form-select" required>
                @foreach($progressioni as $val => $lab)
                    <option value="{{ $val }}" {{ old('progressione', 'analitico_globale') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                @endforeach
            </select>
            <div class="form-text">{{ __('Sequenza delle metodologie seduta per seduta nell\'unità.') }}</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('Team *') }}</label>
            <select name="team_id" class="form-select" required>
                @foreach($teams as $t)
                    <option value="{{ $t->id }}" {{ old('team_id', $defaultTeamId ?? '') == $t->id ? 'selected' : '' }}>{{ $t->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('Data inizio') }}</label>
            <input type="date" name="data_inizio" class="form-control" value="{{ old('data_inizio', date('Y-m-d')) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('Microciclo') }} ({{ __('opzionale') }})</label>
            <select name="microciclo_id" class="form-select">
                <option value="">{{ __('– non specificato –') }}</option>
                @foreach($microcicli as $m)
                    <option value="{{ $m->id }}" {{ old('microciclo_id') == $m->id ? 'selected' : '' }}>
                        {{ __('Settimana') }} {{ $m->numero }} — {{ $m->data_inizio->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Note') }}</label>
            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ __('Crea unità didattica') }}</button>
            <a href="{{ route('allenatore.unita-didattiche.index') }}" class="btn btn-outline-secondary ms-2">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>
</div>
</div>
@endsection
