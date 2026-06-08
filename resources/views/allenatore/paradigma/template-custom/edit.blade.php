@extends('layouts.allenatore')
@section('title', __('Modifica template'))

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('allenatore.paradigma.template-custom.index') }}" class="btn btn-outline-secondary btn-sm">←</a>
    <div>
        <h2 class="mb-0">{{ __('Modifica template') }}: {{ $template->name }}</h2>
        <small class="text-muted">{{ __('Struttura libera — aggiungi i blocchi che compongono la tua seduta') }}</small>
    </div>
</div>

<form action="{{ route('allenatore.paradigma.template-custom.update', $template) }}" method="POST" id="tpl-form">
@csrf @method('PUT')
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ __('Nome template') }} *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $template->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">{{ __('Tipologia') }} *</label>
        <select name="paradigm" class="form-select" required>
            <option value="traditional" {{ old('paradigm', $template->paradigm) === 'traditional' ? 'selected' : '' }}>🔵 Tradizionale</option>
            <option value="ecological"  {{ old('paradigm', $template->paradigm) === 'ecological'  ? 'selected' : '' }}>🟢 Ecologico</option>
            <option value="hybrid"      {{ old('paradigm', $template->paradigm) === 'hybrid'      ? 'selected' : '' }}>🟡 Ibrido</option>
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
        <textarea name="description" class="form-control" rows="2">{{ old('description', $template->description) }}</textarea>
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
    <button type="submit" class="btn btn-primary">{{ __('Salva modifiche') }}</button>
    <a href="{{ route('allenatore.paradigma.template-custom.index') }}" class="btn btn-outline-secondary">{{ __('Annulla') }}</a>
</div>
</form>

<hr class="mt-4">
<form action="{{ route('allenatore.paradigma.template-custom.destroy', $template) }}" method="POST"
      data-confirm="Eliminare il template «{{ addslashes($template->name) }}»?">
    @csrf @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">{{ __('Elimina template') }}</button>
</form>

</div>
</div>
@endsection

@push('scripts')
@php
    $existingBlocks = old('blocks')
        ? old('blocks')
        : $template->blocks->map(fn($b) => [
            'block_type'                 => $b->block_type,
            'block_name'                 => $b->block_name,
            'block_description'          => $b->block_description ?? '',
            'suggested_duration_minutes' => $b->suggested_duration_minutes ?? '',
          ])->toArray();
@endphp
@include('allenatore.paradigma.template-custom._block-builder', ['existingBlocks' => $existingBlocks])
@endpush
