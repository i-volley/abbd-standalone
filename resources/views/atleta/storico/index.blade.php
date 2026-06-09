@extends('layouts.atleta')
@section('title', 'Feedback history')

@section('content')
<h3 class="mb-4">My history</h3>

@forelse($feedback as $fb)
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">{{ $fb->seduta->titolo }}</h6>
                <small class="text-muted">{{ $fb->created_at->format('d/m/Y H:i') }}</small>
                @if(!$fb->inviato_in_scadenza)
                    <span class="badge bg-danger ms-2">Past deadline</span>
                @endif
            </div>
            <div class="d-flex gap-3 text-center">
                <div><div class="fw-bold text-warning">{{ $fb->rpe }}</div><small class="text-muted">RPE</small></div>
                <div><div class="fw-bold text-primary">{{ $fb->qualita_prestazione }}</div><small class="text-muted">Quality</small></div>
                <div><div class="fw-bold text-info">{{ $fb->impegno_squadra }}</div><small class="text-muted">Commitment</small></div>
                <div><div class="fw-bold text-success">{{ $fb->miglioramento_fondamentale }}/5</div><small class="text-muted">Fund.</small></div>
            </div>
        </div>
        @if($fb->nota)
            <p class="mt-2 mb-0 small text-muted">"{{ $fb->nota }}"</p>
        @endif
    </div>
</div>
@empty
<div class="alert alert-info">No feedback submitted yet.</div>
@endforelse

{{ $feedback->links() }}
@endsection
