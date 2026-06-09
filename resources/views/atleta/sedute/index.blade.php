@extends('layouts.atleta')
@section('title', 'My sessions')

@section('content')
<h3 class="mb-4">My sessions</h3>

@forelse($sedute as $seduta)
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="mb-1">{{ $seduta->titolo }}</h5>
                <small class="text-muted">{{ $seduta->data->format('d/m/Y') }} · {{ $seduta->durata_tot_min }} min</small>
                @if($seduta->scadenza_feedback)
                    <div class="mt-1">
                        <x-countdown-scadenza :scadenza="$seduta->scadenza_feedback" />
                    </div>
                @endif
            </div>
            <div class="text-end">
                @if(in_array($seduta->id, $feedbackInviati))
                    <span class="badge bg-success">Feedback sent</span>
                @else
                    <span class="badge bg-warning text-dark">Feedback to send</span>
                @endif
                <div class="mt-2">
                    <a href="{{ route('atleta.sedute.show', $seduta) }}" class="btn btn-sm btn-outline-primary">View</a>
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">No sessions available.</div>
@endforelse
@endsection
