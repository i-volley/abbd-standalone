@extends('layouts.allenatore')
@section('title', 'Nuovo Macrociclo')

@section('content')
<h2 class="mb-4">Nuovo Macrociclo — {{ $stagione->nome }}</h2>
<form action="{{ route('allenatore.stagioni.macrocicli.store', $stagione) }}" method="POST">
    @csrf
    <div class="row g-3" style="max-width:500px">
        <div class="col-12">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome','Pre-campionato') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Fase</label>
            <select name="fase" class="form-select" required>
                @foreach(['preparazione','competizione','transizione'] as $f)
                    <option value="{{ $f }}">{{ ucfirst($f) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <label class="form-label">Inizio</label>
            <input type="date" name="data_inizio" class="form-control" required>
        </div>
        <div class="col-6">
            <label class="form-label">Fine</label>
            <input type="date" name="data_fine" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Obiettivi</label>
            <textarea name="obiettivi" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Crea macrociclo</button>
            <a href="{{ route('allenatore.stagioni.show', $stagione) }}" class="btn btn-outline-secondary ms-2">Annulla</a>
        </div>
    </div>
</form>
@endsection
