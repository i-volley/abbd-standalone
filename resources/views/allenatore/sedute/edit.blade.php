@extends('layouts.allenatore')
@section('title', __('Modifica Seduta'))

@section('content')
<h2 class="mb-4">{{ __('Modifica Seduta') }}: {{ $seduta->titolo }}</h2>

<form action="{{ route('allenatore.sedute.update', $seduta) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">{{ __('Titolo') }}</label>
            <input type="text" name="titolo" class="form-control" value="{{ old('titolo', $seduta->titolo) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Data *') }}</label>
            <input type="date" name="data" class="form-control" value="{{ old('data', $seduta->data->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Scadenza feedback') }}</label>
            <input type="datetime-local" name="scadenza_feedback" class="form-control"
                   value="{{ old('scadenza_feedback', $seduta->scadenza_feedback?->format('Y-m-d\TH:i')) }}">
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="visibile_atleti" class="form-check-input" id="visibile"
                       {{ $seduta->visibile_atleti ? 'checked' : '' }}>
                <label class="form-check-label" for="visibile">{{ __('Visibile agli atleti') }}</label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('Note allenatore') }}</label>
            <textarea name="note_allenatore" class="form-control" rows="3">{{ old('note_allenatore', $seduta->note_allenatore) }}</textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>
            <a href="{{ route('allenatore.sedute.show', $seduta) }}" class="btn btn-outline-secondary ms-2">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>
@endsection
