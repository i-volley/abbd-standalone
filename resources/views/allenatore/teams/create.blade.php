@extends('layouts.allenatore')
@section('title', 'Nuovo Team')

@section('content')
<h2 class="mb-4">Nuovo Team</h2>
<form action="{{ route('allenatore.teams.store') }}" method="POST">
    @csrf
    <div class="row g-3" style="max-width:500px">
        <div class="col-12">
            <label class="form-label">Sport</label>
            <select name="sport_id" class="form-select" required>
                @foreach($sports as $s)
                    <option value="{{ $s->id }}">{{ $s->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Nome team</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required placeholder="es. Under 18 Femminile">
        </div>
        <div class="col-12">
            <label class="form-label">Stagione</label>
            <input type="text" name="stagione" class="form-control" value="{{ old('stagione') }}" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Crea team</button>
            <a href="{{ route('allenatore.teams.index') }}" class="btn btn-outline-secondary ms-2">Annulla</a>
        </div>
    </div>
</form>
@endsection
