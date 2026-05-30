@extends('layouts.allenatore')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard</h2>
    @if($team)
        <span class="badge bg-secondary fs-6">{{ $team->nome }} — {{ $team->sport->nome }}</span>
    @endif
</div>

@if(!$team)
    <div class="alert alert-info">
        Nessun team ancora. <a href="{{ route('allenatore.teams.create') }}">Crea il tuo primo team</a>.
    </div>
@else

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <x-card-kpi label="Sedute totali" :value="$stats['totale_sedute']" color="primary" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="Sedute visibili" :value="$stats['sedute_visibili']" color="info" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="Feedback ricevuti" :value="$stats['totale_feedback']" color="success" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="RPE medio" :value="$stats['avg_rpe']" color="warning" suffix="/10" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="Qualità media" :value="$stats['avg_qualita']" color="primary" suffix="/10" />
    </div>
    <div class="col-md-2">
        <x-card-kpi label="Impegno squadra" :value="$stats['avg_impegno']" color="secondary" suffix="/10" />
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header">RPE per seduta (ultime 10)</div>
            <div class="card-body">
                <canvas id="chartRpe" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                Ultimi feedback
                <a href="{{ route('allenatore.sedute.index') }}" class="btn btn-sm btn-outline-primary">Vedi sedute</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Seduta</th><th>Atleta</th><th>RPE</th></tr></thead>
                    <tbody>
                    @forelse($ultimeFeedback ?? [] as $fb)
                        <tr>
                            <td class="small">{{ $fb->seduta->titolo }}</td>
                            <td class="small">{{ $fb->atleta->name }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $fb->rpe }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted text-center py-3">Nessun feedback ancora</td></tr>
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
