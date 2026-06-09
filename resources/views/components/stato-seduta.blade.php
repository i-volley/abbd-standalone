@props(['stato'])
@php
$map = ['bozza' => 'secondary', 'pubblicata' => 'primary', 'completata' => 'success'];
$label = ['bozza' => 'Draft', 'pubblicata' => 'Published', 'completata' => 'Completed'];
$color = $map[$stato] ?? 'secondary';
$text  = $label[$stato] ?? ucfirst($stato);
@endphp
<span class="badge bg-{{ $color }}">{{ $text }}</span>
