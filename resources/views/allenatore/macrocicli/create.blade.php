@extends('layouts.allenatore')
@section('title', __('Nuovo Macrociclo'))

@section('content')
<h2 class="mb-4">{{ __('Nuovo Macrociclo') }} — {{ $stagione->nome }}</h2>
<form action="{{ route('allenatore.stagioni.macrocicli.store', $stagione) }}" method="POST">
    @csrf
    <div class="row g-3" style="max-width:560px">
        <div class="col-12">
            <label class="form-label">{{ __('Nome') }}</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome','Pre-campionato') }}" required>
        </div>
        <div class="col-md-7">
            <label class="form-label">{{ __('Fase') }}</label>
            <select name="fase" class="form-select" required id="selFase">
                @php $coloriDefault = \App\Models\Macrociclo::coloriDefault(); @endphp
                @foreach(['preparazione','competizione','transizione'] as $f)
                    <option value="{{ $f }}" {{ old('fase') === $f ? 'selected' : '' }}>{{ ucfirst($f) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label">{{ __('Colore calendario') }}</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" name="colore" id="colore"
                       class="form-control form-control-color"
                       style="width:3rem;height:2.6rem;padding:.15rem .25rem"
                       value="{{ old('colore', '#3b82f6') }}">
                <span id="colorePreview" class="badge rounded-pill px-3 py-2" style="background:#3b82f6;font-size:.85rem">
                    {{ __('Anteprima') }}
                </span>
            </div>
        </div>
        <div class="col-6">
            <label class="form-label">{{ __('Inizio') }}</label>
            <input type="date" name="data_inizio" class="form-control"
                   value="{{ old('data_inizio', $suggerisciInizio) }}" required>
        </div>
        <div class="col-6">
            <label class="form-label">{{ __('Fine') }}</label>
            <input type="date" name="data_fine" class="form-control"
                   value="{{ old('data_fine') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('Obiettivo') }}</label>
            <textarea name="obiettivi" class="form-control" rows="2">{{ old('obiettivi') }}</textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ __('Crea macrociclo') }}</button>
            <a href="{{ route('allenatore.stagioni.show', $stagione) }}" class="btn btn-outline-secondary ms-2">{{ __('Annulla') }}</a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const coloriDefault = @json(\App\Models\Macrociclo::coloriDefault());
    const selFase   = document.getElementById('selFase');
    const picker    = document.getElementById('colore');
    const preview   = document.getElementById('colorePreview');

    function aggiornaColore(hex) {
        picker.value  = hex;
        preview.style.background = hex;
    }

    selFase.addEventListener('change', function () {
        if (coloriDefault[this.value]) aggiornaColore(coloriDefault[this.value]);
    });

    picker.addEventListener('input', function () {
        preview.style.background = this.value;
    });

    // Imposta colore default alla fase già selezionata
    if (coloriDefault[selFase.value] && !'{{ old('colore') }}') {
        aggiornaColore(coloriDefault[selFase.value]);
    }
})();
</script>
@endpush
