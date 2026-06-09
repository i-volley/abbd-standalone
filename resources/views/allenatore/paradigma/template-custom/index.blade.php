@extends('layouts.allenatore')
@section('title', __('I miei template'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">{{ __('I miei template') }}</h2>
        <p class="text-muted small mb-0">{{ __('Template personalizzati — struttura libera in base alla tua seduta') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('allenatore.paradigma.templates') }}" class="btn btn-outline-secondary btn-sm">{{ __('Template di sistema') }}</a>
        <a href="{{ route('allenatore.paradigma.template-custom.create') }}" class="btn btn-primary">{{ __('+ Nuovo template') }}</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@forelse($templates as $tpl)
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h6 class="mb-0 fw-bold">{{ $tpl->name }}</h6>
                    @php
                        $parColors = ['traditional'=>'primary','ecological'=>'success','hybrid'=>'warning'];
                        $parLabels = ['traditional'=>'Traditional','ecological'=>'Ecological','hybrid'=>'Hybrid'];
                    @endphp
                    <span class="badge bg-{{ $parColors[$tpl->paradigm] ?? 'secondary' }}" style="font-size:.65rem">
                        {{ $parLabels[$tpl->paradigm] ?? $tpl->paradigm }}
                    </span>
                    @php $totMin = $tpl->blocks->sum('suggested_duration_minutes'); @endphp
                    @if($totMin)
                    <span class="badge bg-light text-dark border" style="font-size:.65rem">⏱ {{ $totMin }}'</span>
                    @endif
                    <span class="badge bg-light text-dark border" style="font-size:.65rem">{{ $tpl->blocks->count() }} {{ __('blocchi') }}</span>
                </div>
                @if($tpl->description)
                <p class="small text-muted mb-2">{{ $tpl->description }}</p>
                @endif
                <div class="d-flex flex-wrap gap-1">
                    @foreach($tpl->blocks as $b)
                    <span class="badge bg-{{ \App\Models\SessionTemplateBlock::blockTypeColor($b->block_type) }}" style="font-size:.6rem">
                        {{ $b->block_name }}
                        @if($b->suggested_duration_minutes) {{ $b->suggested_duration_minutes }}' @endif
                    </span>
                    @endforeach
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <a href="{{ route('allenatore.paradigma.template-custom.edit', $tpl) }}"
                   class="btn btn-sm btn-outline-secondary">{{ __('Modifica') }}</a>
                <form action="{{ route('allenatore.paradigma.template-custom.destroy', $tpl) }}" method="POST"
                      data-confirm="Delete template «{{ addslashes($tpl->name) }}»?">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">×</button>
                </form>
            </div>
        </div>
    </div>
</div>
@empty
<div class="alert alert-light border text-center py-5">
    <p class="mb-2 text-muted">{{ __('Nessun template personalizzato ancora.') }}</p>
    <a href="{{ route('allenatore.paradigma.template-custom.create') }}" class="btn btn-primary">{{ __('+ Nuovo template') }}</a>
</div>
@endforelse
@endsection
