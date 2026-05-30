@forelse($esercizi as $e)
<div class="card mb-2 shadow-sm">
    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-start">
        <div>
            <strong>{{ $e->nome }}</strong>
            <div class="small text-muted">
                <span class="badge bg-secondary me-1">{{ $e->fase }}</span>
                {{ $e->metodologia }} · {{ $e->durata_min }} min
                @if($e->gestoTecnico)
                    · {{ $e->gestoTecnico->nome }}
                @endif
            </div>
            <div class="mt-1">
                @foreach($e->capacita as $c)
                    <x-badge-capacita :capacita="$c" />
                @endforeach
            </div>
        </div>
        @if($sedutaId)
            <button class="btn btn-sm btn-success ms-2 btn-aggiungi"
                    data-esercizio-id="{{ $e->id }}"
                    data-seduta-id="{{ $sedutaId }}"
                    {{ in_array($e->id, $aggiuntiIds) ? 'disabled' : '' }}>
                {{ in_array($e->id, $aggiuntiIds) ? 'Aggiunto ✓' : '+ Aggiungi' }}
            </button>
        @endif
    </div>
</div>
@empty
<p class="text-muted text-center py-3">Nessun esercizio trovato.</p>
@endforelse
