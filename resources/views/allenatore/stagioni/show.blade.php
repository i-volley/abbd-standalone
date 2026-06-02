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
    <div class="d-flex gap-2">
        <a href="{{ route('allenatore.stagioni.edit', $stagione) }}" class="btn btn-outline-secondary">Modifica stagione</a>
        <a href="{{ route('allenatore.stagioni.macrocicli.create', $stagione) }}" class="btn btn-primary">+ Macrociclo</a>
    </div>
</div>

@php
    $inizio   = $stagione->data_inizio;
    $fine     = $stagione->data_fine;
    $totGiorni = max(1, $inizio->diffInDays($fine));

    /**
     * Calcola left% e width% di un elemento nel calendario.
     * Clamp: non va oltre i bordi della stagione.
     * Usa metodi Carbon per evitare max()/min() built-in PHP con oggetti.
     */
    $pos = function ($da, $a) use ($inizio, $fine, $totGiorni) {
        $da = $da->lt($inizio) ? $inizio->copy() : $da->copy();
        $a  = $a->gt($fine)   ? $fine->copy()   : $a->copy();
        if ($da->gte($a)) return ['left' => 0, 'width' => 0];
        $left  = $inizio->diffInDays($da) / $totGiorni * 100;
        $width = max(0.5, $da->diffInDays($a) / $totGiorni * 100);
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
                    // Usa Carbon max()/min() instance methods — non PHP built-in
                    $meseInizio = $mese->copy()->max($inizio);
                    $meseFine   = $mese->copy()->endOfMonth()->min($fine);
                    $p          = $pos($meseInizio, $meseFine);
                    $tooNarrow  = $p['width'] < 5;
                    // 'M' = Jan/Feb (3 lettere), 'F' = January (intero), 'Y' = 2024
                    $labelMese  = $tooNarrow ? $mese->format('M') : $mese->format('M Y');
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
                ">{{ $labelMese }}</div>
                @endforeach
            </div>

            {{-- GRIGLIA DI SFONDO (linee mesi) --}}
            <div style="position:absolute;top:2rem;left:0;right:0;bottom:0;pointer-events:none;z-index:0">
                @foreach($mesi as $mese)
                @php
                    $meseInizio2 = $mese->copy()->max($inizio);
                    $p2 = $pos($meseInizio2, $meseInizio2->copy()->addDay());
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


{{-- ── GIORNI DI ALLENAMENTO PROGRAMMATI ───────────────────────────────────── --}}
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-transparent py-2">
        <span class="fw-semibold">📅 Giorni di allenamento programmati</span>
        <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#form-aggiungi-giorno">
            + Aggiungi giorno
        </button>
    </div>

    {{-- Form aggiungi giorno (collassabile) --}}
    <div class="collapse" id="form-aggiungi-giorno">
        <div class="card-body border-bottom bg-light">
            <form action="{{ route('allenatore.stagioni.giorni.store', $stagione) }}" method="POST">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-sm-3">
                        <label class="form-label small mb-1">Giorno *</label>
                        <select name="giorno_settimana" class="form-select form-select-sm" required>
                            @foreach(\App\Models\GiornoAllenamento::labelGiorni() as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">Inizio *</label>
                        <input type="time" name="ora_inizio" class="form-control form-control-sm" required value="18:00">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">Fine</label>
                        <input type="time" name="ora_fine" class="form-control form-control-sm" value="20:00">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">Luogo</label>
                        <input type="text" name="luogo" class="form-control form-control-sm" placeholder="Palestra A" maxlength="255">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">Note</label>
                        <input type="text" name="note" class="form-control form-control-sm" placeholder="note" maxlength="255">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-sm btn-success w-100">Salva</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista giorni configurati --}}
    <div class="card-body py-2">
        @forelse($stagione->giorniAllenamento as $g)
        <div class="d-flex align-items-center justify-content-between py-1 border-bottom">
            <div>
                <span class="fw-semibold me-2">{{ $g->label_giorno }}</span>
                <span class="badge bg-primary rounded-pill me-1">{{ $g->orario }}</span>
                @if($g->luogo)
                    <span class="badge bg-secondary rounded-pill me-1">📍 {{ $g->luogo }}</span>
                @endif
                @if($g->note)
                    <small class="text-muted">{{ $g->note }}</small>
                @endif
            </div>
            <form action="{{ route('allenatore.stagioni.giorni.destroy', [$stagione, $g]) }}" method="POST"
                  data-confirm="Rimuovere {{ $g->label_giorno }} {{ $g->orario }}?">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger" style="font-size:.75rem;padding:.15rem .4rem">×</button>
            </form>
        </div>
        @empty
        <p class="text-muted small mb-0">Nessun giorno configurato. Aggiungine uno con il tasto sopra.</p>
        @endforelse
    </div>

    {{-- Bottone genera sedute (solo se ci sono giorni) --}}
    @if($stagione->giorniAllenamento->isNotEmpty())
    <div class="card-footer bg-transparent py-2">
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal-genera-sedute">
            ⚡ Genera sedute automaticamente
        </button>
    </div>
    @endif
</div>

{{-- ── MODAL GENERA SEDUTE ──────────────────────────────────────────────────── --}}
<div class="modal fade" id="modal-genera-sedute" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">⚡ Genera sedute automaticamente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('allenatore.stagioni.genera-sedute', $stagione) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Crea sedute <strong>bozza</strong> per tutti i giorni programmati nel periodo scelto.
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small mb-1">Da *</label>
                            <input type="date" name="da" class="form-control form-control-sm"
                                   required value="{{ $stagione->data_inizio->format('Y-m-d') }}"
                                   min="{{ $stagione->data_inizio->format('Y-m-d') }}"
                                   max="{{ $stagione->data_fine->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small mb-1">A *</label>
                            <input type="date" name="a" class="form-control form-control-sm"
                                   required value="{{ $stagione->data_fine->format('Y-m-d') }}"
                                   min="{{ $stagione->data_inizio->format('Y-m-d') }}"
                                   max="{{ $stagione->data_fine->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small mb-1">Titolo base *</label>
                        <input type="text" name="titolo_base" class="form-control form-control-sm"
                               required value="Allenamento" maxlength="120">
                        <div class="form-text">Il titolo finale sarà: "Allenamento – 18:00"</div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="salta_esistenti"
                               id="salta_esistenti" value="1" checked>
                        <label class="form-check-label small" for="salta_esistenti">
                            Salta giorni che hanno già una seduta con lo stesso titolo
                        </label>
                    </div>

                    {{-- Riepilogo giorni configurati --}}
                    <div class="mt-3 p-2 bg-light rounded" style="font-size:.82rem">
                        <strong>Giorni programmati:</strong>
                        @foreach($stagione->giorniAllenamento as $g)
                            <span class="badge bg-secondary ms-1">{{ $g->label_giorno }} {{ $g->orario }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success btn-sm">⚡ Genera</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            <a href="{{ route('allenatore.macrocicli.edit', $m) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
            <form action="{{ route('allenatore.macrocicli.destroy', $m) }}" method="POST"
                  data-confirm="Eliminare il macrociclo «{{ addslashes($m->nome) }}»? Verranno eliminati anche i microcicli collegati.">
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
