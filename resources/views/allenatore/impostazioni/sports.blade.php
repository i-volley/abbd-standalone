@extends('layouts.allenatore')
@section('title', 'Impostazioni')

@section('content')
<h2 class="mb-4">Impostazioni — Sport &amp; Gesti Tecnici</h2>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── TABS SPORT ───────────────────────────────────────────────────────────── --}}
<ul class="nav nav-tabs mb-0" id="sportTabs" role="tablist">
    @foreach($sports as $sport)
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-sport-{{ $sport->id }}"
                data-bs-toggle="tab" data-bs-target="#pane-sport-{{ $sport->id }}"
                type="button" role="tab">
            {{ $sport->nome }}
            <span class="badge bg-secondary ms-1" style="font-size:.65rem">{{ $sport->gestiTecnici->count() }}</span>
        </button>
    </li>
    @endforeach
    <li class="nav-item" role="presentation">
        <button class="nav-link text-success fw-semibold" id="tab-nuovo-sport"
                data-bs-toggle="tab" data-bs-target="#pane-nuovo-sport"
                type="button" role="tab">
            + Nuovo sport
        </button>
    </li>
</ul>

<div class="tab-content border border-top-0 rounded-bottom p-4 bg-white shadow-sm">

    @foreach($sports as $sport)
    <div class="tab-pane fade" id="pane-sport-{{ $sport->id }}" role="tabpanel">
        <div class="row g-4">

            {{-- ── COLONNA SINISTRA: categorie + gesti ──────────────────────── --}}
            <div class="col-lg-7">

                {{-- CATEGORIE ────────────────────────────────────────────────── --}}
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size:.72rem;letter-spacing:.08em">
                    Categorie
                </h6>

                @forelse($sport->categorieGesto as $cat)
                <div class="d-flex align-items-center gap-2 py-2 border-bottom">
                    {{-- Preview badge --}}
                    <span class="badge rounded-pill flex-shrink-0"
                          style="background:{{ $cat->colore }};min-width:5rem;font-size:.75rem">
                        {{ $cat->nome }}
                    </span>

                    {{-- Inline edit form --}}
                    <form action="{{ route('allenatore.categorie-gesto.update', $cat) }}" method="POST"
                          class="d-flex gap-1 align-items-center flex-grow-1">
                        @csrf @method('PATCH')
                        <input type="text" name="nome" value="{{ $cat->nome }}"
                               class="form-control form-control-sm" style="max-width:180px" required>
                        <input type="color" name="colore" value="{{ $cat->colore }}"
                               class="form-control form-control-color form-control-sm"
                               style="width:2.5rem;height:2rem;padding:.1rem .2rem" title="Scegli colore">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Salva</button>
                    </form>

                    {{-- Elimina categoria --}}
                    <form action="{{ route('allenatore.categorie-gesto.destroy', $cat) }}" method="POST"
                          onsubmit="return confirm('Eliminare la categoria \'{{ addslashes($cat->nome) }}\'? I gesti associati perderanno la categoria.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">×</button>
                    </form>
                </div>
                @empty
                <p class="text-muted fst-italic small mb-2">Nessuna categoria. Aggiungine una →</p>
                @endforelse

                {{-- Aggiungi categoria --}}
                <form action="{{ route('allenatore.categorie-gesto.store') }}" method="POST"
                      class="d-flex gap-2 align-items-center mt-2 mb-4">
                    @csrf
                    <input type="hidden" name="sport_id" value="{{ $sport->id }}">
                    <input type="text" name="nome" class="form-control form-control-sm"
                           placeholder="Nuova categoria..." style="max-width:200px" required>
                    <input type="color" name="colore" value="#0d6efd"
                           class="form-control form-control-color form-control-sm"
                           style="width:2.5rem;height:2rem;padding:.1rem .2rem" title="Scegli colore">
                    <button type="submit" class="btn btn-sm btn-primary">+ Aggiungi</button>
                </form>

                {{-- GESTI TECNICI ─────────────────────────────────────────────── --}}
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size:.72rem;letter-spacing:.08em">
                    Gesti tecnici <span class="badge bg-secondary rounded-pill">{{ $sport->gestiTecnici->count() }}</span>
                </h6>

                @forelse($sport->gestiTecnici as $g)
                <div class="d-flex align-items-center gap-2 py-2 border-bottom">
                    <span class="fw-semibold flex-grow-1">{{ $g->nome }}</span>
                    <x-badge-categoria-gesto :categoria="$g->categoriaGesto" />
                    <span class="text-muted small" style="min-width:3rem">№ {{ $g->ordinamento }}</span>
                    <a href="{{ route('allenatore.gesti-tecnici.edit', $g) }}"
                       class="btn btn-sm btn-outline-secondary">Modifica</a>
                    <form action="{{ route('allenatore.gesti-tecnici.destroy', $g) }}" method="POST"
                          onsubmit="return confirm('Eliminare {{ addslashes($g->nome) }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">×</button>
                    </form>
                </div>
                @empty
                <p class="text-muted fst-italic small">Nessun gesto tecnico. Aggiungine uno →</p>
                @endforelse

            </div>

            {{-- ── COLONNA DESTRA: form aggiungi gesto + danger zone ─────────── --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent fw-semibold">+ Aggiungi gesto tecnico</div>
                    <div class="card-body">
                        <form action="{{ route('allenatore.gesti-tecnici.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="sport_id" value="{{ $sport->id }}">

                            <div class="mb-2">
                                <label class="form-label form-label-sm">Nome *</label>
                                <input type="text" name="nome" class="form-control form-control-sm"
                                       placeholder="es. Battuta float" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label form-label-sm">Categoria *</label>
                                <select name="categoria_id" class="form-select form-select-sm" required>
                                    <option value="">Scegli categoria...</option>
                                    @foreach($sport->categorieGesto as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                                    @endforeach
                                </select>
                                @if($sport->categorieGesto->isEmpty())
                                <div class="form-text text-warning">Crea prima almeno una categoria.</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label form-label-sm">Ordinamento</label>
                                <input type="number" name="ordinamento" class="form-control form-control-sm"
                                       value="{{ $sport->gestiTecnici->count() + 1 }}" min="1">
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100"
                                    {{ $sport->categorieGesto->isEmpty() ? 'disabled' : '' }}>
                                Aggiungi gesto tecnico
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-3">
                    <details>
                        <summary class="text-danger small" style="cursor:pointer">Zona pericolosa</summary>
                        <div class="mt-2">
                            <form action="{{ route('allenatore.sports.destroy', $sport) }}" method="POST"
                                  onsubmit="return confirm('ATTENZIONE: eliminare {{ addslashes($sport->nome) }} eliminerà anche tutti i gesti tecnici e le categorie associate. Continuare?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger w-100">Elimina sport {{ $sport->nome }}</button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>

        </div>
    </div>
    @endforeach

    {{-- ── PANNELLO NUOVO SPORT ─────────────────────────────────────────────── --}}
    <div class="tab-pane fade" id="pane-nuovo-sport" role="tabpanel">
        <div class="row">
            <div class="col-md-5">
                <h5 class="mb-3 fw-bold">Aggiungi nuovo sport</h5>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('allenatore.sports.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nome sport *</label>
                                <input type="text" name="nome" class="form-control"
                                       placeholder="es. Basket, Calcio, Tennis..." required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Crea sport</button>
                        </form>
                    </div>
                </div>
                <p class="text-muted small mt-2">
                    Dopo aver creato lo sport, selezionalo dal tab per aggiungere categorie e gesti tecnici.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const openSport = '{{ request('open_sport') }}';
    let targetId = '';

    if (openSport) {
        targetId = 'tab-sport-' + openSport;
    } else {
        const first = document.querySelector('#sportTabs .nav-link');
        if (first) targetId = first.id;
    }

    if (targetId) {
        const el = document.getElementById(targetId);
        if (el) new bootstrap.Tab(el).show();
    }

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
