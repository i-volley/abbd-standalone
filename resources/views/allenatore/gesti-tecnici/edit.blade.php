@extends('layouts.allenatore')
@section('title', __('Modifica Gesto Tecnico'))

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('allenatore.sports.index', ['open_sport' => $gestoTecnico->sport_id]) }}"
       class="btn btn-sm btn-outline-secondary">← {{ __('Impostazioni') }}</a>
    <h2 class="mb-0">{{ __('Modifica') }}: {{ $gestoTecnico->nome }}</h2>
</div>

<div class="card border-0 shadow-sm" style="max-width:440px">
    <div class="card-body">
        <form action="{{ route('allenatore.gesti-tecnici.update', $gestoTecnico) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label">{{ __('Sport') }}</label>
                <input type="text" class="form-control" value="{{ $gestoTecnico->sport->nome ?? '—' }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Nome *') }}</label>
                <input type="text" name="nome" class="form-control"
                       value="{{ old('nome', $gestoTecnico->nome) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Categoria') }}</label>
                <select name="categoria_id" class="form-select">
                    <option value="">{{ __('Nessuna categoria') }}</option>
                    @foreach($categorie as $cat)
                    <option value="{{ $cat->id }}"
                            {{ old('categoria_id', $gestoTecnico->categoria_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nome }}
                    </option>
                    @endforeach
                </select>
                {{-- Preview badge categoria selezionata --}}
                @if($gestoTecnico->categoriaGesto)
                <div class="mt-2">
                    <x-badge-categoria-gesto :categoria="$gestoTecnico->categoriaGesto" />
                </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="form-label">{{ __('Ordine') }}</label>
                <input type="number" name="ordinamento" class="form-control"
                       value="{{ old('ordinamento', $gestoTecnico->ordinamento) }}" min="1">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Salva modifiche') }}</button>
                <a href="{{ route('allenatore.sports.index', ['open_sport' => $gestoTecnico->sport_id]) }}"
                   class="btn btn-outline-secondary">{{ __('Annulla') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
