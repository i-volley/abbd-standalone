{{--
    Partial: assi metodologici FIPAV.
    Richiede: $ruoliDisponibili (array stringhe), $parametri (Collection groupBy tipo)
    Opzionale: $esercizio (per edit — popola old values)
--}}
@php
    $e = $esercizio ?? null;
    $parametri     = $parametri ?? collect();
    $distretti     = $distretti ?? \App\Models\Esercizio::distretti();
    $labDistretto  = ['caviglia' => '🦶 Caviglia', 'ginocchio' => '🦵 Ginocchio', 'lombare' => '🔙 Lombare', 'spalla' => '💪 Spalla'];
    $labRuoli      = [
        'alzatore'             => 'Alzatore',
        'ricevitore_attaccante'=> 'Schiacciatore',
        'centrale'             => 'Centrale',
        'opposto'              => 'Opposto',
        'libero'               => 'Libero',
    ];
    $ruoliSelezionati = old('ruoli', $e ? $e->getRuoliListAttribute() : []);

    // Helper: voci attive di un tipo
    $voci = fn($tipo) => $parametri[$tipo] ?? collect();
@endphp

<div class="card border-secondary border-opacity-25 mt-4 mb-2">
    <div class="card-header bg-transparent py-2">
        <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">
            {{ __('Assi metodologici FIPAV') }}
            <a href="#" class="ms-1 text-muted" data-bs-toggle="tooltip"
               title="{{ __('Dal Manuale Allenatore Primo Grado FIPAV. Tutti opzionali — usati per ricerca e filtro. Gestibili da Impostazioni → Parametri esercizio.') }}">ⓘ</a>
        </small>
    </div>
    <div class="card-body">
        <div class="row g-3">

            {{-- Obiettivo seduta --}}
            <div class="col-md-4">
                <label class="form-label">{{ __('Obiettivo nella seduta') }}</label>
                <select name="obiettivo" class="form-select form-select-sm">
                    <option value="">{{ __('– non specificato –') }}</option>
                    @foreach($voci('obiettivo') as $p)
                        <option value="{{ $p->valore }}" {{ old('obiettivo', $e?->obiettivo) === $p->valore ? 'selected' : '' }}>{{ $p->etichetta }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fase seduta --}}
            <div class="col-md-4">
                <label class="form-label">{{ __('Fase seduta') }}</label>
                <select name="fase_seduta" class="form-select form-select-sm">
                    <option value="">{{ __('– non specificata –') }}</option>
                    @foreach($voci('fase_seduta') as $p)
                        <option value="{{ $p->valore }}" {{ old('fase_seduta', $e?->fase_seduta) === $p->valore ? 'selected' : '' }}>{{ $p->etichetta }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Componente --}}
            <div class="col-md-4">
                <label class="form-label">{{ __('Componente') }}</label>
                <select name="componente" class="form-select form-select-sm">
                    <option value="">{{ __('– non specificata –') }}</option>
                    @foreach($voci('componente') as $p)
                        <option value="{{ $p->valore }}" {{ old('componente', $e?->componente) === $p->valore ? 'selected' : '' }}>{{ $p->etichetta }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fase di gioco --}}
            <div class="col-md-4">
                <label class="form-label">{{ __('Fase di gioco') }}</label>
                <select name="fase_gioco" class="form-select form-select-sm">
                    <option value="">{{ __('– non specificata –') }}</option>
                    @foreach($voci('fase_gioco') as $p)
                        <option value="{{ $p->valore }}" {{ old('fase_gioco', $e?->fase_gioco) === $p->valore ? 'selected' : '' }}>{{ $p->etichetta }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Rendimento --}}
            <div class="col-md-4">
                <label class="form-label">{{ __('Obiettivo rendimento') }}</label>
                <select name="rendimento" class="form-select form-select-sm">
                    <option value="">{{ __('– non specificato –') }}</option>
                    @foreach($voci('rendimento') as $p)
                        <option value="{{ $p->valore }}" {{ old('rendimento', $e?->rendimento) === $p->valore ? 'selected' : '' }}>{{ $p->etichetta }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Livello --}}
            <div class="col-md-2">
                <label class="form-label">{{ __('Livello') }}</label>
                <select name="livello" class="form-select form-select-sm">
                    <option value="">–</option>
                    @foreach($voci('livello') as $p)
                        <option value="{{ $p->valore }}" {{ old('livello', $e?->livello) === $p->valore ? 'selected' : '' }}>{{ $p->etichetta }}</option>
                    @endforeach
                </select>
            </div>

            {{-- N. giocatori --}}
            <div class="col-md-2">
                <label class="form-label">{{ __('N. giocatori') }}</label>
                <input type="text" name="n_giocatori" class="form-control form-control-sm"
                       value="{{ old('n_giocatori', $e?->n_giocatori) }}"
                       placeholder="{{ __('es. 6vs6') }}">
            </div>

            {{-- Prevenzione distretto --}}
            <div class="col-md-4">
                <label class="form-label">{{ __('Prevenzione distretto') }}</label>
                <select name="prevenzione_distretto" class="form-select form-select-sm">
                    <option value="">{{ __('– nessuno –') }}</option>
                    @foreach($distretti as $d)
                        <option value="{{ $d }}" {{ old('prevenzione_distretto', $e?->prevenzione_distretto) === $d ? 'selected' : '' }}>
                            {{ $labDistretto[$d] ?? ucfirst($d) }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">{{ __('Esercizio specifico di prevenzione (Metodologia 3)') }}</div>
            </div>

            {{-- Ruoli --}}
            <div class="col-12">
                <label class="form-label">{{ __('Ruoli (lascia vuoto = tutti)') }}</label>
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
