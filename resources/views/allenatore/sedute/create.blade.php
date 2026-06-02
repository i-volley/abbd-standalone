@extends('layouts.allenatore')
@section('title', 'Nuova Seduta')

@section('content')
<h2 class="mb-4">Nuova Seduta</h2>

<form action="{{ route('allenatore.sedute.store') }}" method="POST" id="formSeduta">
@csrf
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Titolo *</label>
        <input type="text" name="titolo" class="form-control" required value="{{ old('titolo') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Data *</label>
        <input type="date" name="data" class="form-control" required value="{{ old('data', request('data', date('Y-m-d'))) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Luogo</label>
        <input type="text" name="luogo" class="form-control" placeholder="es. Palestra A"
               value="{{ old('luogo', request('luogo')) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Team *</label>
        <select name="team_id" class="form-select" required>
            @foreach($teams as $t)
                <option value="{{ $t->id }}"
                    {{ old('team_id', $defaultTeamId ?? '') == $t->id ? 'selected' : '' }}>
                    {{ $t->nome }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Collegamento unità didattica (opzionale) --}}
@if($unitaDidattiche->isNotEmpty())
<div class="row g-3 mb-3">
    <div class="col-md-7">
        <label class="form-label">Collega a unità didattica <small class="text-muted">(opzionale)</small></label>
        <select name="unita_didattica_id" class="form-select">
            <option value="">– nessuna –</option>
            @foreach($unitaDidattiche as $u)
                <option value="{{ $u->id }}"
                    {{ (old('unita_didattica_id', request('unita_didattica_id')) == $u->id) ? 'selected' : '' }}>
                    {{ $u->titolo }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-5">
        <label class="form-label">Obiettivo di questa seduta</label>
        <input type="text" name="obiettivo_seduta" class="form-control"
               value="{{ old('obiettivo_seduta') }}"
               placeholder="Obiettivo principale (variabile)">
    </div>
</div>
@endif

<button type="submit" class="btn btn-outline-secondary mb-4">Crea seduta bozza e apri costruttore</button>
</form>

<p class="text-muted">Dopo aver creato la bozza potrai aggiungere gli esercizi.</p>
@endsection
