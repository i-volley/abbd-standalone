@extends('layouts.allenatore')
@section('title', $stagione->nome)

@section('content')

{{-- ── HEADER ───────────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0">{{ $stagione->nome }}</h2>
        <small class="text-muted">
            {{ $stagione->data_inizio->format('d/m/Y') }} → {{ $stagione->data_fine->format('d/m/Y') }}
            · {{ $stagione->data_inizio->diffInDays($stagione->data_fine) }} giorni
        </small>
    </div>
    <a href="{{ route('allenatore.stagioni.macrocicli.create', $stagione) }}" class="btn btn-primary">
        + Macrociclo
    </a>
</div>

@php
    $inizio   = $stagione->data_inizio;
    $fine     = $stagione->data_fine;
    $totGiorni = max(1, $inizio->diffInDays($fine));

    /**
     * Calcola left% e width% di un elemento nel calendario.
     * Clamp: non va oltre i bordi della stagione.
     */
    $pos = function ($da, $a) use ($inizio, $fine, $totGiorni) {
        $da    = max($da, $inizio);
        $a     = min($a,  $fine);
        $left  = $inizio->diffInDays($da) / $totGiorni * 100;
        $width = max(0.4, $da->diffInDays($a) / $totGiorni * 100);
        return ['left' => round($left, 2), 'width' => round($width, 2)];
    };

    // Mesi da mostrare nell'header
    $mesi = [];
    $cur  = $inizio->copy()->startOfMonth();
    while ($cur->lte($fine)) {
        $mesi[] = $cur->copy();
        $cur->addMonth();
    }

    // Sedute raggruppate per data (per mostrare il count su giorni con più sedute)
    $sedutePerData = $sedute->groupBy(fn($s) => $s->data->format('Y-m-d'));

    // Colori stato seduta
    $statoColore = [
        'bozza'       => '#94a3b8',
        'pubblicata'  => '#3b82f6',
        'completata'  => '#10b981',
    ];
@endphp

@if($stagione->macrocicli->isEmpty())
<div class="alert alert-info">
    Nessun macrociclo. <a href="{{ route('allenatore.stagioni.macrocicli.create', $stagione) }}">Aggiungi il primo →</a>
</div>
@else

{{-- ── LEGENDA ──────────────────────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap gap-3 mb-3 align-items-center">
    @foreach($stagione->macrocicli as $m)
    <div class="d-flex align-items-center gap-1">
        <span class="rounded-pill d-inline-block" style="width:1rem;height:1rem;background:{{ $m->colore ?? '#4f46e5' }}"></span>
        <small class="fw-semibold">{{ $m->nome }}</small>
        <small class="text-muted">({{ ucfirst($m->fase) }})</small>
    </div>
    @endforeach
    @if($unitaDidattiche->isNotEmpty())
    <div class="d-flex align-items-center gap-1 ms-2">
        <span class="rounded-pill d-inline-block" style="width:1rem;height:1rem;background:#8b5cf6;opacity:.75"></span>
        <small class="fw-semibold">Unità Didattiche</small>
    </div>
    @endif
    @if($sedute->isNotEmpty())
    <div class="d-flex align-items-center gap-1 ms-2">
        <span class="rounded-circle d-inline-block border" style="width:.75rem;height:.75rem;background:#3b82f6"></span>
        <small class="fw-semibold">Sedute</small>
    </div>
    @endif
</div>

{{-- ── CALENDARIO TIMELINE ──────────────────────────────────────────────────── --}}
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="calendar-timeline" style="position:relative;width:100%">

            {{-- HEADER MESI --}}
            <div style="position:relative;height:2rem;margin-bottom:.25rem">
                @foreach($mesi as $mese)
                @php
                    $meseInizio = $mese->copy()->max($inizio);
                    $meseFine   = $mese->copy()->endOfMonth()->min($fine);
                    $p          = $pos($meseInizio, $meseFine);
                    $tooNarrow  = $p['width'] < 5;
                @endphp
                <div style="
                    position:absolute;
                    left:{{ $p['left'] }}%;
                    width:{{ $p['width'] }}%;
                    height:2rem;
                    border-left:1px solid #dee2e6;
                    padding-left:.3rem;
                    display:flex;
                    align-items:center;
                    font-size:.7rem;
                    font-weight:600;
                    color:#6c757d;
                    text-transform:uppercase;
                    letter-spacing:.05em;
                    overflow:hidden;
                    white-space:nowrap;
                ">{{ $tooNarrow ? $mese->format('M') : $mese->format('MMM Y') }}</div>
                @endforeach
            </div>

            {{-- GRIGLIA DI SFONDO (linee mesi) --}}
            <div style="position:absolute;top:2rem;left:0;right:0;bottom:0;pointer-events:none;z-index:0">
                @foreach($mesi as $mese)
                @php
                    $meseInizio = $mese->copy()->max($inizio);
                    $p2 = $pos($meseInizio, $meseInizio->copy()->addDay());
                @endphp
                <div style="
                    position:absolute;
                    left:{{ $p2['left'] }}%;
                    top:0;bottom:0;
                    width:1px;
                    background:#e9ecef;
                "></div>
                @endforeach
            </div>

            {{-- FASCE MACROCICLI --}}
            <div style="position:relative;height:2.8rem;margin-bottom:.5rem;z-index:1">
                @foreach($stagione->macrocicli as $m)
                @php $p = $pos($m->data_inizio, $m->data_fine); @endphp
                <div style="
                    position:absolute;
                    left:{{ $p['left'] }}%;
                    width:{{ $p['width'] }}%;
                    height:100%;
                    background:{{ $m->colore ?? '#4f46e5' }};
                    border-radius:.4rem;
                    opacity:.85;
                    display:flex;
                    align-items:center;
                    padding:0 .5rem;
                    overflow:hidden;
                    white-space:nowrap;
                "
                data-bs-toggle="tooltip"
                title="{{ $m->nome }} · {{ $m->data_inizio->format('d/m') }} → {{ $m->data_fine->format('d/m/Y') }}">
                    <span style="font-size:.7rem;font-weight:700;color:#fff;letter-spacing:.04em;text-shadow:0 1px 2px rgba(0,0,0,.3)">
                        {{ $p['width'] > 8 ? $m->nome : '' }}
                    </span>
                </div>
                @endforeach
            </div>

            {{-- FASCE UNITÀ DIDATTICHE --}}
            @if($unitaDidattiche->isNotEmpty())
            <div style="position:relative;height:1.4rem;margin-bottom:.4rem;z-index:1">
                @foreach($unitaDidattiche as $ud)
                @php
                    // Unità didattica: mostrala come punto/breve fascia partendo da data_inizio
                    // Non ha data_fine diretta — mostriamo una fascia di 14gg come stima
                    $udFine = $ud->data_inizio->copy()->addDays(13)->min($fine);
                    $p = $pos($ud->data_inizio, $udFine);
                @endphp
                <div style="
                    position:absolute;
                    left:{{ $p['left'] }}%;
                    width:{{ max($p['width'], 1.5) }}%;
                    height:100%;
                    background:#8b5cf6;
                    border-radius:.3rem;
                    opacity:.65;
                "
                data-bs-toggle="tooltip"
                title="U.D.: {{ $ud->titolo }} · {{ $ud->data_inizio->format('d/m/Y') }}">
                </div>
                @endforeach
            </div>
            @endif

            {{-- DOT SEDUTE --}}
            <div style="position:relative;height:1.6rem;z-index:2">
                @foreach($sedutePerData as $dataStr => $seduteGiorno)
                @php
                    $data = \Carbon\Carbon::parse($dataStr);
                    $p    = $pos($data, $data->copy()->addDay());
                    $count= $seduteGiorno->count();
                    $stato= $seduteGiorno->first()->stato;
                    $col  = $statoColore[$stato] ?? '#64748b';
                    // Se più sedute nello stesso giorno, usa bordo doppio
                    $border = $count > 1 ? "outline:2px solid $col;outline-offset:1px;" : '';
                @endphp
                <div style="
                    position:absolute;
                    left:calc({{ $p['left'] }}% - .35rem);
                    top:.2rem;
                    width:.7rem;
                    height:.7rem;
                    background:{{ $col }};
                    border-radius:50%;
                    {{ $border }}
                    cursor:pointer;
                "
                data-bs-toggle="tooltip"
                title="{{ $data->format('d/m/Y') }} · {{ $seduteGiorno->pluck('titolo')->join(', ') }}">
                </div>
                @endforeach
            </div>

            {{-- SCALA TEMPORALE (percentuali di avanzamento) --}}
            <div style="position:relative;height:1rem;margin-top:.25rem">
                @foreach([0, 25, 50, 75, 100] as $pct)
                @php
                    $dataLabel = $inizio->copy()->addDays(round($totGiorni * $pct / 100));
                @endphp
                <div style="
                    position:absolute;
                    left:{{ $pct }}%;
                    transform:translateX(-50%);
                    font-size:.6rem;
                    color:#adb5bd;
                    white-space:nowrap;
                ">{{ $dataLabel->format('d/m') }}</div>
                @endforeach
            </div>

        </div>{{-- /calendar-timeline --}}
    </div>
</div>

@endif{{-- macrocicli non vuoti --}}


{{-- ── LISTA MACROCICLI ────────────────────────────────────────────────────── --}}
<h5 class="fw-bold mb-3">Macrocicli</h5>

@forelse($stagione->macrocicli as $m)
<div class="card shadow-sm mb-2" style="border-left:4px solid {{ $m->colore ?? '#4f46e5' }}">
    <div class="card-body py-2 d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-semibold">{{ $m->nome }}</span>
            <span class="badge ms-1 rounded-pill"
                  style="background:{{ $m->colore ?? '#4f46e5' }};font-size:.7rem">
                {{ ucfirst($m->fase) }}
            </span>
            <small class="text-muted ms-2">
                {{ $m->data_inizio->format('d/m/Y') }} → {{ $m->data_fine->format('d/m/Y') }}
                ({{ $m->data_inizio->diffInDays($m->data_fine) }} gg)
            </small>
            @if($m->obiettivi)
            <small class="text-muted d-block" style="font-size:.75rem">{{ Str::limit($m->obiettivi, 80) }}</small>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('allenatore.macrocicli.show', $m) }}" class="btn btn-sm btn-outline-primary">Apri</a>
            <form action="{{ route('allenatore.macrocicli.destroy', $m) }}" method="POST"
                  onsubmit="return confirm('Eliminare {{ addslashes($m->nome) }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">×</button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">Nessun macrociclo.</div>
@endforelse


{{-- ── RIEPILOGO SEDUTE ────────────────────────────────────────────────────── --}}
@if($sedute->isNotEmpty())
<h5 class="fw-bold mt-4 mb-3">
    Sedute della stagione
    <span class="badge bg-secondary rounded-pill ms-1" style="font-size:.75rem">{{ $sedute->count() }}</span>
</h5>
<div class="row g-2">
    @foreach($sedute as $s)
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('allenatore.sedute.show', $s) }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm" style="border-left:3px solid {{ $statoColore[$s->stato] ?? '#64748b' }} !important;border-left-style:solid !important">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <small class="fw-semibold text-dark">{{ $s->titolo }}</small>
                        <span class="badge rounded-pill ms-1"
                              style="background:{{ $statoColore[$s->stato] ?? '#64748b' }};font-size:.65rem">
                            {{ ucfirst($s->stato) }}
                        </span>
                    </div>
                    <small class="text-muted">{{ $s->data->format('d/m/Y') }}</small>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endif


{{-- ── UNITÀ DIDATTICHE ─────────────────────────────────────────────────────── --}}
@if($unitaDidattiche->isNotEmpty())
<h5 class="fw-bold mt-4 mb-3">
    Unità Didattiche
    <span class="badge rounded-pill ms-1" style="background:#8b5cf6;font-size:.75rem">{{ $unitaDidattiche->count() }}</span>
</h5>
<div class="row g-2">
    @foreach($unitaDidattiche as $ud)
    <div class="col-md-6">
        <a href="{{ route('allenatore.unita-didattiche.show', $ud) }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm" style="border-left:3px solid #8b5cf6 !important;border-left-style:solid !important">
                <div class="card-body py-2 px-3">
                    <small class="fw-semibold text-dark d-block">{{ $ud->titolo }}</small>
                    <small class="text-muted">
                        dal {{ $ud->data_inizio->format('d/m/Y') }}
                        · {{ $ud->progressione ? ucfirst(str_replace('_', ' ', $ud->progressione)) : '' }}
                    </small>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endif

@endsection

@push('scripts')
<script>
// Attiva tutti i tooltip Bootstrap
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top' });
});
</script>
@endpush
