@extends('layouts.allenatore')
@section('title', 'Catalogo Esercizi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Catalogo Esercizi</h2>
    <a href="{{ route('allenatore.esercizi.create') }}" class="btn btn-primary">+ Nuovo esercizio</a>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" id="cerca" class="form-control" placeholder="Cerca per nome...">
    </div>
    <div class="col-md-3">
        <select id="filtroFase" class="form-select">
            <option value="">Tutte le fasi</option>
            <option value="riscaldamento">Riscaldamento</option>
            <option value="potenziamento">Potenziamento</option>
            <option value="stretching">Stretching</option>
        </select>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Nome</th><th>Fase</th><th>Metodologia</th>
                <th>Gesto tecnico</th><th>Capacità</th><th>Min</th><th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($esercizi as $e)
            <tr>
                <td><strong>{{ $e->nome }}</strong></td>
                <td><span class="badge bg-secondary">{{ $e->fase }}</span></td>
                <td>{{ $e->metodologia }}</td>
                <td>{{ $e->gestoTecnico?->nome ?? '—' }}</td>
                <td>
                    @foreach($e->capacita as $c)
                        <x-badge-capacita :capacita="$c" />
                    @endforeach
                </td>
                <td>{{ $e->durata_min }}</td>
                <td>
                    <a href="{{ route('allenatore.esercizi.edit', $e) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
                    <form action="{{ route('allenatore.esercizi.destroy', $e) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Eliminare?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Elimina</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Nessun esercizio nel catalogo.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $esercizi->links() }}
@endsection
