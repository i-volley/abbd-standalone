@extends('layouts.allenatore')
@section('title', $stagione->nome)

@section('content')

{{-- ── HEADER ───────────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0">{{ $stagione->nome }}</h2>
        <small class="text-muted">
            {{ $stagione->data_inizio->format('d/m/Y') }} → {{ $stagione->data_fine->format('d/m/Y') }}
            · {{ $stagione->data_inizio->diffInDays($stagione->data_fine) }} {{ __('giorni') }}
        </small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('allenatore.stagioni.edit', $stagione) }}" class="btn btn-outline-secondary">{{ __('Modifica stagione') }}</a>
        <a href="{{ route('allenatore.stagioni.macrocicli.create', $stagione) }}" class="btn btn-primary">{{ __('+ Macrociclo') }}</a>
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
    {{ __('Nessun macrociclo.') }} <a href="{{ route('allenatore.stagioni.macrocicli.create', $stagione) }}">{{ __('Aggiungi il primo →') }}</a>
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
        <small class="fw-semibold">{{ __('Unità Didattiche') }}</small>
    </div>
    @endif
    @if($sedute->isNotEmpty())
    <div class="d-flex align-items-center gap-1 ms-2">
        <span class="rounded-circle d-inline-block border" style="width:.75rem;height:.75rem;background:#3b82f6"></span>
        <small class="fw-semibold">{{ __('Sedute') }}</small>
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
        <span class="fw-semibold">📅 {{ __('Giorni di allenamento programmati') }}</span>
        <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#form-aggiungi-giorno">
            {{ __('+ Aggiungi giorno') }}
        </button>
    </div>

    {{-- Form aggiungi giorno (collassabile) --}}
    <div class="collapse" id="form-aggiungi-giorno">
        <div class="card-body border-bottom bg-light">
            <form action="{{ route('allenatore.stagioni.giorni.store', $stagione) }}" method="POST">
                @csrf
                {{-- Riga 1: giorno, tipo, nome seduta --}}
                <div class="row g-2 mb-2">
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">{{ __('Giorno *') }}</label>
                        <select name="giorno_settimana" class="form-select form-select-sm" required>
                            @foreach(\App\Models\GiornoAllenamento::labelGiorni() as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label small mb-1">{{ __('Tipo allenamento') }}</label>
                        <select name="tipo_allenamento_id" class="form-select form-select-sm">
                            <option value="">{{ __('— nessuno —') }}</option>
                            @foreach($tipiAllenamento as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small mb-1">{{ __('Nome seduta *') }}</label>
                        <input type="text" name="titolo_base" id="add_titolo_base" class="form-control form-control-sm"
                               required placeholder="es. Sala Pesi, Tecnico Tattico" maxlength="120">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label small mb-1">{{ __('Note') }}</label>
                        <input type="text" name="note" class="form-control form-control-sm" placeholder="{{ __('note') }}" maxlength="255">
                    </div>
                </div>
                {{-- Riga 2: orari --}}
                <div class="row g-2 mb-2">
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">{{ __('Inizio *') }}</label>
                        <input type="time" name="ora_inizio" id="add_ora_inizio" class="form-control form-control-sm" required value="18:00">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">{{ __('Fine') }}</label>
                        <input type="time" name="ora_fine" class="form-control form-control-sm" value="20:00">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">{{ __('Ritrovo') }}</label>
                        <input type="time" name="ora_ritrovo" id="add_ora_ritrovo" class="form-control form-control-sm"
                               placeholder="auto -15min">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small mb-1">{{ __('Note ritrovo') }}</label>
                        <input type="text" name="note_ritrovo" class="form-control form-control-sm"
                               placeholder="es. ingresso laterale" maxlength="255">
                    </div>
                </div>
                {{-- Riga 3: luogo con mappa --}}
                <div class="row g-2 mb-2">
                    <div class="col-sm-3">
                        <label class="form-label small mb-1">{{ __('Nome luogo') }}</label>
                        <input type="text" name="luogo" class="form-control form-control-sm" placeholder="Palestra A" maxlength="255">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small mb-1">{{ __('Indirizzo') }}</label>
                        <input type="text" name="indirizzo" id="add_indirizzo" class="form-control form-control-sm"
                               placeholder="Via Roma 1" maxlength="255">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small mb-1">{{ __('Città') }}</label>
                        <input type="text" name="citta" id="add_citta" class="form-control form-control-sm"
                               placeholder="Milano" maxlength="100">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label small mb-1">{{ __('Cerca su mappa') }}</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="add_cerca_mappa" class="form-control form-control-sm"
                                   placeholder="Cerca indirizzo...">
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="cercaSuMappa('add')">🔍</button>
                        </div>
                    </div>
                </div>
                {{-- Mappa Leaflet add --}}
                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <div id="add_map" style="height:200px;border-radius:.4rem;border:1px solid #dee2e6;display:none"></div>
                        <input type="hidden" name="lat" id="add_lat">
                        <input type="hidden" name="lng" id="add_lng">
                        <button type="button" class="btn btn-link btn-sm p-0 mt-1" onclick="toggleMappa('add')">
                            🗺️ {{ __('Mostra/nascondi mappa') }}
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-text">
                        Seduta generata: <em>"[Nome seduta] [Giorno] [Ora]"</em> — es. <strong>Sala Pesi Lunedì 09:00</strong>
                    </div>
                    <button class="btn btn-sm btn-success">💾 {{ __('Salva giorno') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista giorni configurati, ognuno con il proprio bottone Genera --}}
    <div class="card-body py-2">
        @forelse($stagione->giorniAllenamento as $g)
        <div class="border-bottom py-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                {{-- Info giorno --}}
                <div class="d-flex align-items-center flex-wrap gap-1">
                    <span class="fw-semibold">{{ $g->titolo_base ?? 'Allenamento' }}</span>
                    <span class="badge bg-dark rounded-pill">{{ $g->label_giorno }}</span>
                    <span class="badge bg-primary rounded-pill">{{ $g->orario }}</span>
                    @if($g->luogo)
                        <span class="badge bg-secondary rounded-pill">📍 {{ $g->luogo }}</span>
                    @endif
                    @if($g->note)
                        <small class="text-muted">{{ $g->note }}</small>
                    @endif
                    <small class="text-muted" style="font-size:.72rem">
                        → seduta: <em>{{ ($g->titolo_base ?? 'Allenamento') . ' ' . $g->label_giorno . ' ' . substr($g->ora_inizio, 0, 5) }}</em>
                    </small>
                </div>
                {{-- Azioni --}}
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-modifica-{{ $g->id }}">
                        ✏️
                    </button>
                    <button class="btn btn-sm btn-outline-success"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-genera-{{ $g->id }}">
                        ⚡ {{ __('Genera') }}
                    </button>
                    <form action="{{ route('allenatore.stagioni.giorni.destroy', [$stagione, $g]) }}" method="POST"
                          data-confirm="Rimuovere {{ $g->label_giorno }} {{ $g->orario }}?">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">×</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal genera per questo specifico giorno --}}
        <div class="modal fade" id="modal-genera-{{ $g->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title">⚡ Genera «{{ ($g->titolo_base ?? 'Allenamento') . ' ' . $g->label_giorno . ' ' . substr($g->ora_inizio,0,5) }}»</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('allenatore.stagioni.giorni.genera', [$stagione, $g]) }}" method="POST">
                        @csrf
                        <div class="modal-body pb-1">
                            <p class="small text-muted mb-3">
                                Crea sedute <strong>bozza</strong> per ogni <strong>{{ $g->label_giorno }}</strong>
                                nel periodo scelto. Salta date con seduta già esistente (stesso titolo+data).
                            </p>
                            <div class="row g-2">
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
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Annulla') }}</button>
                            <button type="submit" class="btn btn-success btn-sm">⚡ {{ __('Genera') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- Modal modifica per questo specifico giorno --}}
        <div class="modal fade" id="modal-modifica-{{ $g->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title">✏️ Modifica — {{ $g->titolo_base ?? 'Allenamento' }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('allenatore.stagioni.giorni.update', [$stagione, $g]) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="row g-2">
                                {{-- Giorno + Tipo + Nome --}}
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">{{ __('Giorno *') }}</label>
                                    <select name="giorno_settimana" class="form-select form-select-sm" required>
                                        @foreach(\App\Models\GiornoAllenamento::labelGiorni() as $val => $lbl)
                                            <option value="{{ $val }}" {{ $g->giorno_settimana == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">{{ __('Tipo') }}</label>
                                    <select name="tipo_allenamento_id" class="form-select form-select-sm">
                                        <option value="">{{ __('— nessuno —') }}</option>
                                        @foreach($tipiAllenamento as $tipo)
                                            <option value="{{ $tipo->id }}" {{ $g->tipo_allenamento_id == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small mb-1">{{ __('Nome seduta *') }}</label>
                                    <input type="text" name="titolo_base" class="form-control form-control-sm"
                                           required maxlength="120" value="{{ $g->titolo_base }}">
                                </div>
                                {{-- Orari --}}
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">{{ __('Inizio *') }}</label>
                                    <input type="time" name="ora_inizio" id="edit_ora_inizio_{{ $g->id }}"
                                           class="form-control form-control-sm edit-ora-inizio"
                                           data-ritrovo-id="edit_ora_ritrovo_{{ $g->id }}"
                                           required value="{{ substr($g->ora_inizio, 0, 5) }}">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">{{ __('Fine') }}</label>
                                    <input type="time" name="ora_fine" class="form-control form-control-sm"
                                           value="{{ $g->ora_fine ? substr($g->ora_fine, 0, 5) : '' }}">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">{{ __('Ritrovo') }}</label>
                                    <input type="time" name="ora_ritrovo" id="edit_ora_ritrovo_{{ $g->id }}"
                                           class="form-control form-control-sm"
                                           value="{{ $g->ora_ritrovo ? substr($g->ora_ritrovo, 0, 5) : '' }}">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">{{ __('Note ritrovo') }}</label>
                                    <input type="text" name="note_ritrovo" class="form-control form-control-sm"
                                           maxlength="255" value="{{ $g->note_ritrovo }}">
                                </div>
                                {{-- Luogo --}}
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">{{ __('Nome luogo') }}</label>
                                    <input type="text" name="luogo" class="form-control form-control-sm"
                                           maxlength="255" value="{{ $g->luogo }}">
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label small mb-1">{{ __('Indirizzo') }}</label>
                                    <input type="text" name="indirizzo" id="edit_indirizzo_{{ $g->id }}"
                                           class="form-control form-control-sm" maxlength="255" value="{{ $g->indirizzo }}">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small mb-1">{{ __('Città') }}</label>
                                    <input type="text" name="citta" id="edit_citta_{{ $g->id }}"
                                           class="form-control form-control-sm" maxlength="100" value="{{ $g->citta }}">
                                </div>
                                {{-- Cerca mappa --}}
                                <div class="col-sm-8">
                                    <label class="form-label small mb-1">{{ __('Cerca su mappa') }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="edit_cerca_mappa_{{ $g->id }}"
                                               class="form-control form-control-sm" placeholder="Cerca indirizzo..."
                                               value="{{ $g->indirizzo ? $g->indirizzo . ($g->citta ? ', '.$g->citta : '') : '' }}">
                                        <button type="button" class="btn btn-outline-secondary"
                                                onclick="cercaSuMappa('edit_{{ $g->id }}')">🔍</button>
                                    </div>
                                </div>
                                <div class="col-sm-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                            onclick="toggleMappa('edit_{{ $g->id }}')">
                                        🗺️ Mappa
                                    </button>
                                </div>
                                {{-- Mappa --}}
                                <div class="col-12">
                                    <div id="edit_map_{{ $g->id }}" style="height:200px;border-radius:.4rem;border:1px solid #dee2e6;display:{{ $g->lat ? 'block' : 'none' }}"></div>
                                    <input type="hidden" name="lat" id="edit_lat_{{ $g->id }}" value="{{ $g->lat }}">
                                    <input type="hidden" name="lng" id="edit_lng_{{ $g->id }}" value="{{ $g->lng }}">
                                </div>
                                {{-- Note generali --}}
                                <div class="col-12">
                                    <label class="form-label small mb-1">{{ __('Note') }}</label>
                                    <input type="text" name="note" class="form-control form-control-sm"
                                           maxlength="255" value="{{ $g->note }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Annulla') }}</button>
                            <button type="submit" class="btn btn-primary btn-sm">💾 {{ __('Salva modifiche') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <p class="text-muted small mb-0">{{ __('Nessun giorno configurato. Aggiungine uno con il tasto sopra.') }}</p>
        @endforelse
    </div>
</div>

{{-- ── LISTA MACROCICLI ────────────────────────────────────────────────────── --}}
<h5 class="fw-bold mb-3">{{ __('Macrocicli') }}</h5>

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
            <a href="{{ route('allenatore.macrocicli.show', $m) }}" class="btn btn-sm btn-outline-primary">{{ __('Apri') }}</a>
            <a href="{{ route('allenatore.macrocicli.edit', $m) }}" class="btn btn-sm btn-outline-secondary">{{ __('Modifica') }}</a>
            <form action="{{ route('allenatore.macrocicli.destroy', $m) }}" method="POST"
                  data-confirm="Eliminare il macrociclo «{{ addslashes($m->nome) }}»? Verranno eliminati anche i microcicli collegati.">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">×</button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">{{ __('Nessun macrociclo.') }}</div>
@endforelse


{{-- ── RIEPILOGO SEDUTE ────────────────────────────────────────────────────── --}}
@if($sedute->isNotEmpty())
<h5 class="fw-bold mt-4 mb-3">
    {{ __('Sedute della stagione') }}
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
    {{ __('Unità Didattiche') }}
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
                        {{ __('dal') }} {{ $ud->data_inizio->format('d/m/Y') }}
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

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
// ── Tooltip Bootstrap ─────────────────────────────────────────────────────────
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top' });
});

// ── Mappe Leaflet per giorni allenamento ─────────────────────────────────────
const _mappe = {};

function getOsubCreate(prefix) {
    if (_mappe[prefix]) return _mappe[prefix];
    const mapEl = document.getElementById(prefix + '_map') ||
                  document.getElementById('edit_map_' + prefix.replace('edit_',''));
    if (!mapEl) return null;
    mapEl.style.display = 'block';
    const latEl = document.getElementById(prefix + '_lat') ||
                  document.getElementById('edit_lat_' + prefix.replace('edit_',''));
    const lngEl = document.getElementById(prefix + '_lng') ||
                  document.getElementById('edit_lng_' + prefix.replace('edit_',''));
    const initLat = parseFloat(latEl?.value) || 45.4642;
    const initLng = parseFloat(lngEl?.value) || 9.1900;
    const zoom    = latEl?.value ? 15 : 10;
    const map = L.map(mapEl).setView([initLat, initLng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
    const marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);
    marker.on('dragend', () => {
        const ll = marker.getLatLng();
        if (latEl) latEl.value = ll.lat.toFixed(7);
        if (lngEl) lngEl.value = ll.lng.toFixed(7);
        reverseGeocode(ll.lat, ll.lng, prefix);
    });
    map.on('click', e => {
        marker.setLatLng(e.latlng);
        if (latEl) latEl.value = e.latlng.lat.toFixed(7);
        if (lngEl) lngEl.value = e.latlng.lng.toFixed(7);
        reverseGeocode(e.latlng.lat, e.latlng.lng, prefix);
    });
    _mappe[prefix] = { map, marker };
    setTimeout(() => map.invalidateSize(), 300);
    return _mappe[prefix];
}

function toggleMappa(prefix) {
    const mapElId = prefix.startsWith('edit_') ? 'edit_map_' + prefix.slice(5) : prefix + '_map';
    const mapEl = document.getElementById(mapElId);
    if (!mapEl) return;
    if (mapEl.style.display === 'none') {
        mapEl.style.display = 'block';
        getOsubCreate(prefix);
    } else {
        mapEl.style.display = 'none';
    }
}

function cercaSuMappa(prefix) {
    const cercaElId = prefix.startsWith('edit_') ? 'edit_cerca_mappa_' + prefix.slice(5) : prefix + '_cerca_mappa';
    const query = document.getElementById(cercaElId)?.value;
    if (!query) return;
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&addressdetails=1`, {
        headers: { 'Accept-Language': 'it' }
    }).then(r => r.json()).then(results => {
        if (!results.length) { alert('Nessun risultato trovato.'); return; }
        const r = results[0];
        const lat = parseFloat(r.lat), lng = parseFloat(r.lon);
        // Mostra mappa
        const mapElId = prefix.startsWith('edit_') ? 'edit_map_' + prefix.slice(5) : prefix + '_map';
        const mapEl = document.getElementById(mapElId);
        if (mapEl) mapEl.style.display = 'block';
        const obj = getOsubCreate(prefix);
        if (obj) {
            obj.map.setView([lat, lng], 15);
            obj.marker.setLatLng([lat, lng]);
        }
        // Aggiorna hidden + campi
        const latElId = prefix.startsWith('edit_') ? 'edit_lat_' + prefix.slice(5) : prefix + '_lat';
        const lngElId = prefix.startsWith('edit_') ? 'edit_lng_' + prefix.slice(5) : prefix + '_lng';
        const indirizzoElId = prefix.startsWith('edit_') ? 'edit_indirizzo_' + prefix.slice(5) : prefix + '_indirizzo';
        const cittaElId = prefix.startsWith('edit_') ? 'edit_citta_' + prefix.slice(5) : prefix + '_citta';
        const el = id => document.getElementById(id);
        if (el(latElId)) el(latElId).value = lat.toFixed(7);
        if (el(lngElId)) el(lngElId).value = lng.toFixed(7);
        const addr = r.address || {};
        const via = addr.road || addr.pedestrian || '';
        const num = addr.house_number ? ' ' + addr.house_number : '';
        if (el(indirizzoElId)) el(indirizzoElId).value = via + num;
        if (el(cittaElId)) el(cittaElId).value = addr.city || addr.town || addr.village || addr.municipality || '';
    });
}

function reverseGeocode(lat, lng, prefix) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`, {
        headers: { 'Accept-Language': 'it' }
    }).then(r => r.json()).then(r => {
        const addr = r.address || {};
        const via = addr.road || addr.pedestrian || '';
        const num = addr.house_number ? ' ' + addr.house_number : '';
        const indirizzoElId = prefix.startsWith('edit_') ? 'edit_indirizzo_' + prefix.slice(5) : prefix + '_indirizzo';
        const cittaElId = prefix.startsWith('edit_') ? 'edit_citta_' + prefix.slice(5) : prefix + '_citta';
        const el = id => document.getElementById(id);
        if (el(indirizzoElId)) el(indirizzoElId).value = via + num;
        if (el(cittaElId)) el(cittaElId).value = addr.city || addr.town || addr.village || addr.municipality || '';
    });
}

// ── Auto-suggest ora ritrovo = inizio - 15min ─────────────────────────────────
function suggerisciRitrovo(oraInizioEl, oraRitrovoEl) {
    if (!oraInizioEl.value || oraRitrovoEl.value) return; // non sovrascrivere se già impostato
    const [h, m] = oraInizioEl.value.split(':').map(Number);
    const tot = h * 60 + m - 15;
    if (tot < 0) return;
    oraRitrovoEl.value = String(Math.floor(tot / 60)).padStart(2, '0') + ':' + String(tot % 60).padStart(2, '0');
}

// Aggiungi giorno: suggerisci ritrovo al cambio ora inizio
const addInizio = document.getElementById('add_ora_inizio');
const addRitrovo = document.getElementById('add_ora_ritrovo');
if (addInizio && addRitrovo) {
    addInizio.addEventListener('change', () => suggerisciRitrovo(addInizio, addRitrovo));
}

// Edit modali: suggerisci ritrovo
document.querySelectorAll('.edit-ora-inizio').forEach(el => {
    el.addEventListener('change', function() {
        const ritrovoId = this.dataset.ritrovoId;
        const ritrovoEl = document.getElementById(ritrovoId);
        if (ritrovoEl) suggerisciRitrovo(this, ritrovoEl);
    });
});

// Auto-fill tipo → nome seduta (se nome è ancora vuoto)
document.querySelectorAll('[name="tipo_allenamento_id"]').forEach(sel => {
    sel.addEventListener('change', function() {
        const form = this.closest('form');
        const titoloEl = form?.querySelector('[name="titolo_base"]');
        if (titoloEl && !titoloEl.value) {
            titoloEl.value = this.options[this.selectedIndex]?.text !== '— nessuno —'
                ? this.options[this.selectedIndex].text : '';
        }
    });
});

// Inizializza mappe per giorni che hanno già coordinate
@foreach($stagione->giorniAllenamento as $g)
@if($g->lat && $g->lng)
document.addEventListener('DOMContentLoaded', () => {
    // Lazy init: solo quando il modal viene aperto
    document.getElementById('modal-modifica-{{ $g->id }}')?.addEventListener('shown.bs.modal', () => {
        getOsubCreate('edit_{{ $g->id }}');
    });
});
@endif
@endforeach
</script>
@endpush
