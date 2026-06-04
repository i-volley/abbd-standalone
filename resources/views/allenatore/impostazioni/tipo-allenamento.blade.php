@extends('layouts.allenatore')
@section('title', 'Tipi Allenamento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">⚙️ Tipi di allenamento</h2>
    <a href="{{ route('allenatore.parametri.index') }}" class="btn btn-outline-secondary btn-sm">← Impostazioni</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent fw-semibold">Tipi configurati per questo team</div>
    <div class="card-body p-0">
        @forelse($tipi as $tipo)
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            {{-- Nome inline edit --}}
            <form action="{{ route('allenatore.tipo-allenamento.update', $tipo) }}" method="POST"
                  class="d-flex align-items-center gap-2 flex-grow-1 me-3">
                @csrf @method('PATCH')
                <input type="text" name="nome" value="{{ $tipo->nome }}"
                       class="form-control form-control-sm" style="max-width:250px">
                <button class="btn btn-sm btn-outline-primary">Salva</button>
            </form>
            {{-- Elimina --}}
            <form action="{{ route('allenatore.tipo-allenamento.destroy', $tipo) }}" method="POST"
                  data-confirm="Eliminare il tipo «{{ addslashes($tipo->nome) }}»?">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">×</button>
            </form>
        </div>
        @empty
        <p class="text-muted px-3 py-2 mb-0">Nessun tipo configurato.</p>
        @endforelse
    </div>

    {{-- Aggiungi nuovo --}}
    <div class="card-footer bg-light">
        <form action="{{ route('allenatore.tipo-allenamento.store') }}" method="POST"
              class="d-flex gap-2 align-items-center">
            @csrf
            <input type="text" name="nome" class="form-control form-control-sm"
                   placeholder="Nuovo tipo (es. Crossfit, Tennis...)" required maxlength="100" style="max-width:300px">
            <button class="btn btn-success btn-sm">+ Aggiungi</button>
        </form>
    </div>
</div>

<div class="alert alert-info small">
    <strong>Tipi predefiniti</strong>: Allenamento, Sala Pesi, Piscina, Campo da Beach — già presenti alla creazione del team. Puoi rinominarli o aggiungerne di nuovi.
</div>
@endsection
