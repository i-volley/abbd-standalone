@extends('layouts.allenatore')
@section('title', 'Parametri Esercizio')

@section('content')
<h2 class="mb-1">Parametri scheda esercizio</h2>
<p class="text-muted mb-4">
    Gestisci le voci dei menu (Fase, Metodologia, assi FIPAV) della scheda di creazione esercizio.
    Le voci <strong>di sistema</strong> FIPAV non sono eliminabili ma puoi disattivarle.
</p>

<x-alert />

{{-- ── TABS PER TIPO ───────────────────────────────────────────────────────── --}}
<ul class="nav nav-tabs mb-0" id="paramTabs" role="tablist">
    @foreach($tipi as $tipo => $label)
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-{{ $tipo }}"
                data-bs-toggle="tab" data-bs-target="#pane-{{ $tipo }}"
                type="button" role="tab">
            {{ $label }}
            <span class="badge bg-secondary ms-1" style="font-size:.65rem">
                {{ ($parametri[$tipo] ?? collect())->count() }}
            </span>
        </button>
    </li>
    @endforeach
</ul>

<div class="tab-content border border-top-0 rounded-bottom p-4 bg-white shadow-sm">
    @foreach($tipi as $tipo => $label)
    <div class="tab-pane fade" id="pane-{{ $tipo }}" role="tabpanel">
        <div class="row g-4">

            {{-- ── ELENCO VOCI ──────────────────────────────────────────────── --}}
            <div class="col-lg-8">
                <h6 class="text-uppercase text-muted fw-bold mb-3"
                    style="font-size:.72rem;letter-spacing:.08em">
                    Voci di "{{ $label }}"
                </h6>

                @forelse(($parametri[$tipo] ?? collect()) as $p)
                <div class="border-bottom {{ $p->attivo ? '' : 'opacity-50' }}" id="row-{{ $p->id }}">

                    {{-- ── Riga compatta (default) ────────────────────────── --}}
                    <div class="d-flex align-items-center gap-2 py-2 param-row-view">
                        <span class="badge rounded-pill flex-shrink-0"
                              style="background:{{ $p->colore ?? '#6c757d' }};min-width:5rem;font-size:.75rem">
                            {{ $p->etichetta }}
                        </span>
                        <code class="text-muted small flex-grow-1">{{ $p->valore }}</code>
                        @unless($p->attivo)
                            <span class="badge bg-secondary" style="font-size:.65rem">disattivo</span>
                        @endunless
                        @if($p->di_sistema)
                            <span class="badge bg-light text-muted border" style="font-size:.65rem">sistema</span>
                        @endif

                        {{-- Pulsante modifica --}}
                        <button type="button" class="btn btn-sm btn-outline-primary param-edit-toggle"
                                data-target="edit-{{ $p->id }}" title="Modifica">✏️</button>

                        {{-- Elimina (solo non-sistema) --}}
                        @if(!$p->di_sistema)
                        <form action="{{ route('allenatore.parametri.destroy', $p) }}" method="POST"
                              data-confirm="Eliminare «{{ addslashes($p->etichetta) }}»? Gli esercizi già assegnati manterranno il valore.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Elimina">×</button>
                        </form>
                        @endif
                    </div>

                    {{-- ── Pannello modifica (nascosto di default) ─────────── --}}
                    <div class="py-2 px-1 param-edit-panel d-none" id="edit-{{ $p->id }}">
                        <form action="{{ route('allenatore.parametri.update', $p) }}" method="POST"
                              class="d-flex gap-2 align-items-end flex-wrap">
                            @csrf @method('PATCH')

                            <div>
                                <label class="form-label form-label-sm mb-1">Etichetta</label>
                                <input type="text" name="etichetta" value="{{ $p->etichetta }}"
                                       class="form-control form-control-sm" style="width:160px" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm mb-1">Colore</label>
                                <input type="color" name="colore" value="{{ $p->colore ?? '#6c757d' }}"
                                       class="form-control form-control-color form-control-sm"
                                       style="width:2.5rem;height:2rem;padding:.1rem .2rem">
                            </div>
                            <div>
                                <label class="form-label form-label-sm mb-1">Ordine</label>
                                <input type="number" name="ordinamento" value="{{ $p->ordinamento }}"
                                       class="form-control form-control-sm" style="width:4.5rem" min="0">
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <label class="form-label form-label-sm mb-1">Attivo</label>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox"
                                           name="attivo" value="1"
                                           {{ $p->attivo ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-sm btn-primary">💾 Salva</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary param-edit-cancel"
                                        data-target="edit-{{ $p->id }}">Annulla</button>
                            </div>
                        </form>
                    </div>

                </div>
                @empty
                <p class="text-muted fst-italic small">Nessuna voce. Aggiungine una →</p>
                @endforelse
            </div>

            {{-- ── FORM AGGIUNTA ────────────────────────────────────────────── --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent fw-semibold">+ Aggiungi voce "{{ $label }}"</div>
                    <div class="card-body">
                        <form action="{{ route('allenatore.parametri.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="{{ $tipo }}">

                            <div class="mb-2">
                                <label class="form-label form-label-sm">Etichetta *</label>
                                <input type="text" name="etichetta" class="form-control form-control-sm"
                                       placeholder="es. Trasformazione" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label form-label-sm">Valore <small class="text-muted">(opzionale)</small></label>
                                <input type="text" name="valore" class="form-control form-control-sm"
                                       placeholder="auto da etichetta">
                                <div class="form-text" style="font-size:.7rem">
                                    Codice salvato sugli esercizi. Lascia vuoto = generato dall'etichetta.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-sm">Colore badge</label>
                                <input type="color" name="colore" value="#0d6efd"
                                       class="form-control form-control-color form-control-sm"
                                       style="width:3rem;height:2rem;padding:.1rem .2rem">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Aggiungi voce</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function () {

    // Apri il primo tab
    var first = document.querySelector('#paramTabs .nav-link');
    if (first) bootstrap.Tab.getOrCreateInstance(first).show();

    // Toggle pannello modifica
    document.querySelectorAll('.param-edit-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var panel = document.getElementById(this.dataset.target);
            if (!panel) return;
            var row = panel.previousElementSibling;
            panel.classList.toggle('d-none');
            if (row) row.classList.toggle('d-none');
        });
    });

    // Annulla modifica
    document.querySelectorAll('.param-edit-cancel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var panel = document.getElementById(this.dataset.target);
            if (!panel) return;
            panel.classList.add('d-none');
            var row = panel.previousElementSibling;
            if (row) row.classList.remove('d-none');
        });
    });

    // Anteprima colore badge in tempo reale
    document.querySelectorAll('input[type="color"]').forEach(function (picker) {
        picker.addEventListener('input', function () {
            var panel = this.closest('.param-edit-panel');
            if (!panel) return;
            var row = panel.previousElementSibling;
            if (!row) return;
            var badge = row.querySelector('.badge');
            if (badge) badge.style.background = this.value;
        });
    });

});
</script>
@endpush
