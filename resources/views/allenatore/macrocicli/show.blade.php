@extends('layouts.allenatore')
@section('title', $macrociclo->nome)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $macrociclo->nome }}</h2>
    <a href="{{ route('allenatore.macrocicli.microcicli.create', $macrociclo) }}" class="btn btn-primary">+ Microciclo</a>
</div>

@forelse($macrociclo->microcicli as $m)
<div class="card shadow-sm mb-2">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <strong>Settimana {{ $m->numero }}</strong>
            <small class="text-muted ms-2">{{ $m->data_inizio->format('d/m/Y') }} · intensità:
                <span class="badge bg-secondary">{{ $m->intensita }}</span>
            </small>
        </div>
        <form action="{{ route('allenatore.microcicli.destroy', $m) }}" method="POST" onsubmit="return confirm('Eliminare?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Elimina</button>
        </form>
    </div>
</div>
@empty
<div class="alert alert-info">Nessun microciclo.</div>
@endforelse
@endsection
