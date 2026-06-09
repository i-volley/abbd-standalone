@extends('layouts.allenatore')
@section('title', __('Nuovo template'))

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('allenatore.paradigma.template-custom.index') }}" class="btn btn-outline-secondary btn-sm">←</a>
    <div>
        <h2 class="mb-0">{{ __('Nuovo template') }}</h2>
        <small class="text-muted">{{ __('Struttura libera — aggiungi i blocchi che compongono la tua seduta') }}</small>
    </div>
</div>

<form action="{{ route('allenatore.paradigma.template-custom.store') }}" method="POST" id="tpl-form">
@csrf
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ __('Nome template') }} *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name') }}" placeholder="{{ __('Es. Seduta 90 min ricezione-attacco') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">{{ __('Tipologia') }} *</label>
        <select name="paradigm" class="form-select" required>
            <option value="traditional" {{ old('paradigm','traditional') === 'traditional' ? 'selected' : '' }}>🔵 Traditional</option>
            <option value="ecological"  {{ old('paradigm') === 'ecological'  ? 'selected' : '' }}>🟢 Ecological</option>
            <option value="hybrid"      {{ old('paradigm') === 'hybrid'      ? 'selected' : '' }}>🟡 Hybrid</option>
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="w-100 text-center border rounded p-2" style="background:#f8f9fa">
            <div class="small text-muted">{{ __('Durata stimata') }}</div>
            <div class="fw-bold" id="tpl-total-dur">—</div>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('Descrizione') }} <small class="text-muted">({{ __('opzionale') }})</small></label>
        <textarea name="description" class="form-control" rows="2"
                  placeholder="{{ __('Es. Per sedute da 75-90 min con focus sulla fase break-point') }}">{{ old('description') }}</textarea>
    </div>
</div>

{{-- Block builder --}}
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">{{ __('Blocchi') }}</h5>
    <button type="button" class="btn btn-success btn-sm" id="btn-add-block">+ {{ __('Aggiungi blocco') }}</button>
</div>

@error('blocks')
<div class="alert alert-danger py-2 small">{{ $message }}</div>
@enderror

<div id="blocks-list" class="d-flex flex-column gap-2 mb-4">
    {{-- JS popola --}}
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ __('Salva template') }}</button>
    <a href="{{ route('allenatore.paradigma.template-custom.index') }}" class="btn btn-outline-secondary">{{ __('Annulla') }}</a>
</div>
</form>

</div>
</div>
@endsection

@push('scripts')
@include('allenatore.paradigma.template-custom._block-builder', ['existingBlocks' => old('blocks', [])])
@endpush
