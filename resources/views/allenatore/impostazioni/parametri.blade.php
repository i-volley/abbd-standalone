@extends('layouts.allenatore')
@section('title', 'Parametri Esercizio')

@section('content')
<h2 class="mb-1">Parametri scheda esercizio</h2>
<p class="text-muted mb-4">
    Gestisci le voci dei menu (Fase, Metodologia, assi FIPAV) della scheda di creazione esercizio.
    Le voci <strong>di sistema</strong> FIPAV non sono eliminabili ma puoi disattivarle.
</p>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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
                <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size:.72rem;letter-spacing:.08em">
                    Voci di "{{ $label }}"
                </h6>

                @forelse(($parametri[$tipo] ?? collect()) as $p)
                <div class="d-flex align-items-center gap-2 py-2 border-bottom {{ $p->attivo ? '' : 'opacity-50' }}">
                    {{-- Anteprima badge --}}
                    <span class="badge rounded-pill flex-shrink-0"
                          style="background:{{ $p->colore ?? '#6c757d' }};min-width:6rem;font-size:.75rem">
                        {{ $p->etichetta }}
                    </span>

                    <form action="{{ route('allenatore.parametri.update', $p) }}" method="POST"
                          class="d-flex gap-1 align-items-center flex-grow-1 flex-wrap">
                        @csrf @method('PATCH')
                        <input type="text" name="etichetta" value="{{ $p->etichetta }}"
                               class="form-control form-control-sm" style="max-width:180px" required>
                        <input type="color" name="colore" value="{{ $p->colore ?? '#6c757d' }}"
                               class="form-control form-control-color form-control-sm"
                               style="width:2.5rem;height:2rem;padding:.1rem .2rem" title="Colore badge">
                        <input type="number" name="ordinamento" value="{{ $p->ordinamento }}"
                               class="form-control form-control-sm" style="width:4.5rem" min="0" title="Ordinamento">
                        <div class="form-check form-switch ms-1" title="Attivo nel menu">
                            <input class="form-check-input" type="checkbox" name="attivo" value="1"
                                   {{ $p->attivo ? 'checked' : '' }}>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Salva</button>
                    </form>

                    {{-- Codice valore (machine) + elimina --}}
                    <code class="text-muted small" style="min-width:5rem" title="Valore salvato">{{ $p->valore }}</code>
                    @if($p->di_sistema)
                        <span class="badge bg-light text-muted border" title="Voce FIPAV di sistema">sistema</span>
                    @else
                        <form action="{{ route('allenatore.parametri.destroy', $p) }}" method="POST"
                              data-confirm="Eliminare la voce {{ addslashes($p->etichetta) }}? Gli esercizi che la usano manterranno il valore ma non sarà più selezionabile.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">×</button>
                        </form>
                    @endif
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
                                <label class="form-label form-label-sm">Valore (opzionale)</label>
                                <input type="text" name="valore" class="form-control form-control-sm"
                                       placeholder="auto da etichetta">
                                <div class="form-text" style="font-size:.7rem">
                                    Codice salvato sugli esercizi. Lascia vuoto per generarlo dall'etichetta.
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
(function () {
    // Apri il primo tab
    const first = document.querySelector('#paramTabs .nav-link');
    if (first) new bootstrap.Tab(first).show();

    // Anteprima colore badge in tempo reale
    document.querySelectorAll('input[type="color"]').forEach(picker => {
        picker.addEventListener('input', function () {
            const row = this.closest('.d-flex');
            if (!row) return;
            const preview = row.querySelector('.badge');
            if (preview) preview.style.background = this.value;
        });
    });
})();
</script>
@endpush
