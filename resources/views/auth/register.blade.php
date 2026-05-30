<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione — ABBD</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="card shadow-sm" style="width:420px">
    <div class="card-body p-4">
        <h4 class="card-title mb-4 text-center">⚡ ABBD Registrazione</h4>

        @if($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Conferma password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ruolo</label>
                <select name="ruolo" class="form-select" required>
                    <option value="">Scegli ruolo...</option>
                    <option value="allenatore" {{ old('ruolo') === 'allenatore' ? 'selected' : '' }}>Allenatore</option>
                    <option value="atleta" {{ old('ruolo') === 'atleta' ? 'selected' : '' }}>Atleta</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Registrati</button>
        </form>

        <hr>
        <p class="text-center mb-0 small">
            Hai già un account?
            <a href="{{ route('login') }}">Accedi</a>
        </p>
    </div>
</div>
</body>
</html>
