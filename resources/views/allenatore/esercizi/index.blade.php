@extends('layouts.allenatore')
@section('title', __('Catalogo Esercizi'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">{{ __('Catalogo Esercizi') }}</h2>
    <a href="{{ route('allenatore.esercizi.create') }}" class="btn btn-primary">{{ __('+ Nuovo esercizio') }}</a>
</div>

{{-- ── FILTRI ───────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">

        {{-- Metodologia --}}
        <div class="mb-1">
            <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Metodologia') }}</small>
        </div>
        <div class="d-flex gap-2 flex-wrap mb-3">
            <button type="button" class="btn btn-lg btn-outline-secondary filtro-metod active-all" data-val="">{{ __('TUTTE') }}</button>
            <button type="button" class="btn btn-lg btn-outline-primary filtro-metod" data-val="analitico"
                    style="--btn-color:#0d6efd">{{ __('ANALITICO') }}</button>
            <button type="button" class="btn btn-lg filtro-metod" data-val="sintetico"
                    style="background:none;border:2px solid #e6a817;color:#cc8c00;font-weight:600;font-size:1.125rem;padding:.5rem 1rem;border-radius:.375rem">{{ __('SINTETICO') }}</button>
            <button type="button" class="btn btn-lg btn-outline-success filtro-metod" data-val="globale">{{ __('GLOBALE') }}</button>
        </div>

        {{-- Gesto tecnico (nascosto finché non si sceglie metodologia) --}}
        <div id="filtro-gesti-wrap" class="d-none mb-3">
            <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Gesto tecnico') }}</small>
            <div class="d-flex gap-1 flex-wrap mt-1">
                @foreach($gesti as $g)
                <button type="button" class="btn btn-sm btn-outline-secondary filtro-gesto" data-val="{{ $g->id }}">{{ $g->nome }}</button>
                @endforeach
            </div>
        </div>

        {{-- Categoria età --}}
        <div class="mb-1">
            <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Categoria età') }}</small>
        </div>
        <div class="d-flex gap-1 flex-wrap mb-3">
            <button type="button" class="btn btn-sm btn-secondary filtro-cat active-all" data-val="">{{ __('Tutte') }}</button>
            @foreach($categorie as $cat)
            @php $col = \App\Models\Esercizio::catEtaColore($cat); @endphp
            <button type="button" class="btn btn-sm filtro-cat"
                    data-val="{{ $cat }}"
                    style="border:2px solid {{ $col }};color:{{ $col }};background:transparent;font-weight:600">{{ $cat }}</button>
            @endforeach
        </div>

        {{-- Fase di gioco --}}
        <div class="mb-1">
            <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Fase di gioco') }}</small>
        </div>
        <div class="d-flex gap-1 flex-wrap mb-3">
            <button type="button" class="btn btn-sm btn-secondary filtro-fase-gioco active-all" data-val="">{{ __('Tutte') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-fase-gioco" data-val="cambio_palla">{{ __('Cambio palla') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-fase-gioco" data-val="break_point">{{ __('Break point') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-fase-gioco" data-val="ricostruzione">{{ __('Ricostruzione') }}</button>
        </div>

        {{-- Ruolo --}}
        <div class="mb-1">
            <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Ruolo') }}</small>
        </div>
        <div class="d-flex gap-1 flex-wrap mb-3">
            <button type="button" class="btn btn-sm btn-secondary filtro-ruolo active-all" data-val="">{{ __('Tutti') }}</button>
            @php
            $labRuoli = ['alzatore'=>'Alzatore','ricevitore_attaccante'=>'Schiacciatore','centrale'=>'Centrale','opposto'=>'Opposto','libero'=>'Libero'];
            @endphp
            @foreach($ruoliDisponibili as $r)
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-ruolo" data-val="{{ $r }}">{{ $labRuoli[$r] }}</button>
            @endforeach
        </div>

        {{-- Prevenzione --}}
        <div class="mb-1">
            <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Prevenzione distretto') }}</small>
        </div>
        <div class="d-flex gap-1 flex-wrap mb-3">
            <button type="button" class="btn btn-sm btn-secondary filtro-prev active-all" data-val="">{{ __('Tutti') }}</button>
            @php $labDist = ['caviglia'=>'🦶 Caviglia','ginocchio'=>'🦵 Ginocchio','lombare'=>'🔙 Lombare','spalla'=>'💪 Spalla']; @endphp
            @foreach($distretti as $d)
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-prev" data-val="{{ $d }}">{{ $labDist[$d] }}</button>
            @endforeach
        </div>

        {{-- Paradigma --}}
        <div class="mb-1">
            <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Paradigma') }}</small>
        </div>
        <div class="d-flex gap-2 flex-wrap mb-3">
            <button type="button" class="btn btn-sm btn-secondary filtro-paradigm" data-val="">{{ __('Tutti') }}</button>
            <button type="button" class="btn btn-sm filtro-paradigm" data-val="ecological"
                    style="background:#10b981;color:#fff;border:none;font-weight:600;font-size:.875rem;padding:.375rem .75rem;border-radius:.375rem">🌿 {{ __('Ecologico') }}</button>
            <button type="button" class="btn btn-sm btn-outline-primary filtro-paradigm" data-val="traditional">📋 {{ __('Tradizionale') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-paradigm" data-val="neutral">{{ __('Neutro') }}</button>
        </div>

        {{-- Tipo esercizio --}}
        <div class="mb-1">
            <small class="text-muted fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.07em">{{ __('Tipo esercizio') }}</small>
        </div>
        <div class="d-flex gap-1 flex-wrap mb-3">
            <button type="button" class="btn btn-sm btn-secondary filtro-excat" data-val="">{{ __('Tutti') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-excat" data-val="analytic">{{ __('Analitico') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-excat" data-val="situational">{{ __('Situazionale') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-excat" data-val="game_form">{{ __('Forma di gioco') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary filtro-excat" data-val="free_play">{{ __('Gioco libero') }}</button>
        </div>

        {{-- Testo libero --}}
        <input type="text" id="cerca-nome" class="form-control"
               placeholder="{{ __('Cerca per nome o descrizione...') }}">
    </div>
</div>

{{-- ── RISULTATI (aggiornati via AJAX) ─────────────────────────────────────── --}}
<div id="risultati-catalogo">
    @include('allenatore.esercizi._partial-catalogo')
</div>
@endsection

@push('scripts')
<script>
const CERCA_URL = '{{ route('allenatore.esercizi.cerca') }}';

let filtri = { metodologia: '', gesto_tecnico_id: '', categoria_eta: '', fase_gioco: '', ruolo: '', prevenzione_distretto: '', paradigm_primary: '', exercise_category: '', q: '' };

function aggiorna() {
    const params = new URLSearchParams();
    Object.entries(filtri).forEach(([k, v]) => { if (v) params.append(k, v); });

    fetch(`${CERCA_URL}?${params}`)
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc    = parser.parseFromString(html, 'text/html');
            document.getElementById('risultati-catalogo').replaceChildren(
                ...doc.body.childNodes
            );
        });
}

// ── Metodologia ──────────────────────────────────────────────────────────────
document.querySelectorAll('.filtro-metod').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;

        // Toggle: ri-cliccare deseleziona
        if (filtri.metodologia === val && val !== '') {
            filtri.metodologia       = '';
            filtri.gesto_tecnico_id  = '';
        } else {
            filtri.metodologia       = val;
            filtri.gesto_tecnico_id  = '';
        }

        // Active state metodologia
        document.querySelectorAll('.filtro-metod').forEach(b => {
            b.classList.remove('btn-primary','btn-success','btn-secondary');
            if (b.dataset.val === 'analitico') b.classList.add('btn-outline-primary');
            if (b.dataset.val === 'globale')   b.classList.add('btn-outline-success');
            if (b.dataset.val === '')          b.classList.add('btn-outline-secondary');
        });

        if (filtri.metodologia) {
            // Evidenzia selezionato
            if (val === 'analitico') { btn.classList.remove('btn-outline-primary'); btn.classList.add('btn-primary'); }
            if (val === 'globale')   { btn.classList.remove('btn-outline-success'); btn.classList.add('btn-success'); }
            if (val === 'sintetico') { btn.style.background='#e6a817'; btn.style.color='#000'; }
        } else {
            // Reset sintetico style
            document.querySelectorAll('.filtro-metod[data-val="sintetico"]').forEach(b => {
                b.style.background = 'none'; b.style.color = '#cc8c00';
            });
            document.querySelector('.filtro-metod[data-val=""]').classList.remove('btn-outline-secondary');
            document.querySelector('.filtro-metod[data-val=""]').classList.add('btn-secondary');
        }

        // Reset gesti active state
        document.querySelectorAll('.filtro-gesto').forEach(b => b.classList.remove('btn-secondary','active'));

        // Mostra/nascondi gesti
        document.getElementById('filtro-gesti-wrap').classList.toggle('d-none', !filtri.metodologia);

        aggiorna();
    });
});

// ── Gesto tecnico ─────────────────────────────────────────────────────────
document.querySelectorAll('.filtro-gesto').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;
        filtri.gesto_tecnico_id = filtri.gesto_tecnico_id === val ? '' : val;

        document.querySelectorAll('.filtro-gesto').forEach(b => {
            b.classList.remove('btn-secondary');
            b.classList.add('btn-outline-secondary');
        });
        if (filtri.gesto_tecnico_id) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        }
        aggiorna();
    });
});

// ── Categoria età ─────────────────────────────────────────────────────────
document.querySelectorAll('.filtro-cat').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;
        filtri.categoria_eta = filtri.categoria_eta === val && val !== '' ? '' : val;

        document.querySelectorAll('.filtro-cat').forEach(b => {
            b.style.background = 'transparent';
            const col = b.style.borderColor || '#6c757d';
            b.style.color = b.dataset.val ? col : '';
        });

        if (filtri.categoria_eta) {
            btn.style.background = btn.style.borderColor;
            btn.style.color      = '#fff';
        } else {
            // "Tutte" button active
            const tutteBtn = document.querySelector('.filtro-cat[data-val=""]');
            tutteBtn.style.background = '#6c757d';
            tutteBtn.style.color      = '#fff';
        }
        aggiorna();
    });
});

// ── Fase di gioco ─────────────────────────────────────────────────────────
document.querySelectorAll('.filtro-fase-gioco').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;
        filtri.fase_gioco = filtri.fase_gioco === val && val !== '' ? '' : val;

        document.querySelectorAll('.filtro-fase-gioco').forEach(b => {
            b.classList.remove('btn-secondary');
            b.classList.add('btn-outline-secondary');
        });
        if (filtri.fase_gioco) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        } else {
            document.querySelector('.filtro-fase-gioco[data-val=""]').classList.remove('btn-outline-secondary');
            document.querySelector('.filtro-fase-gioco[data-val=""]').classList.add('btn-secondary');
        }
        aggiorna();
    });
});

// ── Ruolo ─────────────────────────────────────────────────────────────────
document.querySelectorAll('.filtro-ruolo').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;
        filtri.ruolo = filtri.ruolo === val && val !== '' ? '' : val;

        document.querySelectorAll('.filtro-ruolo').forEach(b => {
            b.classList.remove('btn-secondary');
            b.classList.add('btn-outline-secondary');
        });
        if (filtri.ruolo) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        } else {
            document.querySelector('.filtro-ruolo[data-val=""]').classList.remove('btn-outline-secondary');
            document.querySelector('.filtro-ruolo[data-val=""]').classList.add('btn-secondary');
        }
        aggiorna();
    });
});

// ── Prevenzione ───────────────────────────────────────────────────────────
document.querySelectorAll('.filtro-prev').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;
        filtri.prevenzione_distretto = filtri.prevenzione_distretto === val && val !== '' ? '' : val;
        document.querySelectorAll('.filtro-prev').forEach(b => {
            b.classList.remove('btn-secondary'); b.classList.add('btn-outline-secondary');
        });
        if (filtri.prevenzione_distretto) {
            btn.classList.remove('btn-outline-secondary'); btn.classList.add('btn-secondary');
        } else {
            const all = document.querySelector('.filtro-prev[data-val=""]');
            all.classList.remove('btn-outline-secondary'); all.classList.add('btn-secondary');
        }
        aggiorna();
    });
});

// ── Paradigma ─────────────────────────────────────────────────────────────
document.querySelectorAll('.filtro-paradigm').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;
        filtri.paradigm_primary = filtri.paradigm_primary === val && val !== '' ? '' : val;
        document.querySelectorAll('.filtro-paradigm').forEach(b => {
            b.classList.remove('btn-secondary','btn-primary','btn-success');
            if (!b.dataset.val) { b.classList.add('btn-outline-secondary'); }
        });
        const active = document.querySelector('.filtro-paradigm[data-val="' + (filtri.paradigm_primary || '') + '"]');
        if (active) {
            if (filtri.paradigm_primary === 'ecological') { active.style.background='#10b981'; active.style.color='#fff'; }
            else if (filtri.paradigm_primary === 'traditional') { active.classList.remove('btn-outline-primary'); active.classList.add('btn-primary'); }
            else { active.classList.remove('btn-outline-secondary'); active.classList.add('btn-secondary'); }
        }
        aggiorna();
    });
});

// ── Tipo esercizio ────────────────────────────────────────────────────────
document.querySelectorAll('.filtro-excat').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;
        filtri.exercise_category = filtri.exercise_category === val && val !== '' ? '' : val;
        document.querySelectorAll('.filtro-excat').forEach(b => {
            b.classList.remove('btn-secondary'); b.classList.add('btn-outline-secondary');
        });
        if (filtri.exercise_category) {
            btn.classList.remove('btn-outline-secondary'); btn.classList.add('btn-secondary');
        } else {
            document.querySelector('.filtro-excat[data-val=""]').classList.remove('btn-outline-secondary');
            document.querySelector('.filtro-excat[data-val=""]').classList.add('btn-secondary');
        }
        aggiorna();
    });
});

// ── Cerca testo ───────────────────────────────────────────────────────────
let searchTimer;
document.getElementById('cerca-nome').addEventListener('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        filtri.q = this.value.trim();
        aggiorna();
    }, 300);
});
</script>
@endpush
