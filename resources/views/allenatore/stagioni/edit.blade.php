@extends('layouts.allenatore')
@section('title', 'Modifica Stagione')

@section('content')
<h2 class="mb-4">Modifica — {{ $stagione->nome }}</h2>

<form action="{{ route('allenatore.stagioni.update', $stagione) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3" style="max-width:500px">

        <div class="col-12">
            <label class="form-label">Nome stagione *</label>
            <input type="text" name="nome" class="form-control"
                   value="{{ old('nome', $stagione->nome) }}" required>
        </div>

        <div class="col-12">
            <label class="form-label">Team</label>
            <select name="team_id" class="form-select" required>
                @foreach($teams as $t)
                    <option value="{{ $t->id }}" {{ $stagione->team_id == $t->id ? 'selected' : '' }}>
                        {{ $t->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-6">
            <label class="form-label">Inizio *</label>
            <input type="date" name="data_inizio" class="form-control"
                   value="{{ old('data_inizio', $stagione->data_inizio->format('Y-m-d')) }}" required>
        </div>

        <div class="col-6">
            <label class="form-label">Fine *</label>
            <input type="date" name="data_fine" class="form-control"
                   value="{{ old('data_fine', $stagione->data_fine->format('Y-m-d')) }}" required>
        </div>

        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="attiva" class="form-check-input" id="attiva" value="1"
                       {{ old('attiva', $stagione->attiva) ? 'checked' : '' }}>
                <label class="form-check-label" for="attiva">Stagione attiva</label>
            </div>
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salva modifiche</button>
            <a href="{{ route('allenatore.stagioni.index') }}" class="btn btn-outline-secondary">Annulla</a>
        </div>
    </div>
</form>
@endsection
