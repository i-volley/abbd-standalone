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

<script>
if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('/sw.js').then(function(reg) {
        return reg.pushManager.getSubscription().then(function(sub) {
            if (sub) return sub;
            return reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array('{{ config("webpush.vapid.public_key") }}')
            });
        });
    }).then(function(sub) {
        fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(sub)
        });
    }).catch(function(err) { console.warn('Push non disponibile:', err); });
}

function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64   = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    var rawData  = window.atob(base64);
    var arr      = new Uint8Array(rawData.length);
    for (var i = 0; i < rawData.length; ++i) arr[i] = rawData.charCodeAt(i);
    return arr;
}
</script>

@stack('scripts')
</body>
</html>
