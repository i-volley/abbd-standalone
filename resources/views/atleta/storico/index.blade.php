@extends('layouts.atleta')
@section('title', 'Storico feedback')

@section('content')
<h3 class="mb-4">Il mio storico</h3>

@forelse($feedback as $fb)
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">{{ $fb->seduta->titolo }}</h6>
                <small class="text-muted">{{ $fb->created_at->format('d/m/Y H:i') }}</small>
                @if(!$fb->inviato_in_scadenza)
                    <span class="badge bg-danger ms-2">Fuori scadenza</span>
                @endif
            </div>
            <div class="d-flex gap-3 text-center">
                <div><div class="fw-bold text-warning">{{ $fb->rpe }}</div><small class="text-muted">RPE</small></div>
                <div><div class="fw-bold text-primary">{{ $fb->qualita_prestazione }}</div><small class="text-muted">Qualità</small></div>
                <div><div class="fw-bold text-info">{{ $fb->impegno_squadra }}</div><small class="text-muted">Impegno</small></div>
                <div><div class="fw-bold text-success">{{ $fb->miglioramento_fondamentale }}/5</div><small class="text-muted">Fond.</small></div>
            </div>
        </div>
        @if($fb->nota)
            <p class="mt-2 mb-0 small text-muted">"{{ $fb->nota }}"</p>
        @endif
    </div>
</div>
@empty
<div class="alert alert-info">Nessun feedback inviato ancora.</div>
@endforelse

{{ $feedback->links() }}
@endsection
