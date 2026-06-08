@extends('layouts.allenatore')
@section('title', __('Unità Didattiche'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">{{ __('Unità Didattiche') }}</h2>
        <p class="text-muted small mb-0">{{ __('Gruppi di sedute con obiettivo permanente condiviso — Manuale FIPAV, Metodologia 1-6') }}</p>
    </div>
    <a href="{{ route('allenatore.unita-didattiche.create') }}" class="btn btn-primary">{{ __('+ Nuova unità didattica') }}</a>
</div>

@forelse($unita as $u)
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    @if($u->colore)
                    <span class="rounded-pill d-inline-block flex-shrink-0"
                          style="width:.75rem;height:.75rem;background:{{ $u->colore }};box-shadow:0 0 0 1px rgba(0,0,0,.12)"></span>
                    @endif
                    <h6 class="mb-0 fw-bold">
                        <a href="{{ route('allenatore.unita-didattiche.show', $u) }}" class="text-decoration-none">{{ $u->titolo }}</a>
                    </h6>
                    @if($u->data_inizio)
                        <small class="text-muted">
                            📅 {{ $u->data_inizio->format('d/m/Y') }}
                            @if($u->data_fine)
                                → {{ $u->data_fine->format('d/m/Y') }}
                            @endif
                        </small>
                    @endif
                </div>
                <p class="small text-muted mb-2">🎯 {{ Str::limit($u->obiettivo_permanente, 100) }}</p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-light text-dark border">{{ $u->sedute->count() }} {{ __('Sedute') }}</span>
                    @if($u->team)
                        <span class="badge bg-light text-dark border">{{ $u->team->nome }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <a href="{{ route('allenatore.unita-didattiche.show', $u) }}" class="btn btn-sm btn-outline-primary">{{ __('Vedi') }}</a>
                <a href="{{ route('allenatore.unita-didattiche.edit', $u) }}" class="btn btn-sm btn-outline-secondary">{{ __('Modifica') }}</a>
            </div>
        </div>
    </div>
</div>
@empty
<div class="alert alert-light border text-center py-5">
    <p class="mb-2 text-muted">{{ __('Nessuna unità didattica ancora.') }}</p>
    <a href="{{ route('allenatore.unita-didattiche.create') }}" class="btn btn-primary">{{ __('Nuova unità didattica') }}</a>
</div>
@endforelse

{{ $unita->links() }}
@endsection
