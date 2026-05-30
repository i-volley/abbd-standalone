@extends('layouts.allenatore')
@section('title', 'Nuovo Gesto Tecnico')

@section('content')
<h2 class="mb-4">Nuovo Gesto Tecnico</h2>
<form action="{{ route('allenatore.gesti-tecnici.store') }}" method="POST">
    @csrf
    <div class="row g-3" style="max-width:400px">
        <div class="col-12">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required placeholder="es. Bagher">
        </div>
        <div class="col-12">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select" required>
                <option value="fondamentale_base">Fondamentale base</option>
                <option value="fondamentale_gioco">Fondamentale di gioco</option>
            </select>
        </div>
        <div class="col-6">
            <label class="form-label">Ordinamento</label>
            <input type="number" name="ordinamento" class="form-control" value="{{ old('ordinamento',0) }}" min="0">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Salva</button>
            <a href="{{ route('allenatore.gesti-tecnici.index') }}" class="btn btn-outline-secondary ms-2">Annulla</a>
        </div>
    </div>
</form>
@endsection
