@extends('layouts.allenatore')
@section('title', __('Nuova Seduta'))

@section('content')
<h2 class="mb-4">{{ __('Nuova Seduta') }}</h2>

<form action="{{ route('allenatore.sedute.store') }}" method="POST" id="formSeduta">
@csrf
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">{{ __('Titolo *') }}</label>
        <input type="text" name="titolo" class="form-control" required value="{{ old('titolo') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('Data *') }}</label>
        <input type="date" name="data" class="form-control" required value="{{ old('data', request('data', date('Y-m-d'))) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('Luogo') }}</label>
        <input type="text" name="luogo" class="form-control" placeholder="{{ __('es. Palestra A') }}"
               value="{{ old('luogo', request('luogo')) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">{{ __('Team *') }}</label>
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

{{-- Parametri seduta --}}
<div class="row g-3 mb-3">
    <div class="col-md-1">
        <label class="form-label">{{ __('N. campi') }}</label>
        <input type="number" name="n_campi" class="form-control" min="1" max="6"
               value="{{ old('n_campi', 1) }}" title="{{ __('Campi di gioco simultanei (1-6)') }}">
    </div>
    <div class="col-md-1">
        <label class="form-label">{{ __('N. atlete') }}</label>
        <input type="number" name="n_atlete" class="form-control" min="1" max="100"
               value="{{ old('n_atlete') }}" placeholder="es. 12">
    </div>
    <div class="col-md-5">
        <label class="form-label">{{ __('Obiettivo principale') }}</label>
        <input type="text" name="obiettivo_principale" class="form-control"
               value="{{ old('obiettivo_principale') }}"
               placeholder="{{ __('es. Ricezione + contrattacco') }}">
    </div>
    <div class="col-md-5">
        <label class="form-label">{{ __('Obiettivo secondario') }}</label>
        <input type="text" name="obiettivo_secondario" class="form-control"
               value="{{ old('obiettivo_secondario') }}"
               placeholder="{{ __('es. Gestione del punto da seconda linea') }}">
    </div>
</div>

{{-- Template suggerito dal paradigma --}}
@php $suggestedTemplate = auth()->user()->getPreferredSessionTemplate(); @endphp
@if($suggestedTemplate)
<div class="alert alert-secondary d-flex align-items-center gap-3 mb-3" style="font-size:.9rem">
    <span>🧠 {{ __('Template suggerito per il tuo paradigma:') }}
        <strong>{{ $suggestedTemplate->name }}</strong></span>
    <a href="{{ route('allenatore.paradigma.preview', $suggestedTemplate) }}"
       class="btn btn-sm btn-outline-secondary ms-auto" target="_blank">{{ __('Anteprima') }}</a>
</div>
@endif

{{-- Collegamento unità didattica (opzionale) --}}
@if($unitaDidattiche->isNotEmpty())
<div class="row g-3 mb-3">
    <div class="col-md-7">
        <label class="form-label">{{ __('Collega a unità didattica') }} <small class="text-muted">({{ __('opzionale') }})</small></label>
        <select name="unita_didattica_id" class="form-select">
            <option value="">{{ __('– nessuna –') }}</option>
            @foreach($unitaDidattiche as $u)
                <option value="{{ $u->id }}"
                    {{ (old('unita_didattica_id', request('unita_didattica_id')) == $u->id) ? 'selected' : '' }}>
                    {{ $u->titolo }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-5">
        <label class="form-label">{{ __('Obiettivo di questa seduta') }}</label>
        <input type="text" name="obiettivo_seduta" class="form-control"
               value="{{ old('obiettivo_seduta') }}"
               placeholder="{{ __('Obiettivo principale (variabile)') }}">
    </div>
</div>
@endif

<button type="submit" class="btn btn-outline-secondary mb-4">{{ __('Crea seduta bozza e apri costruttore') }}</button>
</form>

<p class="text-muted">{{ __('Dopo aver creato la bozza potrai aggiungere gli esercizi.') }}</p>
@endsection
