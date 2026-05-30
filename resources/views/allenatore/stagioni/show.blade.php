@extends('layouts.allenatore')
@section('title', $stagione->nome)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $stagione->nome }}</h2>
    <a href="{{ route('allenatore.stagioni.macrocicli.create', $stagione) }}" class="btn btn-primary">+ Macrociclo</a>
</div>

@forelse($stagione->macrocicli as $m)
<div class="card shadow-sm mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">{{ $m->nome }}</h5>
            <small class="text-muted">{{ $m->fase }} · {{ $m->data_inizio->format('d/m/Y') }} → {{ $m->data_fine->format('d/m/Y') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('allenatore.macrocicli.show', $m) }}" class="btn btn-sm btn-outline-primary">Apri</a>
            <form action="{{ route('allenatore.macrocicli.destroy', $m) }}" method="POST" onsubmit="return confirm('Eliminare?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Elimina</button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">Nessun macrociclo.</div>
@endforelse
@endsection
