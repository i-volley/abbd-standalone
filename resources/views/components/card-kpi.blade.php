@props(['label', 'value', 'color' => 'primary', 'suffix' => ''])
<div class="card border-0 shadow-sm h-100">
    <div class="card-body text-center">
        <div class="display-6 fw-bold text-{{ $color }}">{{ $value }}{{ $suffix }}</div>
        <div class="text-muted small mt-1">{{ $label }}</div>
    </div>
</div>
