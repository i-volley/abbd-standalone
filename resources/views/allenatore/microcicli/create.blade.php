@extends('layouts.allenatore')
@section('title', 'Nuovo Microciclo')

@section('content')
<h2 class="mb-4">Nuovo Microciclo</h2>
<form action="{{ route('allenatore.macrocicli.microcicli.store', $macrociclo) }}" method="POST">
    @csrf
    <div class="row g-3" style="max-width:400px">
        <div class="col-6">
            <label class="form-label">Numero settimana</label>
            <input type="number" name="numero" class="form-control" value="{{ old('numero',1) }}" min="1" required>
        </div>
        <div class="col-6">
            <label class="form-label">Data inizio (lunedì)</label>
            <input type="date" name="data_inizio" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Intensità</label>
            <select name="intensita" class="form-select" required>
                @foreach(['bassa','media','alta','scarico'] as $i)
                    <option value="{{ $i }}">{{ ucfirst($i) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Note</label>
            <textarea name="note" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Crea microciclo</button>
            <a href="{{ route('allenatore.macrocicli.show', $macrociclo) }}" class="btn btn-outline-secondary ms-2">Annulla</a>
        </div>
    </div>
</form>
@endsection
