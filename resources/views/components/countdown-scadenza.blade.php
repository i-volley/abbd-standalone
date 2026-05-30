@props(['scadenza'])
@if($scadenza)
    @php
        $ore  = now()->diffInHours($scadenza, false);
        $giorni = now()->diffInDays($scadenza, false);
    @endphp
    @if($ore < 0)
        <span class="badge bg-danger badge-scadenza">Scaduto</span>
    @elseif($ore < 24)
        <span class="badge bg-warning text-dark badge-scadenza">Scade tra {{ $ore }}h</span>
    @else
        <span class="badge bg-info text-dark badge-scadenza">{{ $giorni }} giorni</span>
    @endif
@endif
