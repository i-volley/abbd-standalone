@extends('layouts.allenatore')
@section('title', 'Nuovo Esercizio')

@section('content')
<h2 class="mb-4">Nuovo Esercizio</h2>

<form action="{{ route('allenatore.esercizi.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Nome *</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Durata (min)</label>
            <input type="number" name="durata_min" class="form-control" value="{{ old('durata_min', 5) }}" min="1">
        </div>

        <div class="col-md-4">
            <label class="form-label">Fase *</label>
            <select name="fase" class="form-select" required>
                <option value="">Scegli...</option>
                @foreach(['riscaldamento','potenziamento','stretching'] as $f)
                    <option value="{{ $f }}" {{ old('fase') === $f ? 'selected' : '' }}>{{ ucfirst($f) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Metodologia *</label>
            <select name="metodologia" class="form-select" required>
                <option value="">Scegli...</option>
                @foreach(['analitico','sintetico','globale'] as $m)
                    <option value="{{ $m }}" {{ old('metodologia') === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Gesto tecnico</label>
            <select name="gesto_tecnico_id" class="form-select">
                <option value="">Nessuno (trasversale)</option>
                @foreach($gesti as $g)
                    <option value="{{ $g->id }}" {{ old('gesto_tecnico_id') == $g->id ? 'selected' : '' }}>
                        {{ $g->nome }} ({{ $g->categoria }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">N. Salti</label>
            <input type="number" name="n_salti" class="form-control" value="{{ old('n_salti', 0) }}" min="0">
        </div>
        <div class="col-md-3">
            <label class="form-label">N. Gesti tecnici</label>
            <input type="number" name="n_gesti" class="form-control" value="{{ old('n_gesti', 0) }}" min="0">
        </div>
        <div class="col-md-6">
            <label class="form-label">URL Video (YouTube/Vimeo)</label>
            <input type="url" name="video_url" class="form-control" value="{{ old('video_url') }}" placeholder="https://...">
        </div>

        <div class="col-12">
            <label class="form-label">Capacità allenate</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach($capacita as $c)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="capacita_ids[]"
                               value="{{ $c->id }}" id="cap{{ $c->id }}"
                               {{ in_array($c->id, old('capacita_ids', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="cap{{ $c->id }}">
                            <x-badge-capacita :capacita="$c" />
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">Descrizione / Note metodologiche</label>
            <textarea name="descrizione" class="form-control" rows="3">{{ old('descrizione') }}</textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Salva esercizio</button>
            <a href="{{ route('allenatore.esercizi.index') }}" class="btn btn-outline-secondary ms-2">Annulla</a>
        </div>
    </div>
</form>
@endsection
