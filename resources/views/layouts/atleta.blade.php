<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ABBD') — Le mie sedute</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('atleta.sedute') }}">⚡ ABBD</a>
        <div class="navbar-nav ms-auto d-flex flex-row gap-3 align-items-center">
            <a class="nav-link {{ request()->routeIs('atleta.sedute') ? 'text-white' : 'text-secondary' }}"
               href="{{ route('atleta.sedute') }}">Sedute</a>
            <a class="nav-link {{ request()->routeIs('atleta.storico') ? 'text-white' : 'text-secondary' }}"
               href="{{ route('atleta.storico') }}">Storico</a>
            <small class="text-muted">{{ auth()->user()->name }}</small>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Esci</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">
    <x-alert />
    @yield('content')
</div>

@stack('scripts')
</body>
</html>
