@extends('layouts.allenatore')
@section('title', $seduta->titolo)

@push('styles')
<style>
.sortable-ghost { opacity: .4; background: #e8f4fd; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2>{{ $seduta->titolo }}</h2>
        <x-stato-seduta :stato="$seduta->stato" />
        @if($seduta->visibile_atleti)
            <span class="badge bg-success ms-1">Visibile atleti</span>
        @endif
    </div>
    <div class="d-flex gap-2">
        @if($seduta->stato === 'bozza')
            <form action="{{ route('allenatore.sedute.pubblica', $seduta) }}" method="POST">
                @csrf<button class="btn btn-primary">Pubblica</button>
            </form>
        @endif
        <form action="{{ route('allenatore.sedute.visibilita', $seduta) }}" method="POST">
            @csrf
            <button class="btn {{ $seduta->visibile_atleti ? 'btn-warning' : 'btn-success' }}">
                {{ $seduta->visibile_atleti ? 'Nascondi atleti' : 'Rendi visibile + Notifica' }}
            </button>
        </form>
        <a href="{{ route('allenatore.sedute.edit', $seduta) }}" class="btn btn-outline-secondary">Modifica info</a>
    </div>
</div>

<div class="row mb-3">
    <div class="col-auto">
        <small class="text-muted">Data: <strong>{{ $seduta->data->format('d/m/Y') }}</strong></small>
    </div>
    <div class="col-auto">
        <small class="text-muted">Durata: <strong id="durata-display">{{ $seduta->durata_tot_min }}</strong> min</small>
    </div>
    @if($seduta->scadenza_feedback)
    <div class="col-auto">
        <small class="text-muted">Scadenza: <strong>{{ $seduta->scadenza_feedback->format('d/m/Y H:i') }}</strong></small>
        <x-countdown-scadenza :scadenza="$seduta->scadenza_feedback" />
    </div>
    @endif
</div>

<div class="catalogo-split">
    {{-- Colonna sinistra: catalogo filtrato --}}
    <div>
        <div class="card shadow-sm mb-3">
            <div class="card-header">Filtri catalogo</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12">
                        <input type="text" id="filtro-q" class="form-control form-control-sm" placeholder="Cerca nome...">
                    </div>
                    <div class="col-6">
                        <select id="filtro-fase" class="form-select form-select-sm">
                            <option value="">Tutte le fasi</option>
                            <option value="riscaldamento">Riscaldamento</option>
                            <option value="potenziamento">Potenziamento</option>
                            <option value="stretching">Stretching</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select id="filtro-metodologia" class="form-select form-select-sm">
                            <option value="">Tutte le metodologie</option>
                            <option value="analitico">Analitico</option>
                            <option value="sintetico">Sintetico</option>
                            <option value="globale">Globale</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="catalogo-risultati">
            <p class="text-muted text-center py-3">Usa i filtri per cercare esercizi...</p>
        </div>
    </div>

    {{-- Colonna destra: seduta in costruzione --}}
    <div>
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Esercizi nella seduta</span>
                <span class="badge bg-info" id="count-esercizi">{{ $seduta->sedutaEsercizi->count() }}</span>
            </div>
            <div class="card-body p-0">
                <ul id="lista-seduta" class="list-group list-group-flush">
                    @foreach($seduta->sedutaEsercizi as $se)
                    <li class="list-group-item" data-pivot="{{ $se->id }}"
                        data-durata="{{ $se->esercizio->durata_min }}"
                        data-salti="{{ $se->esercizio->n_salti }}"
                        data-gesti="{{ $se->esercizio->n_gesti }}"
                        data-serie="{{ $se->serie ?? 1 }}"
                        data-rip="{{ $se->ripetizioni ?? 1 }}">
                        <div class="d-flex align-items-start gap-2">
                            <span class="drag-handle mt-1">&#9776;</span>
                            <div class="flex-grow-1">
                                <strong class="small">{{ $se->esercizio->nome }}</strong>
                                <div class="d-flex gap-2 mt-1">
                                    <input type="number" placeholder="Serie" class="form-control form-control-sm" style="width:70px" value="{{ $se->serie }}">
                                    <input type="number" placeholder="Rip." class="form-control form-control-sm" style="width:70px" value="{{ $se->ripetizioni }}">
                                    <input type="number" placeholder="Rec.s" class="form-control form-control-sm" style="width:70px" value="{{ $se->recupero_sec }}">
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1 ms-1">
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
                <p class="text-muted text-center py-4 {{ $seduta->sedutaEsercizi->count() ? 'd-none' : '' }}" id="empty-msg">
                    Aggiungi esercizi dal catalogo
                </p>
            </div>
        </div>

        {{-- Pannello carico seduta --}}
        <div class="card shadow-sm mt-3" id="card-carico">
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem;letter-spacing:.07em">Carico seduta</small>
                <small class="text-muted" id="carico-updated" style="font-size:.7rem"></small>
            </div>
            <div class="card-body py-2">
                <div class="d-flex gap-3 flex-wrap">
                    <div class="text-center">
                        <div class="fw-bold fs-5" id="carico-durata">0</div>
                        <div class="text-muted" style="font-size:.7rem">MIN</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5" id="carico-salti">0</div>
                        <div class="text-muted" style="font-size:.7rem">SALTI</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5" id="carico-gesti">0</div>
                        <div class="text-muted" style="font-size:.7rem">GESTI</div>
                    </div>
                </div>
                <div id="carico-warning" class="mt-2" style="display:none"></div>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <form action="{{ route('allenatore.sedute.update', $seduta) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="titolo" value="{{ $seduta->titolo }}">
                    <input type="hidden" name="data" value="{{ $seduta->data->format('Y-m-d') }}">
                    <div class="mb-2">
                        <label class="form-label small">Scadenza feedback</label>
                        <input type="datetime-local" name="scadenza_feedback" class="form-control form-control-sm"
                               value="{{ $seduta->scadenza_feedback?->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Note allenatore</label>
                        <textarea name="note_allenatore" class="form-control form-control-sm" rows="2">{{ $seduta->note_allenatore }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Salva info seduta</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($seduta->feedback->count() > 0)
<div class="card shadow-sm mt-4">
    <div class="card-header">Feedback ricevuti ({{ $seduta->feedback->count() }})</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr><th>Atleta</th><th>RPE</th><th>Qualità</th><th>Impegno</th><th>Fond.</th><th>Nota</th></tr>
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
    const SEDUTA_ID = {{ $seduta->id }};
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const lista = document.getElementById('lista-seduta');

    // SortableJS drag & drop
    Sortable.create(lista, {
        handle: '.drag-handle', animation: 150, ghostClass: 'sortable-ghost',
        onEnd: function() {
            const ordine = [...lista.querySelectorAll('[data-pivot]')].map(el => el.dataset.pivot);
            fetch('/allenatore/sedute/' + SEDUTA_ID + '/ordine', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
                body: JSON.stringify({ordine: ordine})
            });
        }
    });

    // Toggle voto atleta
    lista.addEventListener('change', function(e) {
        if (!e.target.classList.contains('toggle-voto')) return;
        fetch('/allenatore/sedute/' + SEDUTA_ID + '/esercizi/' + e.target.dataset.pivot + '/voto', {
            method: 'PATCH', headers: {'X-CSRF-TOKEN':CSRF}
        });
    });

    // Rimuovi esercizio
    lista.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-rimuovi');
        if (!btn) return;
        if (!confirm('Rimuovere?')) return;
        const pivotId = btn.dataset.pivot;
        fetch('/allenatore/sedute/' + SEDUTA_ID + '/esercizi/' + pivotId, {
            method: 'DELETE', headers: {'X-CSRF-TOKEN':CSRF}
        }).then(r => r.json()).then(data => {
            btn.closest('li').remove();
            document.getElementById('durata-display').textContent = data.durata_tot;
            var cnt = lista.querySelectorAll('[data-pivot]').length;
            document.getElementById('count-esercizi').textContent = cnt;
            if (cnt === 0) document.getElementById('empty-msg').classList.remove('d-none');
        });
    });

    // Aggiungi esercizio (delegato su catalogo-risultati)
    document.getElementById('catalogo-risultati').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-aggiungi');
        if (!btn || btn.disabled) return;
        btn.disabled = true;
        fetch('/allenatore/sedute/' + SEDUTA_ID + '/esercizi', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({esercizio_id: btn.dataset.esercizioId})
        }).then(r => r.json()).then(function(data) {
            btn.textContent = 'Aggiunto ✓';
            document.getElementById('durata-display').textContent = data.durata_tot;
            document.getElementById('empty-msg').classList.add('d-none');
            // Ricarica intera pagina seduta per mostrare nuovo esercizio nella lista
            location.reload();
        });
    });

    // Filtri catalogo con debounce
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

        fetch(url.toString()).then(function(r){ return r.text(); }).then(function(html) {
            var container = document.getElementById('catalogo-risultati');
            container.textContent = '';
            var parser = new DOMParser();
            var doc    = parser.parseFromString(html, 'text/html');
            var nodes  = Array.from(doc.body.childNodes);
            nodes.forEach(function(n){ container.appendChild(document.importNode(n, true)); });
        });
    }

    ['filtro-q','filtro-fase','filtro-metodologia'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', function() {
            clearTimeout(timer); timer = setTimeout(cercaEsercizi, 300);
        });
    });

    cercaEsercizi();

    // ── CARICO SEDUTA ─────────────────────────────────────────────────────
    function calcolaCarico() {
        var items = lista.querySelectorAll('[data-pivot]');
        var totDurata = 0, totSalti = 0, totGesti = 0;
        items.forEach(function(li) {
            var inputs   = li.querySelectorAll('input[type=number]');
            var serie    = parseInt(inputs[0]?.value) || parseInt(li.dataset.serie) || 1;
            var rip      = parseInt(inputs[1]?.value) || parseInt(li.dataset.rip)   || 1;
            var salti    = parseInt(li.dataset.salti) || 0;
            var gesti    = parseInt(li.dataset.gesti) || 0;
            var durata   = parseInt(li.dataset.durata) || 0;
            totDurata += durata;
            totSalti  += salti * serie * rip;
            totGesti  += gesti * serie * rip;
        });
        document.getElementById('carico-durata').textContent = totDurata;
        document.getElementById('carico-salti').textContent  = totSalti;
        document.getElementById('carico-gesti').textContent  = totGesti;

        // Colorazione + warning (soglie da Metodologia 3)
        var warns = [];
        var elSalti = document.getElementById('carico-salti');
        var elGesti = document.getElementById('carico-gesti');
        elSalti.className = totSalti > 400 ? 'fw-bold fs-5 text-danger' : totSalti > 250 ? 'fw-bold fs-5 text-warning' : 'fw-bold fs-5 text-success';
        elGesti.className = totGesti > 600 ? 'fw-bold fs-5 text-danger' : totGesti > 400 ? 'fw-bold fs-5 text-warning' : 'fw-bold fs-5 text-success';
        if (totSalti > 400) warns.push('⚠️ Volume salti molto elevato — prevedi 48h di recupero prima della prossima sessione di attacco/muro');
        else if (totSalti > 250) warns.push('⚡ Volume salti alto');
        if (totGesti > 600) warns.push('⚠️ Volume gesti elevato — rischio sovraccarico tendine');

        var wEl = document.getElementById('carico-warning');
        if (warns.length) {
            wEl.innerHTML = warns.map(function(w){ return '<small class="text-danger d-block">' + w + '</small>'; }).join('');
            wEl.style.display = '';
        } else {
            wEl.style.display = 'none';
        }
        document.getElementById('carico-updated').textContent = new Date().toLocaleTimeString('it-IT', {hour:'2-digit',minute:'2-digit'});
    }

    // Ricalcola al cambiamento di serie/ripetizioni
    lista.addEventListener('input', function(e) {
        if (e.target.type === 'number') calcolaCarico();
    });
    calcolaCarico();

})();
</script>
@endpush
