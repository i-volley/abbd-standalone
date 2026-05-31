@php
$metodBadge     = ['analitico' => 'bg-primary', 'sintetico' => 'bg-warning text-dark', 'globale' => 'bg-success'];
$faseGiocoBadge = ['cambio_palla' => 'bg-info text-dark', 'break_point' => 'bg-danger', 'ricostruzione' => 'bg-warning text-dark'];
$faseGiocoLab   = ['cambio_palla' => 'CP', 'break_point' => 'BP', 'ricostruzione' => 'RIC'];
$ruoloLab       = ['alzatore' => 'ALZ', 'ricevitore_attaccante' => 'R-A', 'centrale' => 'CEN', 'opposto' => 'OPP', 'libero' => 'LIB'];
@endphp

@forelse($esercizi as $e)
<div class="card mb-2 shadow-sm">
    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                <strong>{{ $e->nome }}</strong>
                <span class="badge {{ $metodBadge[$e->metodologia] ?? 'bg-secondary' }} rounded-pill" style="font-size:.65rem">{{ strtoupper($e->metodologia) }}</span>
                <span class="badge bg-secondary rounded-pill" style="font-size:.65rem">{{ $e->fase }}</span>
                @if($e->fase_gioco)
                    <span class="badge {{ $faseGiocoBadge[$e->fase_gioco] ?? 'bg-secondary' }} rounded-pill" style="font-size:.65rem">{{ $faseGiocoLab[$e->fase_gioco] ?? $e->fase_gioco }}</span>
                @endif
                @foreach($e->ruoli as $r)
                    <span class="badge bg-dark rounded-pill" style="font-size:.65rem">{{ $ruoloLab[$r->ruolo] ?? $r->ruolo }}</span>
                @endforeach
            </div>
            <div class="small text-muted d-flex gap-2 flex-wrap mb-1">
                {{ $e->durata_min }} min
                @if($e->gestoTecnico) · {{ $e->gestoTecnico->nome }}@endif
                @if($e->n_giocatori) · {{ $e->n_giocatori }}@endif
            </div>
            <div class="d-flex flex-wrap gap-1">
                @foreach($e->capacita as $c)
                    <x-badge-capacita :capacita="$c" />
                @endforeach
            </div>
        </div>
        @if($sedutaId)
        <div class="d-flex flex-column gap-1 flex-shrink-0 ms-2">
            @if(!in_array($e->id, $aggiuntiIds))
            <select class="form-select form-select-sm track-select" style="width:130px;font-size:.72rem">
                <option value="completo">👥 Tutti</option>
                <option value="alzatore">🖐️ Alzatore</option>
                <option value="ricevitore_attaccante">🤸 Ric.-Att.</option>
                <option value="centrale">🏛️ Centrale</option>
                <option value="opposto">⚔️ Opposto</option>
                <option value="libero">🛡️ Libero</option>
            </select>
            @endif
            <button class="btn btn-sm btn-success btn-aggiungi"
                    data-esercizio-id="{{ $e->id }}"
                    data-seduta-id="{{ $sedutaId }}"
                    {{ in_array($e->id, $aggiuntiIds) ? 'disabled' : '' }}>
                {{ in_array($e->id, $aggiuntiIds) ? 'Aggiunto ✓' : '+ Aggiungi' }}
            </button>
        </div>
        @endif
    </div>
</div>
@empty
<p class="text-muted text-center py-3">Nessun esercizio trovato.</p>
@endforelse
