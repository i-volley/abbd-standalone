@php
$metodBadge     = ['analitico' => 'bg-primary', 'sintetico' => 'bg-warning text-dark', 'globale' => 'bg-success'];
$faseBadge      = ['riscaldamento' => 'bg-warning text-dark', 'potenziamento' => 'bg-danger', 'stretching' => 'bg-info text-dark'];
$faseGiocoBadge = ['cambio_palla' => 'bg-info text-dark', 'break_point' => 'bg-danger', 'ricostruzione' => 'bg-warning text-dark'];
$faseGiocoLab   = ['cambio_palla' => 'CP', 'break_point' => 'BP', 'ricostruzione' => 'RIC'];
$ruoloLab       = ['alzatore' => 'ALZ', 'ricevitore_attaccante' => 'SCH', 'centrale' => 'CEN', 'opposto' => 'OPP', 'libero' => 'LIB'];
@endphp

{{-- ── I MIEI ESERCIZI ─────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center gap-2 mb-3 mt-1">
    <h5 class="mb-0 fw-bold">I miei esercizi</h5>
    <span class="badge bg-dark rounded-pill">{{ $miei->count() }}</span>
</div>

@forelse($miei as $e)
<div class="card mb-2 shadow-sm border-0">
    <div class="card-body py-2 px-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                    <strong>{{ $e->nome }}</strong>
                    <span class="badge {{ $metodBadge[$e->metodologia] ?? 'bg-secondary' }} rounded-pill" style="font-size:.7rem">{{ strtoupper($e->metodologia) }}</span>
                    <span class="badge {{ $faseBadge[$e->fase] ?? 'bg-secondary' }} rounded-pill" style="font-size:.7rem">{{ $e->fase }}</span>
                    @if($e->categoria_eta)
                        <x-badge-categoria-eta :categoria="$e->categoria_eta" />
                    @endif
                    @if($e->fase_gioco)
                        <span class="badge {{ $faseGiocoBadge[$e->fase_gioco] ?? 'bg-secondary' }} rounded-pill" style="font-size:.65rem">{{ $faseGiocoLab[$e->fase_gioco] ?? $e->fase_gioco }}</span>
                    @endif
                    @foreach($e->ruoli as $r)
                        <span class="badge bg-dark rounded-pill" style="font-size:.65rem">{{ $ruoloLab[$r->ruolo] ?? $r->ruolo }}</span>
                    @endforeach
                </div>
                <div class="small text-muted d-flex gap-2 flex-wrap mb-1">
                    @if($e->gestoTecnico)<span>{{ $e->gestoTecnico->nome }}</span>@endif
                    <span>{{ $e->durata_min }} min</span>
                    @if($e->n_salti > 0)<span>{{ $e->n_salti }} salti</span>@endif
                    @if($e->n_gesti > 0)<span>{{ $e->n_gesti }} gesti</span>@endif
                    @if($e->n_giocatori)<span>{{ $e->n_giocatori }}</span>@endif
                </div>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($e->capacita as $c)
                        <x-badge-capacita :capacita="$c" />
                    @endforeach
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <a href="{{ route('allenatore.esercizi.edit', $e) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
                <form action="{{ route('allenatore.esercizi.destroy', $e) }}" method="POST" class="d-inline"
                      data-confirm="Eliminare {{ addslashes($e->nome) }}?">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Elimina</button>
                </form>
            </div>
        </div>
    </div>
</div>
@empty
<p class="text-muted py-2">Nessun esercizio creato da te. <a href="{{ route('allenatore.esercizi.create') }}">Crea il primo</a>.</p>
@endforelse

{{-- ── CATALOGO ESERCITAZIONI ──────────────────────────────────────────────── --}}
<hr class="my-4">
<div class="d-flex align-items-center gap-2 mb-3">
    <h5 class="mb-0 fw-bold">Catalogo esercitazioni</h5>
    <span class="badge bg-dark rounded-pill">{{ $catalogo->count() }}</span>
    <small class="text-muted">Database generale</small>
</div>

@forelse($catalogo as $e)
<div class="card mb-2 shadow-sm border-0 bg-light">
    <div class="card-body py-2 px-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                    <strong>{{ $e->nome }}</strong>
                    <span class="badge {{ $metodBadge[$e->metodologia] ?? 'bg-secondary' }} rounded-pill" style="font-size:.7rem">{{ strtoupper($e->metodologia) }}</span>
                    <span class="badge {{ $faseBadge[$e->fase] ?? 'bg-secondary' }} rounded-pill" style="font-size:.7rem">{{ $e->fase }}</span>
                    @if($e->categoria_eta)
                        <x-badge-categoria-eta :categoria="$e->categoria_eta" />
                    @endif
                </div>
                <div class="small text-muted d-flex gap-2 flex-wrap mb-1">
                    @if($e->gestoTecnico)<span>{{ $e->gestoTecnico->nome }}</span>@endif
                    <span>{{ $e->durata_min }} min</span>
                    @if($e->n_salti > 0)<span>{{ $e->n_salti }} salti</span>@endif
                    @if($e->n_gesti > 0)<span>{{ $e->n_gesti }} gesti</span>@endif
                </div>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($e->capacita as $c)
                        <x-badge-capacita :capacita="$c" />
                    @endforeach
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <a href="{{ route('allenatore.esercizi.edit', $e) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
            </div>
        </div>
    </div>
</div>
@empty
<p class="text-muted py-2">Nessun esercizio nel catalogo generale.</p>
@endforelse
