@extends('layouts.allenatore')
@section('title', $macrociclo->nome)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">{{ $macrociclo->nome }}</h2>
        <small class="text-muted">
            <span class="d-inline-block rounded-pill me-1"
                  style="width:.8rem;height:.8rem;background:{{ $macrociclo->colore ?? '#4f46e5' }};vertical-align:middle"></span>
            {{ ucfirst($macrociclo->fase) }} ·
            {{ $macrociclo->data_inizio->format('d/m/Y') }} → {{ $macrociclo->data_fine->format('d/m/Y') }}
        </small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('allenatore.macrocicli.edit', $macrociclo) }}" class="btn btn-sm btn-outline-secondary">{{ __('Modifica') }}</a>
        <a href="{{ route('allenatore.macrocicli.microcicli.create', $macrociclo) }}" class="btn btn-primary btn-sm">{{ __('+ Microciclo') }}</a>
    </div>
</div>

@forelse($macrociclo->microcicli as $m)
<div class="card shadow-sm mb-2">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ __('Settimana') }} {{ $m->numero }}</strong>
            <small class="text-muted ms-2">{{ $m->data_inizio->format('d/m/Y') }} · intensity:
                <span class="badge bg-secondary">{{ $m->intensita }}</span>
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('allenatore.microcicli.edit', $m) }}" class="btn btn-sm btn-outline-secondary">{{ __('Modifica') }}</a>
            <form action="{{ route('allenatore.microcicli.destroy', $m) }}" method="POST" data-confirm="Delete?">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">{{ __('Elimina') }}</button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">{{ __('Nessun microciclo.') }}</div>
@endforelse
@endsection
