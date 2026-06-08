@extends('layouts.allenatore')
@section('title', __('Nuovo Gesto Tecnico'))

@section('content')
<h2 class="mb-4">{{ __('Nuovo Gesto Tecnico') }}</h2>
<form action="{{ route('allenatore.gesti-tecnici.store') }}" method="POST">
    @csrf
    <div class="row g-3" style="max-width:400px">
        <div class="col-12">
            <label class="form-label">{{ __('Nome') }}</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required placeholder="{{ __('es. Bagher') }}">
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('Categoria') }}</label>
            <select name="categoria" class="form-select" required>
                <option value="fondamentale_base">{{ __('Fondamentale base') }}</option>
                <option value="fondamentale_gioco">{{ __('Fondamentale di gioco') }}</option>
            </select>
        </div>
        <div class="col-6">
            <label class="form-label">{{ __('Ordine') }}</label>
            <input type="number" name="ordinamento" class="form-control" value="{{ old('ordinamento',0) }}" min="0">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>
            <a href="{{ route('allenatore.gesti-tecnici.index') }}" class="btn btn-outline-secondary ms-2">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>
@endsection
