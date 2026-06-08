@extends('layouts.allenatore')
@section('title', __('Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard</h2>
    @if($team)
        <span class="badge bg-secondary fs-6">{{ $team->nome }} — {{ $team->sport->nome }}</span>
    @endif
</div>

@if(!$team)
    <div class="alert alert-info">
        {{ __('Nessun team ancora.') }} <a href="{{ route('allenatore.teams.create') }}">{{ __('Crea il tuo primo team') }}</a>.
    </div>
@else

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <x-card-kpi label="{{ __('Sedute totali') }}" :value="$stats['totale_sedute']" color="primary" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="{{ __('Sedute visibili') }}" :value="$stats['sedute_visibili']" color="info" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="{{ __('Feedback ricevuti') }}" :value="$stats['totale_feedback']" color="success" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="{{ __('RPE medio') }}" :value="$stats['avg_rpe']" color="warning" suffix="/10" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="{{ __('Qualità media') }}" :value="$stats['avg_qualita']" color="primary" suffix="/10" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="{{ __('Impegno squadra') }}" :value="$stats['avg_impegno']" color="secondary" suffix="/10" />
    </div>
</div>

{{-- ── INSIGHT DIAGNOSTICI ────────────────────────────────────────────── --}}
@if($insights->isNotEmpty())
<div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #dc3545 !important">
    <div class="card-header bg-transparent py-2 d-flex align-items-center gap-2">
        <span>🔍</span>
        <small class="fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Insight diagnostici — ultimi 30 giorni') }}</small>
        <small class="text-muted ms-auto">{{ __('Basati sul feedback degli atleti · Metodo FIPAV') }}</small>
    </div>
    <div class="card-body py-2">
        @foreach($insights as $ins)
        @php
        $metodBadge = ['analitico'=>'bg-primary','sintetico'=>'bg-warning text-dark','globale'=>'bg-success'];
        @endphp
        <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    @if($ins['fondamentale'])
                        <strong>{{ $ins['fondamentale'] }}</strong>
                    @else
                        <strong>{{ __('Generale') }}</strong>
                    @endif
                    <span class="badge {{ $metodBadge[$ins['metodologia_consigliata']] ?? 'bg-secondary' }} rounded-pill" style="font-size:.7rem">
                        → {{ strtoupper($ins['metodologia_consigliata']) }}
                    </span>
                    <small class="text-muted">{{ $ins['num_sedute'] }} sedute · avg miglioramento {{ $ins['avg_miglioramento'] }}/5 · RPE {{ $ins['avg_rpe'] }}</small>
                </div>
                <small class="text-muted">{{ $ins['descrizione'] }}</small>
            </div>
            <a href="{{ route('allenatore.wizard.risultati') }}?{{ $ins['wizard_params'] }}" class="btn btn-sm btn-outline-primary flex-shrink-0">
                {{ __('Prescrivi →') }}
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header">{{ __('RPE per seduta (ultime 10)') }}</div>
            <div class="card-body">
                <canvas id="chartRpe" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                {{ __('Ultimi feedback') }}
                <a href="{{ route('allenatore.sedute.index') }}" class="btn btn-sm btn-outline-primary">{{ __('Vedi sedute') }}</a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>{{ __('Seduta') }}</th><th>{{ __('Atleta') }}</th><th>RPE</th></tr></thead>
                    <tbody>
                    @forelse($ultimeFeedback ?? [] as $fb)
                        <tr>
                            <td class="small">{{ $fb->seduta->titolo }}</td>
                            <td class="small">{{ $fb->atleta->name }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $fb->rpe }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted text-center py-3">{{ __('Nessun feedback ancora') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
@if($team && isset($rpePerSeduta) && $rpePerSeduta->count() > 0)
new Chart(document.getElementById('chartRpe'), {
    type: 'line',
    data: {
        labels: @json($rpePerSeduta->pluck('titolo')),
        datasets: [{
            label: 'RPE Medio',
            data: @json($rpePerSeduta->pluck('feedback_avg_rpe')),
            borderColor: '#f97316',
            backgroundColor: 'rgba(249,115,22,0.1)',
            tension: 0.3,
            fill: true,
        }]
    },
    options: {
        scales: { y: { min: 1, max: 10 } },
        plugins: { legend: { display: false } }
    }
});
@endif
</script>
@endpush
