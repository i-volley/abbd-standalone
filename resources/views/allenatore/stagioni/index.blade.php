@extends('layouts.allenatore')
@section('title', 'Pianificazione')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Pianificazione</h2>
    <a href="{{ route('allenatore.stagioni.create') }}" class="btn btn-primary">+ Nuova stagione</a>
</div>

@forelse($stagioni as $s)
<div class="card shadow-sm mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">{{ $s->nome }}</h5>
            <small class="text-muted">{{ $s->team->nome }} · {{ $s->data_inizio->format('d/m/Y') }} → {{ $s->data_fine->format('d/m/Y') }}</small>
            @if($s->attiva)<span class="badge bg-success ms-2">Attiva</span>@endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('allenatore.stagioni.edit', $s) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
            <a href="{{ route('allenatore.stagioni.show', $s) }}" class="btn btn-sm btn-outline-primary">Apri</a>
            <form action="{{ route('allenatore.stagioni.destroy', $s) }}" method="POST"
                  data-confirm="Eliminare la stagione «{{ addslashes($s->nome) }}»? Verranno eliminati anche macrocicli e microcicli collegati.">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Elimina</button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">Nessuna stagione. Crea la prima!</div>
@endforelse
@endsection
