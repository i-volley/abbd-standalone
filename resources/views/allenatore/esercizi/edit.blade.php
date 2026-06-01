@extends('layouts.allenatore')
@section('title', 'Modifica Esercizio')

@section('content')
<h2 class="mb-4">Modifica: {{ $esercizio->nome }}</h2>

<form action="{{ route('allenatore.esercizi.update', $esercizio) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Nome *</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome', $esercizio->nome) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Durata (min)</label>
            <input type="number" name="durata_min" class="form-control" value="{{ old('durata_min', $esercizio->durata_min) }}" min="1">
        </div>

        <div class="col-md-4">
            <label class="form-label">Fase *</label>
            <select name="fase" class="form-select" required>
                @foreach(($parametri['fase'] ?? collect()) as $p)
                    <option value="{{ $p->valore }}" {{ old('fase', $esercizio->fase) === $p->valore ? 'selected' : '' }}>{{ $p->etichetta }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Metodologia *</label>
            <select name="metodologia" class="form-select" required>
                @foreach(($parametri['metodologia'] ?? collect()) as $p)
                    <option value="{{ $p->valore }}" {{ old('metodologia', $esercizio->metodologia) === $p->valore ? 'selected' : '' }}>{{ $p->etichetta }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Gesto tecnico</label>
            <select name="gesto_tecnico_id" class="form-select">
                <option value="">Nessuno</option>
                @foreach($gesti as $g)
                    <option value="{{ $g->id }}"
                        {{ old('gesto_tecnico_id', $esercizio->gesto_tecnico_id) == $g->id ? 'selected' : '' }}>
                        {{ $g->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">N. Salti</label>
            <input type="number" name="n_salti" class="form-control" value="{{ old('n_salti', $esercizio->n_salti) }}" min="0">
        </div>
        <div class="col-md-3">
            <label class="form-label">N. Gesti</label>
            <input type="number" name="n_gesti" class="form-control" value="{{ old('n_gesti', $esercizio->n_gesti) }}" min="0">
        </div>
        <div class="col-md-6">
            <label class="form-label">URL Video</label>
            <input type="url" name="video_url" class="form-control" value="{{ old('video_url', $esercizio->video_url) }}">
        </div>

        <div class="col-12">
            <label class="form-label">Capacità</label>
            <div class="d-flex flex-wrap gap-2">
                @php $selezionate = old('capacita_ids', $esercizio->capacita->pluck('id')->toArray()); @endphp
                @foreach($capacita as $c)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="capacita_ids[]"
                               value="{{ $c->id }}" id="cap{{ $c->id }}"
                               {{ in_array($c->id, $selezionate) ? 'checked' : '' }}>
                        <label class="form-check-label" for="cap{{ $c->id }}">
                            <x-badge-capacita :capacita="$c" />
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">Descrizione</label>
            <textarea name="descrizione" class="form-control" rows="3">{{ old('descrizione', $esercizio->descrizione) }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Categoria età</label>
            <select name="categoria_eta" class="form-select">
                <option value="">Nessuna (trasversale)</option>
                @foreach($categorie as $cat)
                    @php $col = \App\Models\Esercizio::catEtaColore($cat); @endphp
                    <option value="{{ $cat }}"
                            {{ old('categoria_eta', $esercizio->categoria_eta) === $cat ? 'selected' : '' }}
                            style="color:{{ $col }};font-weight:600">{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-8 d-flex align-items-end">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_pubblico" id="is_pubblico" value="1"
                       {{ old('is_pubblico', $esercizio->is_pubblico) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_pubblico">
                    Rendi pubblico nel catalogo generale
                    <small class="text-muted d-block">Visibile a tutti gli allenatori</small>
                </label>
            </div>
        </div>

        <div class="col-12">
            @include('allenatore.esercizi._assi-metodologici')
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Salva modifiche</button>
            <a href="{{ route('allenatore.esercizi.index') }}" class="btn btn-outline-secondary ms-2">Annulla</a>
        </div>
    </div>
</form>
@endsection
