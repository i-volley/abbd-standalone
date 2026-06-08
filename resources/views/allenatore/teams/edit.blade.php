@extends('layouts.allenatore')
@section('title', __('Modifica Team'))

@section('content')
<h2 class="mb-4">{{ __('Modifica') }} — {{ $team->nome }}</h2>

<form action="{{ route('allenatore.teams.update', $team) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3" style="max-width:500px">

        <div class="col-12">
            <label class="form-label">{{ __('Nome team *') }}</label>
            <input type="text" name="nome" class="form-control"
                   value="{{ old('nome', $team->nome) }}" required>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Sport') }}</label>
            <select name="sport_id" class="form-select">
                @foreach($sports as $s)
                    <option value="{{ $s->id }}" {{ $team->sport_id == $s->id ? 'selected' : '' }}>
                        {{ $s->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Stagione') }}</label>
            <input type="text" name="stagione" class="form-control"
                   value="{{ old('stagione', $team->stagione) }}"
                   placeholder="{{ __('es. 2024-2025') }}">
        </div>

        {{-- ── SOGLIE CARICO SEDUTA ──────────────────────────────────── --}}
        <div class="col-12 mt-3">
            <div class="card border-secondary border-opacity-25">
                <div class="card-header bg-transparent py-2 d-flex align-items-center gap-2">
                    <small class="fw-semibold text-uppercase text-muted" style="font-size:.7rem;letter-spacing:.07em">
                        ⚡ {{ __('Soglie carico seduta') }}
                    </small>
                    <span data-bs-toggle="tooltip"
                          title="{{ __('Soglie usate nel pannello Carico durante la compilazione seduta. Formula: n_salti × serie × rip per ogni esercizio.') }}"
                          style="cursor:help;color:#6c757d">ⓘ</span>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-2">
                        <div class="col-12">
                            <small class="text-muted d-block mb-2" style="font-size:.8rem">
                                <strong>{{ __('Formula') }}:</strong>
                                {{ __('Totale salti') }} = Σ (n_salti × serie × rip) &nbsp;|&nbsp;
                                {{ __('Totale gesti') }} = Σ (n_gesti × serie × rip)
                            </small>
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-sm">⚡ {{ __('Salti — warning') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-warning">&gt;</span>
                                <input type="number" name="soglia_salti_warn" class="form-control"
                                       value="{{ old('soglia_salti_warn', $team->soglia_salti_warn ?? 250) }}"
                                       min="0" max="9999">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-sm">⚠️ {{ __('Salti — danger') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-danger">&gt;</span>
                                <input type="number" name="soglia_salti_danger" class="form-control"
                                       value="{{ old('soglia_salti_danger', $team->soglia_salti_danger ?? 400) }}"
                                       min="0" max="9999">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-sm">⚡ {{ __('Gesti — warning') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-warning">&gt;</span>
                                <input type="number" name="soglia_gesti_warn" class="form-control"
                                       value="{{ old('soglia_gesti_warn', $team->soglia_gesti_warn ?? 400) }}"
                                       min="0" max="9999">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-sm">⚠️ {{ __('Gesti — danger') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-danger">&gt;</span>
                                <input type="number" name="soglia_gesti_danger" class="form-control"
                                       value="{{ old('soglia_gesti_danger', $team->soglia_gesti_danger ?? 600) }}"
                                       min="0" max="9999">
                            </div>
                        </div>
                    </div>
                    <small class="text-muted" style="font-size:.75rem">
                        {{ __('Default FIPAV') }}: {{ __('Salti') }} 250 / 400 &nbsp;·&nbsp; {{ __('Gesti') }} 400 / 600
                    </small>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ __('Salva modifiche') }}</button>
            <a href="{{ route('allenatore.teams.index') }}" class="btn btn-outline-secondary">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>
@endsection
