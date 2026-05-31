{{--
    Partial: assi metodologici FIPAV.
    Richiede: $ruoliDisponibili (array stringhe)
    Opzionale: $esercizio (per edit — popola old values)
--}}
@php
    $e = $esercizio ?? null;
    $distretti     = $distretti ?? \App\Models\Esercizio::distretti();
    $labDistretto  = ['caviglia' => '🦶 Caviglia', 'ginocchio' => '🦵 Ginocchio', 'lombare' => '🔙 Lombare', 'spalla' => '💪 Spalla'];
    $labObiettivo  = ['permanente' => 'Permanente', 'principale' => 'Principale', 'secondario' => 'Secondario'];
    $labFaseSeduta = ['preparatoria' => 'Preparatoria', 'centrale' => 'Centrale', 'finale' => 'Finale'];
    $labFaseGioco  = ['cambio_palla' => 'Cambio palla', 'break_point' => 'Break point', 'ricostruzione' => 'Ricostruzione'];
    $labComponente = ['tecnica' => 'Tecnica', 'tattica' => 'Tattica'];
    $labRendimento = ['positivita' => 'Positività', 'gestione_errore' => 'Gestione errore', 'efficienza' => 'Efficienza'];
    $labLivello    = ['base' => 'Base', 'medio' => 'Medio', 'alto' => 'Alto'];
    $labRuoli      = [
        'alzatore'             => 'Alzatore',
        'ricevitore_attaccante'=> 'Ric.-Attaccante',
        'centrale'             => 'Centrale',
        'opposto'              => 'Opposto',
        'libero'               => 'Libero',
    ];
    $ruoliSelezionati = old('ruoli', $e ? $e->getRuoliListAttribute() : []);
@endphp

<div class="card border-secondary border-opacity-25 mt-4 mb-2">
    <div class="card-header bg-transparent py-2">
        <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">
            Assi metodologici FIPAV
            <a href="#" class="ms-1 text-muted" data-bs-toggle="tooltip"
               title="Dal Manuale Allenatore Primo Grado FIPAV. Tutti opzionali — usati per ricerca e filtro.">ⓘ</a>
        </small>
    </div>
    <div class="card-body">
        <div class="row g-3">

            {{-- Obiettivo seduta --}}
            <div class="col-md-4">
                <label class="form-label">Obiettivo nella seduta</label>
                <select name="obiettivo" class="form-select form-select-sm">
                    <option value="">– non specificato –</option>
                    @foreach($labObiettivo as $val => $lab)
                        <option value="{{ $val }}" {{ old('obiettivo', $e?->obiettivo) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fase seduta --}}
            <div class="col-md-4">
                <label class="form-label">Fase seduta</label>
                <select name="fase_seduta" class="form-select form-select-sm">
                    <option value="">– non specificata –</option>
                    @foreach($labFaseSeduta as $val => $lab)
                        <option value="{{ $val }}" {{ old('fase_seduta', $e?->fase_seduta) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Componente --}}
            <div class="col-md-4">
                <label class="form-label">Componente</label>
                <select name="componente" class="form-select form-select-sm">
                    <option value="">– non specificata –</option>
                    @foreach($labComponente as $val => $lab)
                        <option value="{{ $val }}" {{ old('componente', $e?->componente) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fase di gioco --}}
            <div class="col-md-4">
                <label class="form-label">Fase di gioco</label>
                <select name="fase_gioco" class="form-select form-select-sm">
                    <option value="">– non specificata –</option>
                    @foreach($labFaseGioco as $val => $lab)
                        <option value="{{ $val }}" {{ old('fase_gioco', $e?->fase_gioco) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Rendimento --}}
            <div class="col-md-4">
                <label class="form-label">Obiettivo rendimento</label>
                <select name="rendimento" class="form-select form-select-sm">
                    <option value="">– non specificato –</option>
                    @foreach($labRendimento as $val => $lab)
                        <option value="{{ $val }}" {{ old('rendimento', $e?->rendimento) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Livello --}}
            <div class="col-md-2">
                <label class="form-label">Livello</label>
                <select name="livello" class="form-select form-select-sm">
                    <option value="">–</option>
                    @foreach($labLivello as $val => $lab)
                        <option value="{{ $val }}" {{ old('livello', $e?->livello) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- N. giocatori --}}
            <div class="col-md-2">
                <label class="form-label">N. giocatori</label>
                <input type="text" name="n_giocatori" class="form-control form-control-sm"
                       value="{{ old('n_giocatori', $e?->n_giocatori) }}"
                       placeholder="es. 6vs6">
            </div>

            {{-- Prevenzione distretto --}}
            <div class="col-md-4">
                <label class="form-label">Prevenzione distretto</label>
                <select name="prevenzione_distretto" class="form-select form-select-sm">
                    <option value="">– nessuno –</option>
                    @foreach($distretti as $d)
                        <option value="{{ $d }}" {{ old('prevenzione_distretto', $e?->prevenzione_distretto) === $d ? 'selected' : '' }}>
                            {{ $labDistretto[$d] ?? ucfirst($d) }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Esercizio specifico di prevenzione (Metodologia 3)</div>
            </div>

            {{-- Ruoli --}}
            <div class="col-12">
                <label class="form-label">Ruoli (lascia vuoto = tutti)</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($ruoliDisponibili as $r)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox"
                                   name="ruoli[]" value="{{ $r }}"
                                   id="ruolo_{{ $r }}"
                                   {{ in_array($r, $ruoliSelezionati) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ruolo_{{ $r }}">
                                {{ $labRuoli[$r] ?? $r }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
