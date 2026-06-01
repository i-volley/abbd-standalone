<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ABBD') — Allenatore</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

{{-- ── TOPBAR MOBILE (visibile solo < 768px via CSS) ──────────────────────── --}}
<div class="mobile-topbar">
    <button class="hamburger" id="abbd-sidebar-toggle" aria-label="Apri menu">☰</button>
    <a class="brand" href="{{ route('allenatore.dashboard') }}">⚡ ABBD</a>
</div>
<div class="sidebar-overlay" id="abbd-sidebar-overlay"></div>

@php
    // Team attivo in sessione (letto una volta per il layout intero)
    $currentTeam = session('current_team_id')
        ? \App\Models\Team::find(session('current_team_id'))
        : null;
@endphp

<nav class="sidebar">
    <a class="sidebar-brand" href="{{ route('allenatore.dashboard') }}">⚡ ABBD</a>

    <ul class="nav flex-column">

        {{-- ── ENTRY POINT: I MIEI TEAM ──────────────────────────────────── --}}
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ request()->routeIs('allenatore.teams*') ? 'active' : '' }}"
               href="{{ route('allenatore.teams.index') }}">
                👥 I miei Team
            </a>
        </li>

        {{-- ── CONTESTO TEAM ATTIVO ──────────────────────────────────────── --}}
        @if($currentTeam)
        <li class="nav-item mt-1">
            {{-- Label team attivo --}}
            <div class="px-3 py-1 d-flex align-items-center justify-content-between"
                 style="background:rgba(255,255,255,.07);border-radius:.3rem;margin:0 .5rem">
                <small class="text-white fw-bold" style="font-size:.75rem;letter-spacing:.04em">
                    {{ Str::limit($currentTeam->nome, 20) }}
                </small>
                <a href="{{ route('allenatore.teams.index') }}"
                   class="text-white-50 ms-1" style="font-size:.7rem;text-decoration:none" title="Cambia team">✕</a>
            </div>
        </li>

        {{-- Sub-menu team attivo --}}
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('allenatore.teams.hub') ? 'active' : '' }}"
               href="{{ route('allenatore.teams.hub', $currentTeam) }}"
               style="font-size:.9rem">
                ↳ Riepilogo team
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('allenatore.stagioni*') ? 'active' : '' }}"
               href="{{ route('allenatore.stagioni.index') }}"
               style="font-size:.9rem">
                ↳ Pianificazione
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('allenatore.sedute*') ? 'active' : '' }}"
               href="{{ route('allenatore.sedute.index') }}"
               style="font-size:.9rem">
                ↳ Sedute
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('allenatore.unita-didattiche*') ? 'active' : '' }}"
               href="{{ route('allenatore.unita-didattiche.index') }}"
               style="font-size:.9rem">
                ↳ Unità Didattiche
            </a>
        </li>
        @endif

        {{-- ── SEZIONI GLOBALI ───────────────────────────────────────────── --}}
        <li class="nav-item mt-2">
            <a class="nav-link {{ request()->routeIs('allenatore.dashboard') ? 'active' : '' }}"
               href="{{ route('allenatore.dashboard') }}">
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.esercizi*') ? 'active' : '' }}"
               href="{{ route('allenatore.esercizi.index') }}">
                Catalogo Esercizi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.wizard*') ? 'active' : '' }}"
               href="{{ route('allenatore.wizard.index') }}"
               style="{{ request()->routeIs('allenatore.wizard*') ? '' : 'color:#f59e0b;font-weight:600' }}">
                🔍 Wizard Diagnostico
            </a>
        </li>

        {{-- ── IMPOSTAZIONI ──────────────────────────────────────────────── --}}
        <li class="nav-item mt-3">
            <a class="nav-link {{ request()->routeIs('allenatore.sports*') ? 'active' : '' }}"
               href="{{ route('allenatore.sports.index') }}">
                Impostazioni
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('allenatore.parametri*') ? 'active' : '' }}"
               href="{{ route('allenatore.parametri.index') }}"
               style="font-size:.9rem">
                ↳ Parametri esercizio
            </a>
        </li>

    </ul>

    <div class="mt-auto p-3" style="position:absolute;bottom:0;width:100%">
        <small class="text-muted d-block mb-1">{{ auth()->user()->name }}</small>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-secondary w-100">Esci</button>
        </form>
    </div>
</nav>

<main class="main-content">
    <x-alert />
    @yield('content')
</main>

{{-- ── MODAL DOPPIA CONFERMA (globale) ─────────────────────────────────────── --}}
<div class="modal fade" id="abbd-confirm-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span style="font-size:1.4rem">⚠️</span>
                    <span>Conferma eliminazione</span>
                </h5>
            </div>
            <div class="modal-body pt-2 pb-1">
                <p id="abbd-confirm-msg" class="mb-2" style="font-size:.95rem"></p>
                <p class="text-danger small mb-0" style="font-size:.78rem">
                    Questa operazione <strong>non può essere annullata</strong>.
                </p>
            </div>
            <div class="modal-footer border-0 gap-2">
                <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Annulla</button>
                <button type="button" id="abbd-confirm-btn" class="btn btn-danger" disabled>
                    <span id="abbd-confirm-countdown" class="me-1" style="font-size:.8rem"></span>
                    Elimina definitivamente
                </button>
            </div>
        </div>
    </div>
</div>

@stack('scripts')
<script>
/*
 * ABBD — Doppia conferma eliminazione (globale)
 * Bootstrap è caricato via Vite come ES module (deferred).
 * window 'load' garantisce che bootstrap sia disponibile prima di usarlo.
 */
window.addEventListener('load', function () {

    /* ── Sidebar mobile: toggle hamburger + overlay ──────────────────────── */
    (function () {
        var sidebar = document.querySelector('.sidebar');
        var toggle  = document.getElementById('abbd-sidebar-toggle');
        var overlay = document.getElementById('abbd-sidebar-overlay');
        if (!sidebar || !toggle || !overlay) return;

        function open()  { sidebar.classList.add('open');  overlay.classList.add('show'); }
        function close() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

        toggle.addEventListener('click', function () {
            sidebar.classList.contains('open') ? close() : open();
        });
        overlay.addEventListener('click', close);

        // Chiudi al click su un link del menu (navigazione)
        sidebar.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', close);
        });
    })();

    var _pendingForm   = null;
    var _countdownTimer = null;
    var modalEl  = document.getElementById('abbd-confirm-modal');
    var msgEl    = document.getElementById('abbd-confirm-msg');
    var confirmBtn  = document.getElementById('abbd-confirm-btn');
    var countdownEl = document.getElementById('abbd-confirm-countdown');

    function getModal() {
        // Lazy: crea o riusa l'istanza Bootstrap Modal
        return bootstrap.Modal.getOrCreateInstance(modalEl);
    }

    function startCountdown() {
        confirmBtn.disabled = true;
        var secs = 2;
        countdownEl.textContent = '(' + secs + 's)';
        clearInterval(_countdownTimer);
        _countdownTimer = setInterval(function () {
            secs--;
            if (secs <= 0) {
                clearInterval(_countdownTimer);
                confirmBtn.disabled = false;
                countdownEl.textContent = '';
            } else {
                countdownEl.textContent = '(' + secs + 's)';
            }
        }, 1000);
    }

    // Intercetta submit su qualsiasi form con data-confirm
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.matches || !form.matches('form[data-confirm]')) return;

        e.preventDefault();
        e.stopImmediatePropagation();

        _pendingForm = form;
        msgEl.textContent = form.dataset.confirm || 'Sei sicuro?';
        startCountdown();
        getModal().show();
    }, true); // capture phase — intercetta prima di qualsiasi handler inline

    // Bottone conferma: sottomette il form
    confirmBtn.addEventListener('click', function () {
        if (!_pendingForm) return;
        var form = _pendingForm;
        _pendingForm = null;
        getModal().hide();
        delete form.dataset.confirm; // evita loop
        form.submit();
    });

    // Reset stato alla chiusura
    modalEl.addEventListener('hidden.bs.modal', function () {
        clearInterval(_countdownTimer);
        _pendingForm   = null;
        confirmBtn.disabled = true;
        countdownEl.textContent = '';
    });
});
</script>
</body>
</html>
