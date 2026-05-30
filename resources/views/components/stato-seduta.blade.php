@props(['stato'])
@php
$map = ['bozza' => 'secondary', 'pubblicata' => 'primary', 'completata' => 'success'];
$color = $map[$stato] ?? 'secondary';
@endphp
<span class="badge bg-{{ $color }}">{{ ucfirst($stato) }}</span>
