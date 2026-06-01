@extends('layouts.allenatore')
@section('title', 'Modifica Macrociclo')

@section('content')
<h2 class="mb-1">Modifica — {{ $macrociclo->nome }}</h2>
<small class="text-muted d-block mb-4">
    Stagione: {{ $macrociclo->stagione->nome }}
</small>

<form action="{{ route('allenatore.macrocicli.update', $macrociclo) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3" style="max-width:560px">

        <div class="col-12">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control"
                   value="{{ old('nome', $macrociclo->nome) }}" required>
        </div>

        <div class="col-md-7">
            <label class="form-label">Fase</label>
            <select name="fase" class="form-select" required id="selFase">
                @php $coloriDefault = \App\Models\Macrociclo::coloriDefault(); @endphp
                @foreach(['preparazione','competizione','transizione'] as $f)
                    <option value="{{ $f }}" {{ old('fase', $macrociclo->fase) === $f ? 'selected' : '' }}>
                        {{ ucfirst($f) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label">Colore calendario</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" name="colore" id="colore"
                       class="form-control form-control-color"
                       style="width:3rem;height:2.6rem;padding:.15rem .25rem"
                       value="{{ old('colore', $macrociclo->colore ?? '#4f46e5') }}">
                <span id="colorePreview" class="badge rounded-pill px-3 py-2"
                      style="background:{{ $macrociclo->colore ?? '#4f46e5' }};font-size:.85rem">
                    Anteprima
                </span>
            </div>
        </div>

        <div class="col-6">
            <label class="form-label">Inizio</label>
            <input type="date" name="data_inizio" class="form-control"
                   value="{{ old('data_inizio', $macrociclo->data_inizio->format('Y-m-d')) }}" required>
        </div>

        <div class="col-6">
            <label class="form-label">Fine</label>
            <input type="date" name="data_fine" class="form-control"
                   value="{{ old('data_fine', $macrociclo->data_fine->format('Y-m-d')) }}" required>
        </div>

        <div class="col-12">
            <label class="form-label">Obiettivi</label>
            <textarea name="obiettivi" class="form-control" rows="2">{{ old('obiettivi', $macrociclo->obiettivi) }}</textarea>
        </div>

        <div class="col-12 d-flex gap-2 align-items-center">
            <button type="submit" class="btn btn-primary">Salva modifiche</button>
            <a href="{{ route('allenatore.macrocicli.show', $macrociclo) }}"
               class="btn btn-outline-secondary">Annulla</a>
            <form action="{{ route('allenatore.macrocicli.destroy', $macrociclo) }}" method="POST"
                  class="ms-auto"
                  data-confirm="Eliminare il macrociclo «{{ addslashes($macrociclo->nome) }}»?">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">Elimina macrociclo</button>
            </form>
        </div>

    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const picker  = document.getElementById('colore');
    const preview = document.getElementById('colorePreview');
    picker.addEventListener('input', function () {
        preview.style.background = this.value;
    });
})();
</script>
@endpush
