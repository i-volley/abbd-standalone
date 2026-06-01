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

<nav class="sidebar">
    <a class="sidebar-brand" href="{{ route('allenatore.dashboard') }}">⚡ ABBD</a>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.dashboard') ? 'active' : '' }}"
               href="{{ route('allenatore.dashboard') }}">
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.wizard*') ? 'active' : '' }}"
               href="{{ route('allenatore.wizard.index') }}"
               style="{{ request()->routeIs('allenatore.wizard*') ? '' : 'color:#f59e0b;font-weight:600' }}">
                🔍 Wizard Diagnostico
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.esercizi*') ? 'active' : '' }}"
               href="{{ route('allenatore.esercizi.index') }}">
                Catalogo Esercizi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.teams*') ? 'active' : '' }}"
               href="{{ route('allenatore.teams.index') }}">
                I miei Team
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.stagioni*') ? 'active' : '' }}"
               href="{{ route('allenatore.stagioni.index') }}">
                Pianificazione
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.sedute*') ? 'active' : '' }}"
               href="{{ route('allenatore.sedute.index') }}">
                Sedute
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('allenatore.unita-didattiche*') ? 'active' : '' }}"
               href="{{ route('allenatore.unita-didattiche.index') }}">
                Unità Didattiche
            </a>
        </li>
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

@stack('scripts')
</body>
</html>
