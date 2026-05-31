@extends('layouts.allenatore')
@section('title', 'Modifica Gesto Tecnico')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('allenatore.sports.index', ['open_sport' => $gestoTecnico->sport_id]) }}"
       class="btn btn-sm btn-outline-secondary">← Impostazioni</a>
    <h2 class="mb-0">Modifica: {{ $gestoTecnico->nome }}</h2>
</div>

<div class="card border-0 shadow-sm" style="max-width:440px">
    <div class="card-body">
        <form action="{{ route('allenatore.gesti-tecnici.update', $gestoTecnico) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label">Sport</label>
                <input type="text" class="form-control" value="{{ $gestoTecnico->sport->nome ?? '—' }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Nome *</label>
                <input type="text" name="nome" class="form-control"
                       value="{{ old('nome', $gestoTecnico->nome) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Categoria *</label>
                <div class="d-flex gap-3 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="categoria"
                               value="fondamentale_base" id="cat-base"
                               {{ old('categoria', $gestoTecnico->categoria) === 'fondamentale_base' ? 'checked' : '' }}>
                        <label class="form-check-label" for="cat-base">
                            <span class="badge bg-primary">Fondamentale base</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="categoria"
                               value="fondamentale_gioco" id="cat-gioco"
                               {{ old('categoria', $gestoTecnico->categoria) === 'fondamentale_gioco' ? 'checked' : '' }}>
                        <label class="form-check-label" for="cat-gioco">
                            <span class="badge bg-success">Fondamentale di gioco</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Ordinamento</label>
                <input type="number" name="ordinamento" class="form-control"
                       value="{{ old('ordinamento', $gestoTecnico->ordinamento) }}" min="1">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Salva modifiche</button>
                <a href="{{ route('allenatore.sports.index', ['open_sport' => $gestoTecnico->sport_id]) }}"
                   class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
