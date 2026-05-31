@extends('layouts.allenatore')
@section('title', 'Wizard Diagnostico')

@push('styles')
<style>
.wizard-step { display: none; }
.wizard-step.active { display: block; }

.sintomo-card {
    cursor: pointer;
    border: 2px solid #dee2e6;
    border-radius: .75rem;
    padding: 1.25rem;
    transition: border-color .15s, box-shadow .15s, background .15s;
    text-align: left;
    background: #fff;
    width: 100%;
}
.sintomo-card:hover { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.1); }
.sintomo-card.selected { border-color: #0d6efd; background: #f0f5ff; box-shadow: 0 0 0 3px rgba(13,110,253,.15); }
.sintomo-card .badge-metod { font-size: .7rem; margin-top: .5rem; }

.step-indicator { display: flex; gap: .5rem; margin-bottom: 2rem; }
.step-dot {
    width: 2rem; height: 2rem; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 600;
    background: #e9ecef; color: #6c757d;
    transition: background .2s, color .2s;
}
.step-dot.active   { background: #0d6efd; color: #fff; }
.step-dot.done     { background: #198754; color: #fff; }
.step-dot + .step-dot { margin-left: .25rem; }
.step-line { flex: 1; height: 2px; background: #dee2e6; margin: auto .25rem; border-radius: 1px; }
.step-line.done { background: #198754; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="mb-4">
    <h2 class="mb-1">🔍 Wizard Diagnostico</h2>
    <p class="text-muted mb-0">Dimmi cosa vedi in partita — il sistema prescrive gli esercizi giusti secondo il metodo FIPAV.</p>
</div>

{{-- Step indicator --}}
<div class="step-indicator" id="step-indicator">
    <div class="step-dot active" id="dot-1">1</div>
    <div class="step-line" id="line-1"></div>
    <div class="step-dot" id="dot-2">2</div>
    <div class="step-line" id="line-2"></div>
    <div class="step-dot" id="dot-3">3</div>
    <div class="step-line" id="line-3"></div>
    <div class="step-dot" id="dot-4">4</div>
</div>

<form action="{{ route('allenatore.wizard.risultati') }}" method="GET" id="wizard-form">

    {{-- ── STEP 1: SINTOMO ──────────────────────────────────────────────── --}}
    <div class="wizard-step active" id="step-1">
        <h5 class="fw-bold mb-1">Che problema vedi nella tua squadra?</h5>
        <p class="text-muted small mb-4">Scegli il sintomo prevalente osservato in allenamento o partita.</p>

        <input type="hidden" name="sintomo" id="input-sintomo" required>

        <div class="d-flex flex-column gap-3">

            <button type="button" class="sintomo-card" data-val="errori_tecnici">
                <div class="d-flex align-items-start gap-3">
                    <span style="font-size:1.8rem">❌</span>
                    <div>
                        <strong>Molti errori tecnici nell'esecuzione</strong>
                        <p class="small text-muted mb-0 mt-1">Il gesto tecnico non è corretto — errori nella postura, nel piano di rimbalzo, nella coordinazione del colpo.</p>
                        <span class="badge bg-primary badge-metod">→ ANALITICO</span>
                    </div>
                </div>
            </button>

            <button type="button" class="sintomo-card" data-val="ritmo_velocita">
                <div class="d-flex align-items-start gap-3">
                    <span style="font-size:1.8rem">⚡</span>
                    <div>
                        <strong>Il gesto crolla quando aumenta la velocità di gioco</strong>
                        <p class="small text-muted mb-0 mt-1">Da fermi eseguono bene, ma sotto ritmo di gara compaiono gli errori. Difficoltà di adattamento alla velocità della palla o della sequenza.</p>
                        <span class="badge bg-warning text-dark badge-metod">→ SINTETICO</span>
                    </div>
                </div>
            </button>

            <button type="button" class="sintomo-card" data-val="complessita_situazionale">
                <div class="d-flex align-items-start gap-3">
                    <span style="font-size:1.8rem">🌀</span>
                    <div>
                        <strong>Errori quando la situazione si complica</strong>
                        <p class="small text-muted mb-0 mt-1">In esercizio vanno bene, in partita crolla tutto. Faticano con il numero di variabili simultanee: avversari, compagni, palla.</p>
                        <span class="badge bg-success badge-metod">→ GLOBALE</span>
                    </div>
                </div>
            </button>

            <button type="button" class="sintomo-card" data-val="scelte_tattiche">
                <div class="d-flex align-items-start gap-3">
                    <span style="font-size:1.8rem">🧠</span>
                    <div>
                        <strong>Difficoltà nelle scelte tattiche</strong>
                        <p class="small text-muted mb-0 mt-1">Tecnicamente sono capaci, ma le decisioni sono lente o sbagliate: dove attaccare, quando murarsi, come distribuire il gioco.</p>
                        <span class="badge bg-success badge-metod">→ GLOBALE TATTICO</span>
                    </div>
                </div>
            </button>

        </div>

        <div class="mt-4 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" id="btn-next-1" disabled onclick="goToStep(2)">
                Avanti →
            </button>
        </div>
    </div>

    {{-- ── STEP 2: FONDAMENTALE ─────────────────────────────────────────── --}}
    <div class="wizard-step" id="step-2">
        <h5 class="fw-bold mb-1">Su quale fondamentale?</h5>
        <p class="text-muted small mb-4">Indica il gesto tecnico specifico. Puoi lasciare "Tutti" per vedere tutti gli esercizi prescritti.</p>

        <select name="gesto_tecnico_id" class="form-select form-select-lg mb-3">
            <option value="tutti">🏐 Tutti i fondamentali</option>
            @foreach($gesti as $g)
                <option value="{{ $g->id }}">{{ $g->nome }}</option>
            @endforeach
        </select>

        <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" onclick="goToStep(1)">← Indietro</button>
            <button type="button" class="btn btn-primary" onclick="goToStep(3)">Avanti →</button>
        </div>
    </div>

    {{-- ── STEP 3: FASE DI GIOCO ───────────────────────────────────────── --}}
    <div class="wizard-step" id="step-3">
        <h5 class="fw-bold mb-1">In quale fase di gioco si manifesta?</h5>
        <p class="text-muted small mb-4">Opzionale — aiuta a restringere la selezione.</p>

        <div class="d-flex flex-column gap-2">
            @foreach([
                'tutti'        => ['🔄', 'Tutte le fasi', ''],
                'cambio_palla' => ['🎯', 'Cambio palla', 'Battuta → ricezione → alzata → attacco'],
                'break_point'  => ['🛡️', 'Break point', 'Muro → difesa → contrattacco'],
                'ricostruzione'=> ['🔁', 'Ricostruzione', 'Copertura → ricostruzione del contrattacco'],
            ] as $val => [$icon, $label, $sub])
            <label class="d-flex align-items-center gap-3 p-3 border rounded" style="cursor:pointer">
                <input type="radio" name="fase_gioco" value="{{ $val }}" class="form-check-input mt-0" {{ $val === 'tutti' ? 'checked' : '' }}>
                <span style="font-size:1.4rem">{{ $icon }}</span>
                <div>
                    <strong>{{ $label }}</strong>
                    @if($sub)<p class="small text-muted mb-0">{{ $sub }}</p>@endif
                </div>
            </label>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" onclick="goToStep(2)">← Indietro</button>
            <button type="button" class="btn btn-primary" onclick="goToStep(4)">Avanti →</button>
        </div>
    </div>

    {{-- ── STEP 4: RUOLO ───────────────────────────────────────────────── --}}
    <div class="wizard-step" id="step-4">
        <h5 class="fw-bold mb-1">Quale ruolo vuoi allenare?</h5>
        <p class="text-muted small mb-4">Filtra per ruolo specifico o cerca esercizi per tutta la squadra.</p>

        <div class="d-flex flex-wrap gap-2 mb-4">
            @foreach([
                'tutti'                 => ['👥', 'Tutta la squadra'],
                'alzatore'              => ['🖐️', 'Alzatore'],
                'ricevitore_attaccante' => ['🤸', 'Ric.-Attaccante'],
                'centrale'              => ['🏛️', 'Centrale'],
                'opposto'               => ['⚔️', 'Opposto'],
                'libero'                => ['🛡️', 'Libero'],
            ] as $val => [$icon, $label])
            <label class="btn btn-outline-secondary d-flex align-items-center gap-1 px-3" style="cursor:pointer">
                <input type="radio" name="ruolo" value="{{ $val }}" class="d-none" {{ $val === 'tutti' ? 'checked' : '' }}
                       onchange="document.querySelectorAll('[name=ruolo]').forEach(r => r.closest('label').classList.toggle('btn-secondary', r.checked)); this.closest('label').classList.add('btn-secondary'); this.closest('label').classList.remove('btn-outline-secondary')">
                {{ $icon }} {{ $label }}
            </label>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" onclick="goToStep(3)">← Indietro</button>
            <button type="submit" class="btn btn-success btn-lg px-4">
                🔍 Mostra esercizi prescritti
            </button>
        </div>
    </div>

</form>

</div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;
const TOTAL = 4;

function goToStep(n) {
    document.getElementById('step-' + currentStep).classList.remove('active');
    document.getElementById('dot-' + currentStep).classList.remove('active');
    if (n > currentStep) document.getElementById('dot-' + currentStep).classList.add('done');

    if (currentStep < TOTAL && n > currentStep) {
        document.getElementById('line-' + currentStep).classList.add('done');
    }

    currentStep = n;
    document.getElementById('step-' + n).classList.add('active');
    document.getElementById('dot-' + n).classList.add('active');
    document.getElementById('dot-' + n).classList.remove('done');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

// Sintomo card selection
document.querySelectorAll('.sintomo-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.sintomo-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        document.getElementById('input-sintomo').value = this.dataset.val;
        document.getElementById('btn-next-1').disabled = false;
    });
});

// Evidenzia radio ruolo selezionato al mount
document.querySelectorAll('[name=ruolo]').forEach(r => {
    if (r.checked) {
        r.closest('label').classList.add('btn-secondary');
        r.closest('label').classList.remove('btn-outline-secondary');
    }
});
</script>
@endpush
