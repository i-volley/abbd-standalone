@extends('layouts.allenatore')
@section('title', 'Nuova Stagione')

@section('content')
<h2 class="mb-4">Nuova Stagione</h2>
<form action="{{ route('allenatore.stagioni.store') }}" method="POST">
    @csrf
    <div class="row g-3" style="max-width:500px">
        <div class="col-12">
            <label class="form-label">Team</label>
            <select name="team_id" class="form-select" required>
                @foreach($teams as $t)
                <option value="{{ $t->id }}"
                    {{ old('team_id', $defaultTeamId ?? '') == $t->id ? 'selected' : '' }}>
                    {{ $t->nome }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome','Stagione 2024-2025') }}" required>
        </div>
        <div class="col-6">
            <label class="form-label">Inizio</label>
            <input type="date" name="data_inizio" class="form-control" value="{{ old('data_inizio') }}" required>
        </div>
        <div class="col-6">
            <label class="form-label">Fine</label>
            <input type="date" name="data_fine" class="form-control" value="{{ old('data_fine') }}" required>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="attiva" class="form-check-input" id="attiva">
                <label class="form-check-label" for="attiva">Stagione attiva</label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Crea stagione</button>
            <a href="{{ route('allenatore.stagioni.index') }}" class="btn btn-outline-secondary ms-2">Annulla</a>
        </div>
    </div>
</form>
@endsection
