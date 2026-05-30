@props(['name', 'label', 'min' => 1, 'max' => 10, 'value' => 5])
@php
$labels = [
    1=>'Molto facile',2=>'Molto facile',3=>'Facile',4=>'Facile',
    5=>'Moderato',6=>'Moderato',7=>'Faticoso',8=>'Faticoso',
    9=>'Massimale',10=>'Massimale'
];
@endphp
<div class="mb-4">
    <label class="form-label fw-semibold">{{ $label }}
        <span class="badge bg-primary ms-2" id="val-{{ $name }}">{{ $value }}</span>
        @if($max === 10)
        <span class="slider-label ms-2" id="lbl-{{ $name }}">{{ $labels[$value] ?? '' }}</span>
        @endif
    </label>
    <input type="range" class="form-range" name="{{ $name }}" id="range-{{ $name }}"
           min="{{ $min }}" max="{{ $max }}" value="{{ $value }}"
           oninput="updateSlider('{{ $name }}', this.value, {{ $max }})">
    <div class="d-flex justify-content-between slider-label">
        <span>{{ $min }}</span><span>{{ $max }}</span>
    </div>
    <script>
    function updateSlider(name, val, max) {
        document.getElementById('val-'+name).textContent = val;
        var lblEl = document.getElementById('lbl-'+name);
        if (lblEl) {
            var rpeLabels = {1:'Molto facile',2:'Molto facile',3:'Facile',4:'Facile',
                5:'Moderato',6:'Moderato',7:'Faticoso',8:'Faticoso',9:'Massimale',10:'Massimale'};
            lblEl.textContent = rpeLabels[parseInt(val)] || '';
        }
    }
    </script>
</div>
