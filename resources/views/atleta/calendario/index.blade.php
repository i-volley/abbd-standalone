@extends('layouts.atleta')
@section('title', 'Calendario stagione')

@php
$nomiGiorni = ['Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'];
$nomiMesi   = ['','Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
@endphp

@section('content')
<div class="d-flex align-items-baseline gap-3 mb-1">
    <h3 class="mb-0">Calendario stagione</h3>
</div>
@if($stagione)
    <p class="text-muted mb-4" style="font-size:.9rem">
        {{ $stagione->nome }}
        &nbsp;·&nbsp;
        {{ $stagione->data_inizio->format('d/m/Y') }} – {{ $stagione->data_fine->format('d/m/Y') }}
    </p>
@endif

@if($settimane->isEmpty())
    <div class="alert alert-info">
        @if(!$stagione)
            Nessuna stagione attiva per il tuo team.
        @else
            Nessun allenamento programmato per le prossime settimane.
        @endif
    </div>
@else
    @foreach($settimane as $weekKey => $slots)
    @php
        $lunedi = \Carbon\Carbon::parse($weekKey);
        $domenica = $lunedi->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
    @endphp
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="fw-semibold text-uppercase"
                  style="font-size:.75rem;letter-spacing:.08em;color:#6b7280">
                Settimana {{ $lunedi->format('d/m') }} – {{ $domenica->format('d/m') }}
            </span>
        </div>

        @foreach($slots as $slot)
        @php
            $seduta = $slot['seduta'];
            $giorno = $slot['giorno'];
            $data   = $slot['data'];
            $oggi   = \Carbon\Carbon::today();
            $isOggi = $data->isSameDay($oggi);
        @endphp
        <div class="card mb-2 shadow-sm border-0 {{ $isOggi ? 'border-start border-primary border-3' : '' }}"
             style="{{ !$seduta ? 'opacity:.72' : '' }}; {{ $isOggi ? 'border-left:3px solid #0d6efd !important' : '' }}">
            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center gap-2 flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    {{-- Data pill --}}
                    <div class="text-center flex-shrink-0"
                         style="min-width:44px;background:{{ $isOggi ? '#0d6efd' : '#f1f5f9' }};border-radius:.5rem;padding:.35rem .5rem">
                        <div class="fw-bold" style="font-size:1.1rem;line-height:1;color:{{ $isOggi ? '#fff' : '#1e293b' }}">
                            {{ $data->format('d') }}
                        </div>
                        <div class="text-uppercase" style="font-size:.6rem;color:{{ $isOggi ? '#e0e7ff' : '#94a3b8' }}">
                            {{ $nomiMesi[$data->month] }}
                        </div>
                    </div>
                    {{-- Info --}}
                    <div>
                        <div class="fw-semibold small">
                            {{ $nomiGiorni[$data->dayOfWeek] }}
                            <span class="text-muted fw-normal" style="font-size:.82rem">{{ $giorno->orario }}</span>
                            @if($isOggi)
                                <span class="badge bg-primary ms-1" style="font-size:.6rem">Oggi</span>
                            @endif
                        </div>
                        <div class="d-flex gap-1 flex-wrap align-items-center mt-1">
                            @if($giorno->tipoAllenamento)
                                <span class="badge bg-secondary rounded-pill" style="font-size:.62rem">
                                    {{ $giorno->tipoAllenamento->nome }}
                                </span>
                            @endif
                            @if($giorno->luogo)
                                <span class="text-muted" style="font-size:.78rem">📍 {{ $giorno->luogo }}</span>
                            @endif
                            @if($seduta && $seduta->titolo)
                                <span class="text-muted" style="font-size:.78rem">· {{ $seduta->titolo }}</span>
                            @endif
                            @if($seduta && $seduta->obiettivo_principale)
                                <span class="text-muted" style="font-size:.75rem">🎯 {{ $seduta->obiettivo_principale }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stato / azione --}}
                <div class="flex-shrink-0 text-end">
                    @if($seduta)
                        @if($slot['feedback_inviato'])
                            <span class="badge bg-success rounded-pill me-1">✓ Feedback inviato</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill me-1" style="font-size:.7rem">Feedback da inviare</span>
                        @endif
                        <a href="{{ route('atleta.sedute.show', $seduta) }}"
                           class="btn btn-sm btn-outline-primary">Vedi</a>
                    @else
                        <span class="badge rounded-pill"
                              style="background:#f1f5f9;color:#94a3b8;font-size:.7rem;border:1px solid #e2e8f0">
                            Programmato
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
@endif
@endsection
