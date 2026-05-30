@props(['name', 'label', 'value' => 0])
<div class="mb-3">
    <label class="form-label fw-semibold">{{ $label }}</label>
    <div class="stelle-rating" data-field="{{ $name }}">
        @for($i = 1; $i <= 5; $i++)
            <span class="star {{ $i <= $value ? 'active' : '' }}" data-val="{{ $i }}">&#9733;</span>
        @endfor
    </div>
    <input type="hidden" name="{{ $name }}" id="input-{{ $name }}" value="{{ $value }}">
</div>
<script>
document.querySelectorAll('.stelle-rating[data-field="{{ $name }}"]').forEach(function(container) {
    container.querySelectorAll('.star').forEach(function(star) {
        star.addEventListener('click', function() {
            var val = parseInt(this.dataset.val);
            document.getElementById('input-{{ $name }}').value = val;
            container.querySelectorAll('.star').forEach(function(s, idx) {
                s.classList.toggle('active', idx < val);
            });
        });
    });
});
</script>
