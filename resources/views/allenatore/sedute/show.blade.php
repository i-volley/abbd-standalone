@extends('layouts.allenatore')
@section('title', $seduta->titolo)

@push('styles')
<style>
.sortable-ghost { opacity: .4; background: #e8f4fd; }

/* Campo tabs */
.campo-tab-btn { border: none; opacity: .65; transition: opacity .15s; }
.campo-tab-btn.attivo { opacity: 1; box-shadow: 0 0 0 2px rgba(0,0,0,.25); }
.campo-tab-btn:hover { opacity: .9; }

/* Exercise metrics row */
.metrics-row { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 4px; }
.metrics-row .form-control,
.metrics-row .form-select { font-size: .75rem; padding: .2rem .4rem; height: 28px; }

/* Left border campo color */
.li-campo-border { border-left: 4px solid transparent; }

/* Save indicator */
.save-dot { width:7px; height:7px; border-radius:50%; display:inline-block; background:#6c757d; vertical-align:middle; }
.save-dot.saving { background:#f59e0b; }
.save-dot.saved  { background:#10b981; }
.save-dot.error  { background:#ef4444; }
</style>
@endpush

@section('content')

{{-- ── Header ───────────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2>{{ $seduta->titolo }}</h2>
        <x-stato-seduta :stato="$seduta->stato" />
        @if($seduta->visibile_atleti)
            <span class="badge bg-success ms-1">{{ __('Visibile atleti') }}</span>
        @endif
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($seduta->stato === 'bozza')
            <form action="{{ route('allenatore.sedute.pubblica', $seduta) }}" method="POST">
                @csrf<button class="btn btn-primary">{{ __('Pubblica') }}</button>
            </form>
        @endif
        <form action="{{ route('allenatore.sedute.visibilita', $seduta) }}" method="POST">
            @csrf
            <button class="btn {{ $seduta->visibile_atleti ? 'btn-warning' : 'btn-success' }}">
                {{ $seduta->visibile_atleti ? __('Nascondi atleti') : __('Rendi visibile + Notifica') }}
            </button>
        </form>
        <a href="{{ route('allenatore.sedute.edit', $seduta) }}" class="btn btn-outline-secondary">{{ __('Modifica info') }}</a>
    </div>
</div>

{{-- ── Info row ─────────────────────────────────────────────────────────────── --}}
<div class="row mb-2 g-2">
    <div class="col-auto">
        <small class="text-muted">Data: <strong>{{ $seduta->data->format('d/m/Y') }}</strong></small>
    </div>
    @if($seduta->luogo)
    <div class="col-auto">
        <small class="text-muted">📍 <strong>{{ $seduta->luogo }}</strong></small>
    </div>
    @endif
    <div class="col-auto">
        <small class="text-muted">{{ __('Durata') }}: <strong id="durata-display">{{ $seduta->durata_tot_min }}</strong> min</small>
    </div>
    @if($seduta->n_atlete)
    <div class="col-auto">
        <small class="text-muted">👥 <strong>{{ $seduta->n_atlete }}</strong> atlete</small>
    </div>
    @endif
    @if($seduta->scadenza_feedback)
    <div class="col-auto">
        <small class="text-muted">{{ __('Scadenza') }}: <strong>{{ $seduta->scadenza_feedback->format('d/m/Y H:i') }}</strong></small>
        <x-countdown-scadenza :scadenza="$seduta->scadenza_feedback" />
    </div>
    @endif
</div>

{{-- ── Obiettivi ────────────────────────────────────────────────────────────── --}}
@if($seduta->obiettivo_principale || $seduta->obiettivo_secondario)
<div class="row g-2 mb-2">
    @if($seduta->obiettivo_principale)
    <div class="col-auto">
        <span class="badge bg-primary" style="font-size:.8rem">
            🎯 {{ $seduta->obiettivo_principale }}
        </span>
    </div>
    @endif
    @if($seduta->obiettivo_secondario)
    <div class="col-auto">
        <span class="badge bg-secondary" style="font-size:.8rem">
            ↳ {{ $seduta->obiettivo_secondario }}
        </span>
    </div>
    @endif
</div>
@endif

{{-- ── Campi toolbar ────────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap" id="campi-toolbar">
    <small class="text-muted fw-semibold">{{ __('Campi:') }}</small>
    @foreach($seduta->campi as $campo)
    <span class="d-inline-flex align-items-center gap-1 badge campo-pill"
          data-campo-id="{{ $campo->id }}"
          style="background:{{ $campo->colore }};font-size:.82rem;padding:.38em .75em;cursor:default">
        <span>{{ $campo->nome }}</span>
        <button type="button"
                class="btn-close btn-close-white btn-rimuovi-campo"
                data-campo-id="{{ $campo->id }}"
                style="font-size:.55rem;opacity:.8"
                title="Rimuovi {{ $campo->nome }}"></button>
    </span>
    @endforeach
    <button class="btn btn-sm btn-outline-secondary" id="btn-aggiungi-campo" style="font-size:.8rem">
        {{ __('+ Campo') }}
    </button>
</div>

{{-- ── Split layout ─────────────────────────────────────────────────────────── --}}
<div class="catalogo-split">

    {{-- Colonna sinistra: catalogo ──────────────────────────────────────────── --}}
    <div>
        <div class="card shadow-sm mb-3">
            <div class="card-header">{{ __('Filtri catalogo') }}</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12">
                        <input type="text" id="filtro-q" class="form-control form-control-sm" placeholder="{{ __('Cerca nome...') }}">
                    </div>
                    <div class="col-6">
                        <select id="filtro-fase" class="form-select form-select-sm">
                            <option value="">{{ __('Tutte le fasi') }}</option>
                            <option value="riscaldamento">{{ __('Riscaldamento') }}</option>
                            <option value="potenziamento">{{ __('Potenziamento') }}</option>
                            <option value="stretching">{{ __('Stretching') }}</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select id="filtro-metodologia" class="form-select form-select-sm">
                            <option value="">{{ __('Tutte le metodologie') }}</option>
                            <option value="analitico">Analitico</option>
                            <option value="sintetico">Sintetico</option>
                            <option value="globale">Globale</option>
                        </select>
                    </div>
                    {{-- Campo target per l'aggiunta --}}
                    <div class="col-12" id="add-campo-wrapper" {{ $seduta->campi->isEmpty() ? 'style=display:none' : '' }}>
                        <select id="add-to-campo" class="form-select form-select-sm">
                            <option value="">{{ __('— nessun campo —') }}</option>
                            @foreach($seduta->campi as $campo)
                            <option value="{{ $campo->id }}">{{ $campo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="catalogo-risultati">
            <p class="text-muted text-center py-3">{{ __('Usa i filtri per cercare esercizi...') }}</p>
        </div>
    </div>

    {{-- Colonna destra: seduta ───────────────────────────────────────────────── --}}
    <div>
        {{-- Campo filter tabs --}}
        @if($seduta->campi->isNotEmpty())
        <div class="d-flex gap-1 mb-2 flex-wrap" id="campo-filter-tabs">
            <button class="btn btn-sm btn-dark campo-tab-btn attivo" data-filter="all"
                    style="font-size:.78rem">{{ __('Tutti') }}</button>
            @foreach($seduta->campi as $campo)
            <button class="btn btn-sm campo-tab-btn" data-filter="{{ $campo->id }}"
                    style="background:{{ $campo->colore }};color:#fff;font-size:.78rem">
                {{ $campo->nome }}
            </button>
            @endforeach
        </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('Esercizi nella seduta') }}</span>
                <div class="d-flex align-items-center gap-2">
                    <span class="save-dot" id="save-indicator" title="{{ __('Stato salvataggio metriche') }}"></span>
                    <span class="badge bg-info" id="count-esercizi">{{ $seduta->sedutaEsercizi->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @php
                    $trackLabel = [
                        'alzatore'               => 'ALZ',
                        'ricevitore_attaccante'  => 'SCH',
                        'centrale'               => 'CEN',
                        'opposto'                => 'OPP',
                        'libero'                 => 'LIB',
                    ];
                    $campiMap = $seduta->campi->keyBy('id');
                @endphp
                <ul id="lista-seduta" class="list-group list-group-flush">
                    @foreach($seduta->sedutaEsercizi as $se)
                    <li class="list-group-item li-campo-border"
                        data-pivot="{{ $se->id }}"
                        data-campo-id="{{ $se->campo_id ?? '' }}"
                        data-durata="{{ $se->esercizio->durata_min }}"
                        data-salti="{{ $se->esercizio->n_salti }}"
                        data-gesti="{{ $se->esercizio->n_gesti }}"
                        data-serie="{{ $se->serie ?? 1 }}"
                        data-rip="{{ $se->ripetizioni ?? 1 }}"
                        style="{{ $se->campo ? 'border-left-color:'.$se->campo->colore.';' : '' }}">
                        <div class="d-flex align-items-start gap-2">
                            <span class="drag-handle mt-1" style="cursor:grab;color:#aaa">&#9776;</span>
                            <div class="flex-grow-1 overflow-hidden">
                                {{-- Nome + badges --}}
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <strong class="small">{{ $se->esercizio->nome }}</strong>
                                    @if(($se->track ?? 'completo') !== 'completo')
                                    <span class="badge bg-dark rounded-pill" style="font-size:.6rem">{{ $trackLabel[$se->track] ?? $se->track }}</span>
                                    @endif
                                    @if($se->campo)
                                    <span class="badge rounded-pill campo-badge-{{ $se->id }}"
                                          style="background:{{ $se->campo->colore }};font-size:.6rem">{{ $se->campo->nome }}</span>
                                    @else
                                    <span class="badge rounded-pill campo-badge-{{ $se->id }}" style="background:#dee2e6;color:#666;font-size:.6rem"></span>
                                    @endif
                                </div>
                                {{-- Row 1: serie / rip / rec --}}
                                <div class="metrics-row">
                                    <input type="number" placeholder="Serie" class="form-control metrica"
                                           style="width:60px" name="serie"
                                           value="{{ $se->serie }}" min="1">
                                    <input type="number" placeholder="Rip." class="form-control metrica"
                                           style="width:60px" name="ripetizioni"
                                           value="{{ $se->ripetizioni }}" min="1">
                                    <input type="number" placeholder="Rec.s" class="form-control metrica"
                                           style="width:65px" name="recupero_sec"
                                           value="{{ $se->recupero_sec }}" min="0">
                                </div>
                                {{-- Row 2: fondamentale / salti / minuti / carico / campo --}}
                                <div class="metrics-row">
                                    <select class="form-select metrica" name="fondamentale_id" style="width:130px">
                                        <option value="">Fondamentale</option>
                                        @foreach($gestiFondamentali as $gf)
                                        <option value="{{ $gf->id }}"
                                            {{ $se->fondamentale_id == $gf->id ? 'selected' : '' }}>
                                            {{ $gf->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <input type="number" placeholder="Salti" class="form-control metrica"
                                           style="width:60px" name="n_salti"
                                           value="{{ $se->n_salti }}" min="0">
                                    <input type="number" placeholder="Min" class="form-control metrica"
                                           style="width:55px" name="minuti_lavoro"
                                           value="{{ $se->minuti_lavoro }}" min="0"
                                           title="{{ __('Minuti di lavoro') }}">
                                    <input type="number" placeholder="1-10" class="form-control metrica"
                                           style="width:58px" name="carico_percepito"
                                           value="{{ $se->carico_percepito }}" min="1" max="10"
                                           title="{{ __('Carico percepito (1=lieve, 10=massimo)') }}">
                                    @if($seduta->campi->isNotEmpty())
                                    <select class="form-select metrica campo-select" name="campo_id"
                                            style="width:105px" data-pivot="{{ $se->id }}">
                                        <option value="">Campo</option>
                                        @foreach($seduta->campi as $c)
                                        <option value="{{ $c->id }}"
                                                data-colore="{{ $c->colore }}"
                                            {{ $se->campo_id == $c->id ? 'selected' : '' }}>
                                            {{ $c->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1 ms-1 flex-shrink-0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-voto" type="checkbox"
                                           id="voto{{ $se->id }}"
                                           data-pivot="{{ $se->id }}"
                                           {{ $se->voto_abilitato ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="voto{{ $se->id }}">Voto</label>
                                </div>
                                <button class="btn btn-sm btn-outline-danger btn-rimuovi"
                                        data-pivot="{{ $se->id }}">✕</button>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                <p class="text-muted text-center py-4 {{ $seduta->sedutaEsercizi->count() ? 'd-none' : '' }}"
                   id="empty-msg">{{ __('+ Aggiungi') }} esercizi dal catalogo</p>
            </div>
        </div>

        {{-- Pannello carico seduta --}}
        <div class="card shadow-sm mt-3" id="card-carico">
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem;letter-spacing:.07em">
                    {{ __('Carico seduta') }}
                </small>
                <div class="d-flex align-items-center gap-2">
                    {{-- Info formula --}}
                    <span data-bs-toggle="tooltip" data-bs-html="true"
                          title="<strong>{{ __('Formula') }}</strong><br>{{ __('Salti') }}: Σ (n_salti × serie × rip)<br>{{ __('Gesti') }}: Σ (n_gesti × serie × rip)<br>{{ __('Durata') }}: Σ durata_min<br><br><em>{{ __('Soglie team') }}: ⚡ {{ $soglie['salti_warn'] }} / ⚠️ {{ $soglie['salti_danger'] }} salti &nbsp;·&nbsp; ⚡ {{ $soglie['gesti_warn'] }} / ⚠️ {{ $soglie['gesti_danger'] }} gesti</em>"
                          style="cursor:help;color:#6c757d;font-size:.85rem">ⓘ</span>
                    <small class="text-muted" id="carico-updated" style="font-size:.7rem"></small>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="d-flex gap-3 flex-wrap">
                    <div class="text-center">
                        <div class="fw-bold fs-5" id="carico-durata">0</div>
                        <div class="text-muted" style="font-size:.7rem">MIN</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5" id="carico-salti">0</div>
                        <div class="text-muted" style="font-size:.7rem">{{ strtoupper(__('Salti')) }}</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5" id="carico-gesti">0</div>
                        <div class="text-muted" style="font-size:.7rem">{{ strtoupper(__('Gesti')) }}</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5" id="carico-carico">—</div>
                        <div class="text-muted" style="font-size:.7rem">CARICO AVG</div>
                    </div>
                </div>
                <div id="carico-warning" class="mt-2" style="display:none"></div>
            </div>
        </div>

        {{-- Salva info seduta --}}
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <form action="{{ route('allenatore.sedute.update', $seduta) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="titolo" value="{{ $seduta->titolo }}">
                    <input type="hidden" name="data" value="{{ $seduta->data->format('Y-m-d') }}">
                    <input type="hidden" name="luogo" value="{{ $seduta->luogo }}">
                    <div class="row g-2 mb-2">
                        <div class="col-md-2">
                            <label class="form-label small">{{ __('N. atlete') }}</label>
                            <input type="number" name="n_atlete" class="form-control form-control-sm"
                                   value="{{ $seduta->n_atlete }}" min="1" max="100">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small">{{ __('Obiettivo principale') }}</label>
                            <input type="text" name="obiettivo_principale" class="form-control form-control-sm"
                                   value="{{ $seduta->obiettivo_principale }}"
                                   placeholder="{{ __('es. Ricezione + contrattacco') }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small">{{ __('Obiettivo secondario') }}</label>
                            <input type="text" name="obiettivo_secondario" class="form-control form-control-sm"
                                   value="{{ $seduta->obiettivo_secondario }}"
                                   placeholder="{{ __('es. Gestione del punto da seconda linea') }}">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">{{ __('Scadenza feedback') }}</label>
                        <input type="datetime-local" name="scadenza_feedback" class="form-control form-control-sm"
                               value="{{ $seduta->scadenza_feedback?->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">{{ __('Note allenatore') }}</label>
                        <textarea name="note_allenatore" class="form-control form-control-sm" rows="2">{{ $seduta->note_allenatore }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('Salva info seduta') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── Feedback ──────────────────────────────────────────────────────────────── --}}
@if($seduta->feedback->count() > 0)
<div class="card shadow-sm mt-4">
    <div class="card-header">{{ __('Feedback ricevuti') }} ({{ $seduta->feedback->count() }})</div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr><th>{{ __('Atleta') }}</th><th>RPE</th><th>{{ __('Qualità media') }}</th><th>{{ __('Impegno squadra') }}</th><th>Fond.</th><th>Nota</th></tr>
            </thead>
            <tbody>
            @foreach($seduta->feedback as $fb)
                <tr>
                    <td>{{ $fb->atleta->name }}</td>
                    <td><span class="badge bg-warning text-dark">{{ $fb->rpe }}</span></td>
                    <td>{{ $fb->qualita_prestazione }}</td>
                    <td>{{ $fb->impegno_squadra }}</td>
                    <td>{{ $fb->miglioramento_fondamentale }}/5</td>
                    <td class="small text-muted">{{ Str::limit($fb->nota, 40) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
(function() {
    const SEDUTA_ID  = {{ $seduta->id }};
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
    const lista      = document.getElementById('lista-seduta');
    const saveIndicator = document.getElementById('save-indicator');

    // Mappa colori campi (id → colore)
    const campiColori = @json($seduta->campi->pluck('colore', 'id'));
    const campiNomi   = @json($seduta->campi->pluck('nome', 'id'));

    // ── Save indicator ───────────────────────────────────────────────────────
    let saveTimer;
    function setSave(state) {
        saveIndicator.className = 'save-dot ' + state;
        if (state === 'saved') {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => saveIndicator.className = 'save-dot', 3000);
        }
    }

    // ── SortableJS ───────────────────────────────────────────────────────────
    Sortable.create(lista, {
        handle: '.drag-handle', animation: 150, ghostClass: 'sortable-ghost',
        onEnd: function() {
            const ordine = [...lista.querySelectorAll('[data-pivot]:not([style*="display:none"])')].map(el => el.dataset.pivot);
            fetch('/allenatore/sedute/' + SEDUTA_ID + '/ordine', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
                body: JSON.stringify({ordine: ordine})
            });
        }
    });

    // ── Toggle voto ──────────────────────────────────────────────────────────
    lista.addEventListener('change', function(e) {
        if (e.target.classList.contains('toggle-voto')) {
            fetch('/allenatore/sedute/' + SEDUTA_ID + '/esercizi/' + e.target.dataset.pivot + '/voto', {
                method: 'PATCH', headers: {'X-CSRF-TOKEN': CSRF}
            });
            return;
        }

        // Metrics autosave (any .metrica input/select)
        if (e.target.classList.contains('metrica') || e.target.classList.contains('campo-select')) {
            const li = e.target.closest('[data-pivot]');
            if (!li) return;
            salvaMetriche(li);

            // Se campo cambia: aggiorna border color + badge + data-campo-id
            if (e.target.name === 'campo_id') {
                const campoId = e.target.value;
                li.dataset.campoId = campoId;
                const colore = campoId ? (campiColori[campoId] || '#dee2e6') : 'transparent';
                li.style.borderLeftColor = colore;
                const badge = li.querySelector('.campo-badge-' + li.dataset.pivot);
                if (badge) {
                    badge.textContent = campoId ? (campiNomi[campoId] || '') : '';
                    badge.style.background = campoId ? colore : '#dee2e6';
                    badge.style.color = campoId ? '#fff' : '#666';
                }
            }

            // Ricalcola carico se serie/rip cambiano
            if (['serie','ripetizioni'].includes(e.target.name)) calcolaCarico();
        }
    });

    // Blur autosave per input numerici
    lista.addEventListener('blur', function(e) {
        if (e.target.classList.contains('metrica') && e.target.type === 'number') {
            const li = e.target.closest('[data-pivot]');
            if (li) salvaMetriche(li);
        }
    }, true);

    function salvaMetriche(li) {
        const pivotId = li.dataset.pivot;
        const data = {};
        li.querySelectorAll('.metrica').forEach(function(inp) {
            data[inp.name] = inp.value !== '' ? inp.value : null;
        });
        setSave('saving');
        fetch('/allenatore/sedute/' + SEDUTA_ID + '/esercizi/' + pivotId + '/metriche', {
            method: 'PATCH',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify(data)
        }).then(r => r.ok ? setSave('saved') : setSave('error'))
          .catch(() => setSave('error'));
    }

    // ── Rimuovi esercizio ────────────────────────────────────────────────────
    lista.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-rimuovi');
        if (!btn) return;
        if (!confirm('Rimuovere questo esercizio dalla seduta?')) return;
        const pivotId = btn.dataset.pivot;
        fetch('/allenatore/sedute/' + SEDUTA_ID + '/esercizi/' + pivotId, {
            method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF}
        }).then(r => r.json()).then(function(data) {
            btn.closest('li').remove();
            document.getElementById('durata-display').textContent = data.durata_tot;
            const cnt = lista.querySelectorAll('[data-pivot]').length;
            document.getElementById('count-esercizi').textContent = cnt;
            if (cnt === 0) document.getElementById('empty-msg').classList.remove('d-none');
            calcolaCarico();
        });
    });

    // ── Aggiungi esercizio dal catalogo ──────────────────────────────────────
    document.getElementById('catalogo-risultati').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-aggiungi');
        if (!btn || btn.disabled) return;
        const trackSel  = btn.closest('.d-flex')?.querySelector('.track-select');
        const track     = trackSel ? trackSel.value : 'completo';
        const campoSel  = document.getElementById('add-to-campo');
        const campo_id  = campoSel ? campoSel.value : '';
        btn.disabled = true;
        fetch('/allenatore/sedute/' + SEDUTA_ID + '/esercizi', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({esercizio_id: btn.dataset.esercizioId, track: track, campo_id: campo_id || null})
        }).then(r => r.json()).then(function(data) {
            btn.textContent = 'Aggiunto ✓';
            document.getElementById('durata-display').textContent = data.durata_tot;
            document.getElementById('empty-msg').classList.add('d-none');
            location.reload();
        });
    });

    // ── Campo filter tabs ────────────────────────────────────────────────────
    const filterTabsEl = document.getElementById('campo-filter-tabs');
    if (filterTabsEl) {
        filterTabsEl.addEventListener('click', function(e) {
            const btn = e.target.closest('.campo-tab-btn');
            if (!btn) return;
            filterTabsEl.querySelectorAll('.campo-tab-btn').forEach(b => b.classList.remove('attivo'));
            btn.classList.add('attivo');
            const filter = btn.dataset.filter;
            lista.querySelectorAll('[data-pivot]').forEach(function(li) {
                if (filter === 'all') {
                    li.style.display = '';
                } else {
                    li.style.display = (li.dataset.campoId == filter) ? '' : 'none';
                }
            });
        });
    }

    // ── Aggiungi campo ───────────────────────────────────────────────────────
    document.getElementById('btn-aggiungi-campo').addEventListener('click', function() {
        const nome = prompt('Nome del campo (es. Campo 1, Tecnica, Muro):');
        if (!nome || !nome.trim()) return;
        fetch('/allenatore/sedute/' + SEDUTA_ID + '/campi', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({nome: nome.trim()})
        }).then(r => r.json()).then(function(data) {
            location.reload();
        });
    });

    // ── Rimuovi campo ────────────────────────────────────────────────────────
    document.getElementById('campi-toolbar').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-rimuovi-campo');
        if (!btn) return;
        const campoId = btn.dataset.campoId;
        const nome    = btn.closest('.campo-pill')?.querySelector('span')?.textContent || 'questo campo';
        if (!confirm('Rimuovere "' + nome.trim() + '"? Gli esercizi assegnati perderanno il campo.')) return;
        fetch('/allenatore/sedute/' + SEDUTA_ID + '/campi/' + campoId, {
            method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF}
        }).then(r => r.json()).then(function() {
            location.reload();
        });
    });

    // ── Filtri catalogo ──────────────────────────────────────────────────────
    var timer;
    function cercaEsercizi() {
        var q    = document.getElementById('filtro-q').value;
        var fase = document.getElementById('filtro-fase').value;
        var met  = document.getElementById('filtro-metodologia').value;
        var url  = new URL('/allenatore/esercizi/cerca', window.location.origin);
        if (q)    url.searchParams.set('q', q);
        if (fase) url.searchParams.set('fase[]', fase);
        if (met)  url.searchParams.set('metodologia[]', met);
        url.searchParams.set('seduta_id', SEDUTA_ID);

        fetch(url.toString()).then(r => r.text()).then(function(html) {
            var container = document.getElementById('catalogo-risultati');
            container.textContent = '';
            var doc  = new DOMParser().parseFromString(html, 'text/html');
            doc.body.childNodes.forEach(n => container.appendChild(document.importNode(n, true)));
        });
    }

    ['filtro-q','filtro-fase','filtro-metodologia'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', function() {
            clearTimeout(timer); timer = setTimeout(cercaEsercizi, 300);
        });
    });

    cercaEsercizi();

    // ── Soglie carico (configurate per il team) ──────────────────────────────
    var SOGLIE = {
        saltiWarn:   {{ $soglie['salti_warn'] }},
        saltiDanger: {{ $soglie['salti_danger'] }},
        gestiWarn:   {{ $soglie['gesti_warn'] }},
        gestiDanger: {{ $soglie['gesti_danger'] }},
    };

    // ── Carico seduta ────────────────────────────────────────────────────────
    // Formula: totale = Σ (valore_esercizio × serie × ripetizioni)
    // n_salti e n_gesti: usa l'override del pivot se presente, altrimenti il default dell'esercizio
    function calcolaCarico() {
        var items = lista.querySelectorAll('[data-pivot]');
        var totDurata = 0, totSalti = 0, totGesti = 0;
        var carichi = [], nCarichi = 0;

        items.forEach(function(li) {
            var serieInput = li.querySelector('input[name=serie]');
            var ripInput   = li.querySelector('input[name=ripetizioni]');
            var saltiInput = li.querySelector('input[name=n_salti]');
            var serie  = parseInt(serieInput?.value) || parseInt(li.dataset.serie) || 1;
            var rip    = parseInt(ripInput?.value)   || parseInt(li.dataset.rip)   || 1;
            // FIX: usa override pivot n_salti se valorizzato, altrimenti default esercizio
            var salti  = saltiInput && saltiInput.value !== ''
                ? (parseInt(saltiInput.value) || 0)
                : (parseInt(li.dataset.salti) || 0);
            var gesti  = parseInt(li.dataset.gesti) || 0;
            var durata = parseInt(li.dataset.durata) || 0;

            totDurata += durata;
            totSalti  += salti * serie * rip;
            totGesti  += gesti * serie * rip;

            var cInput = li.querySelector('input[name=carico_percepito]');
            var c = cInput ? parseInt(cInput.value) : NaN;
            if (!isNaN(c) && c >= 1 && c <= 10) { carichi.push(c); nCarichi++; }
        });

        document.getElementById('carico-durata').textContent = totDurata;

        var elSalti = document.getElementById('carico-salti');
        var elGesti = document.getElementById('carico-gesti');
        elSalti.textContent = totSalti;
        elGesti.textContent = totGesti;
        elSalti.className = totSalti > SOGLIE.saltiDanger ? 'fw-bold fs-5 text-danger'
                          : totSalti > SOGLIE.saltiWarn   ? 'fw-bold fs-5 text-warning'
                          : 'fw-bold fs-5 text-success';
        elGesti.className = totGesti > SOGLIE.gestiDanger ? 'fw-bold fs-5 text-danger'
                          : totGesti > SOGLIE.gestiWarn   ? 'fw-bold fs-5 text-warning'
                          : 'fw-bold fs-5 text-success';

        var avgCarico = nCarichi > 0 ? (carichi.reduce((a,b)=>a+b,0)/nCarichi).toFixed(1) : '—';
        document.getElementById('carico-carico').textContent = avgCarico;

        var warns = [];
        if (totSalti > SOGLIE.saltiDanger)      warns.push('⚠️ {{ __("Volume salti molto elevato — prevedi 48h recupero") }}');
        else if (totSalti > SOGLIE.saltiWarn)   warns.push('⚡ {{ __("Volume salti alto") }}');
        if (totGesti > SOGLIE.gestiDanger)       warns.push('⚠️ {{ __("Volume gesti elevato — rischio sovraccarico") }}');

        var wEl = document.getElementById('carico-warning');
        if (warns.length) {
            wEl.innerHTML = warns.map(function(w) { return '<small class="text-danger d-block">' + w + '</small>'; }).join('');
            wEl.style.display = '';
        } else {
            wEl.style.display = 'none';
        }
        document.getElementById('carico-updated').textContent =
            new Date().toLocaleTimeString(navigator.language || 'it-IT', {hour:'2-digit',minute:'2-digit'});
    }

    lista.addEventListener('input', function(e) {
        if (e.target.type === 'number') calcolaCarico();
    });
    calcolaCarico();

})();
</script>
@endpush
