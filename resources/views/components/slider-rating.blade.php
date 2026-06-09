@props(['name', 'label', 'min' => 1, 'max' => 10, 'value' => 5])
@php
$labels = [
    1=>'Very easy',2=>'Very easy',3=>'Easy',4=>'Easy',
    5=>'Moderate',6=>'Moderate',7=>'Hard',8=>'Hard',
    9=>'Maximum',10=>'Maximum'
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
            var rpeLabels = {1:'Very easy',2:'Very easy',3:'Easy',4:'Easy',
                5:'Moderate',6:'Moderate',7:'Hard',8:'Hard',9:'Maximum',10:'Maximum'};
            lblEl.textContent = rpeLabels[parseInt(val)] || '';
        }
    }
    </script>
</div>
