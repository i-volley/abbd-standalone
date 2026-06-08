@extends('layouts.allenatore')
@section('title', __('Nuovo Team'))

@section('content')
<h2 class="mb-4">{{ __('Nuovo Team') }}</h2>
<form action="{{ route('allenatore.teams.store') }}" method="POST">
    @csrf
    <div class="row g-3" style="max-width:500px">
        <div class="col-12">
            <label class="form-label">{{ __('Sport') }}</label>
            <select name="sport_id" class="form-select" required>
                @foreach($sports as $s)
                    <option value="{{ $s->id }}">{{ $s->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('Nome team') }}</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required placeholder="{{ __('es. Under 18 Femminile') }}">
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('Stagione') }}</label>
            <input type="text" name="stagione" class="form-control" value="{{ old('stagione') }}" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ __('Crea team') }}</button>
            <a href="{{ route('allenatore.teams.index') }}" class="btn btn-outline-secondary ms-2">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>
@endsection
