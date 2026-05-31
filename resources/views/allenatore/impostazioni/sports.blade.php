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

    {{-- ── PANNELLO PER OGNI SPORT ─────────────────────────────────────────── --}}
    @foreach($sports as $sport)
    <div class="tab-pane fade" id="pane-sport-{{ $sport->id }}" role="tabpanel">

        <div class="row g-4">

            {{-- Gesti tecnici esistenti --}}
            <div class="col-lg-7">
                <h5 class="mb-3 fw-bold">Gesti tecnici — {{ $sport->nome }}</h5>

                @forelse($sport->gestiTecnici as $g)
                <div class="d-flex align-items-center gap-2 py-2 border-bottom">
                    <span class="fw-semibold flex-grow-1">{{ $g->nome }}</span>
                    <span class="badge {{ $g->categoria === 'fondamentale_base' ? 'bg-primary' : 'bg-success' }} rounded-pill">
                        {{ $g->categoria === 'fondamentale_base' ? 'Base' : 'Gioco' }}
                    </span>
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
                <p class="text-muted fst-italic">Nessun gesto tecnico. Aggiungine uno →</p>
                @endforelse
            </div>

            {{-- Form aggiungi gesto tecnico --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent fw-semibold">
                        + Aggiungi gesto tecnico
                    </div>
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
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="categoria"
                                               value="fondamentale_base" id="cat-base-{{ $sport->id }}" checked>
                                        <label class="form-check-label" for="cat-base-{{ $sport->id }}">
                                            <span class="badge bg-primary">Base</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="categoria"
                                               value="fondamentale_gioco" id="cat-gioco-{{ $sport->id }}">
                                        <label class="form-check-label" for="cat-gioco-{{ $sport->id }}">
                                            <span class="badge bg-success">Gioco</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label form-label-sm">Ordinamento</label>
                                <input type="number" name="ordinamento" class="form-control form-control-sm"
                                       value="{{ $sport->gestiTecnici->count() + 1 }}" min="1">
                                <div class="form-text">Posizione nella lista esercizi</div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                Aggiungi gesto tecnico
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Danger zone sport --}}
                <div class="mt-3">
                    <details>
                        <summary class="text-danger small" style="cursor:pointer">Zona pericolosa</summary>
                        <div class="mt-2">
                            <form action="{{ route('allenatore.sports.destroy', $sport) }}" method="POST"
                                  onsubmit="return confirm('ATTENZIONE: eliminare {{ addslashes($sport->nome) }} eliminerà anche tutti i gesti tecnici associati. Continuare?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger w-100">
                                    Elimina sport {{ $sport->nome }}
                                </button>
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
                    Una volta creato lo sport, selezionalo dal tab per aggiungere i gesti tecnici specifici.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Apri il tab corretto in base al query param ?open_sport=ID
(function () {
    const openSport = '{{ request('open_sport') }}';
    let targetId = '';

    if (openSport) {
        targetId = 'tab-sport-' + openSport;
    } else if (document.getElementById('sportTabs')) {
        // Di default apri il primo sport
        const first = document.querySelector('#sportTabs .nav-link');
        if (first) targetId = first.id;
    }

    if (targetId) {
        const el = document.getElementById(targetId);
        if (el) new bootstrap.Tab(el).show();
    }
})();
</script>
@endpush
