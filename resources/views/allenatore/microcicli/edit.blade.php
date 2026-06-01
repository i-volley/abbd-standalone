@extends('layouts.allenatore')
@section('title', 'Modifica Microciclo')

@section('content')
@php $macrociclo = $microciclo->macrociclo; @endphp

<h2 class="mb-1">Modifica Microciclo — Settimana {{ $microciclo->numero }}</h2>
<small class="text-muted d-block mb-4">
    Macrociclo: <a href="{{ route('allenatore.macrocicli.show', $macrociclo) }}">{{ $macrociclo->nome }}</a>
</small>

<form action="{{ route('allenatore.microcicli.update', $microciclo) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3" style="max-width:400px">

        <div class="col-6">
            <label class="form-label">Numero settimana *</label>
            <input type="number" name="numero" class="form-control"
                   value="{{ old('numero', $microciclo->numero) }}" min="1" required>
        </div>

        <div class="col-6">
            <label class="form-label">Data inizio *</label>
            <input type="date" name="data_inizio" class="form-control"
                   value="{{ old('data_inizio', $microciclo->data_inizio->format('Y-m-d')) }}" required>
        </div>

        <div class="col-12">
            <label class="form-label">Intensità *</label>
            <select name="intensita" class="form-select" required>
                @foreach(['bassa' => 'Bassa', 'media' => 'Media', 'alta' => 'Alta', 'scarico' => 'Scarico'] as $v => $l)
                    <option value="{{ $v }}" {{ old('intensita', $microciclo->intensita) === $v ? 'selected' : '' }}>
                        {{ $l }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Note</label>
            <textarea name="note" class="form-control" rows="2">{{ old('note', $microciclo->note) }}</textarea>
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salva modifiche</button>
            <a href="{{ route('allenatore.macrocicli.show', $macrociclo) }}"
               class="btn btn-outline-secondary">Annulla</a>
            <form action="{{ route('allenatore.microcicli.destroy', $microciclo) }}" method="POST"
                  class="ms-auto"
                  data-confirm="Eliminare il microciclo settimana {{ $microciclo->numero }}?">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">Elimina</button>
            </form>
        </div>
    </div>
</form>
@endsection
