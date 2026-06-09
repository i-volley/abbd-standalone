@extends('layouts.atleta')
@section('title', $seduta->titolo)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h3>{{ $seduta->titolo }}</h3>
        <small class="text-muted">{{ $seduta->data->format('d/m/Y') }} · {{ $seduta->durata_tot_min }} min</small>
        @if($seduta->scadenza_feedback)
            <div><x-countdown-scadenza :scadenza="$seduta->scadenza_feedback" /></div>
        @endif
    </div>
    @if(!$haFeedback)
        <a href="#form-feedback" class="btn btn-success">Send feedback</a>
    @else
        <span class="badge bg-success fs-6">Feedback sent</span>
    @endif
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">Exercises</div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Exercise</th><th>Phase</th><th>Sets</th><th>Reps</th><th>Rest(s)</th><th>Video</th></tr>
            </thead>
            <tbody>
            @foreach($seduta->sedutaEsercizi as $i => $se)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <strong>{{ $se->esercizio->nome }}</strong>
                    @foreach($se->esercizio->capacita as $c)
                        <x-badge-capacita :capacita="$c" />
                    @endforeach
                </td>
                <td><span class="badge bg-secondary">{{ $se->esercizio->fase }}</span></td>
                <td>{{ $se->serie ?? '—' }}</td>
                <td>{{ $se->ripetizioni ?? '—' }}</td>
                <td>{{ $se->recupero_sec ?? '—' }}</td>
                <td>
                    @if($se->esercizio->video_url)
                        <a href="{{ $se->esercizio->video_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">▶ Video</a>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($seduta->note_allenatore)
<div class="alert alert-light border mb-4">
    <strong>Coach notes:</strong> {{ $seduta->note_allenatore }}
</div>
@endif

@if(!$haFeedback)
<div id="form-feedback" class="card shadow-sm">
    <div class="card-header bg-success text-white">Submit your feedback</div>
    <div class="card-body">
        <form action="{{ route('atleta.feedback.store') }}" method="POST">
            @csrf
            <input type="hidden" name="seduta_id" value="{{ $seduta->id }}">

            <x-slider-rating name="rpe" label="1. Perceived exertion (RPE)" :min="1" :max="10" :value="5" />
            <x-slider-rating name="qualita_prestazione" label="2. Quality of my performance" :min="1" :max="10" :value="5" />
            <x-slider-rating name="impegno_squadra" label="3. Team effort and involvement" :min="1" :max="10" :value="5" />
            <x-stelle-rating name="miglioramento_fondamentale" label="4. Perceived fundamental improvement" :value="0" />

            @php $eserciziVotabili = $seduta->sedutaEsercizi->where('voto_abilitato', true); @endphp
            @if($eserciziVotabili->count() > 0)
            <hr>
            <h6>5. Exercise rating</h6>
            @foreach($eserciziVotabili as $se)
                <x-stelle-rating
                    name="gradimento_esercizio[{{ $se->id }}]"
                    label="{{ $se->esercizio->nome }}"
                    :value="0" />
            @endforeach
            @endif

            <hr>
            <div class="mb-3">
                <label class="form-label">Free note (optional, max 500 characters)</label>
                <textarea name="nota" class="form-control" rows="3" maxlength="500"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Send feedback</button>
        </form>
    </div>
</div>
@endif
@endsection
