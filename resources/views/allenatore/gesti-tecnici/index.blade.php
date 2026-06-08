@extends('layouts.allenatore')
@section('title', __('Gesti Tecnici'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ __('Gesti Tecnici') }}</h2>
    <a href="{{ route('allenatore.gesti-tecnici.create') }}" class="btn btn-primary">{{ __('+ Nuovo gesto tecnico') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr><th>{{ __('Nome') }}</th><th>{{ __('Categoria') }}</th><th>{{ __('Ordine') }}</th><th></th></tr>
        </thead>
        <tbody>
        @forelse($gesti as $g)
        <tr>
            <td>{{ $g->nome }}</td>
            <td><span class="badge bg-secondary">{{ $g->categoria }}</span></td>
            <td>{{ $g->ordinamento }}</td>
            <td>
                <a href="{{ route('allenatore.gesti-tecnici.edit', $g) }}" class="btn btn-sm btn-outline-secondary">{{ __('Modifica') }}</a>
                <form action="{{ route('allenatore.gesti-tecnici.destroy', $g) }}" method="POST" class="d-inline"
                      data-confirm="Eliminare?">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">{{ __('Elimina') }}</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-muted text-center py-3">{{ __('Nessun gesto tecnico.') }}</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
