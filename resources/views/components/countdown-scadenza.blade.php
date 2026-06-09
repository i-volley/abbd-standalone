@props(['scadenza'])
@if($scadenza)
    @php
        $ore  = now()->diffInHours($scadenza, false);
        $giorni = now()->diffInDays($scadenza, false);
    @endphp
    @if($ore < 0)
        <span class="badge bg-danger badge-scadenza">Expired</span>
    @elseif($ore < 24)
        <span class="badge bg-warning text-dark badge-scadenza">Expires in {{ $ore }}h</span>
    @else
        <span class="badge bg-info text-dark badge-scadenza">{{ intval($giorni) }} days</span>
    @endif
@endif
