@extends('layouts.allenatore')
@section('title', 'Modifica Gesto Tecnico')

@section('content')
<h2 class="mb-4">Modifica: {{ $gestoTecnico->nome }}</h2>
<form action="{{ route('allenatore.gesti-tecnici.update', $gestoTecnico) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3" style="max-width:400px">
        <div class="col-12">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome', $gestoTecnico->nome) }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select" required>
                @foreach(['fondamentale_base','fondamentale_gioco'] as $c)
                    <option value="{{ $c }}" {{ old('categoria', $gestoTecnico->categoria) === $c ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($c)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <label class="form-label">Ordinamento</label>
            <input type="number" name="ordinamento" class="form-control" value="{{ old('ordinamento', $gestoTecnico->ordinamento) }}" min="0">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Salva</button>
            <a href="{{ route('allenatore.gesti-tecnici.index') }}" class="btn btn-outline-secondary ms-2">Annulla</a>
        </div>
    </div>
</form>
@endsection
