@extends('layouts.allenatore')
@section('title', $esercizio->nome)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $esercizio->nome }}</h2>
    <a href="{{ route('allenatore.esercizi.edit', $esercizio) }}" class="btn btn-outline-secondary">Modifica</a>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Fase</dt>
                    <dd class="col-7"><span class="badge bg-secondary">{{ $esercizio->fase }}</span></dd>
                    <dt class="col-5">Metodologia</dt>
                    <dd class="col-7">{{ $esercizio->metodologia }}</dd>
                    <dt class="col-5">Gesto tecnico</dt>
                    <dd class="col-7">{{ $esercizio->gestoTecnico?->nome ?? '—' }}</dd>
                    <dt class="col-5">Durata</dt>
                    <dd class="col-7">{{ $esercizio->durata_min }} min</dd>
                    <dt class="col-5">N. Salti</dt>
                    <dd class="col-7">{{ $esercizio->n_salti }}</dd>
                    <dt class="col-5">N. Gesti</dt>
                    <dd class="col-7">{{ $esercizio->n_gesti }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @if($esercizio->capacita->count())
        <div class="card shadow-sm mb-3">
            <div class="card-header">Capacità allenate</div>
            <div class="card-body d-flex flex-wrap gap-2">
                @foreach($esercizio->capacita as $c)
                    <x-badge-capacita :capacita="$c" />
                @endforeach
            </div>
        </div>
        @endif

        @if($esercizio->video_url)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <a href="{{ $esercizio->video_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                    ▶ Guarda il video
                </a>
            </div>
        </div>
        @endif
    </div>

    @if($esercizio->descrizione)
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">Note metodologiche</div>
            <div class="card-body">{{ $esercizio->descrizione }}</div>
        </div>
    </div>
    @endif
</div>
@endsection
