@extends('layouts.allenatore')
@section('title', 'Template di Seduta')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Template di Seduta</h2>
        <p class="text-muted mb-0">Template di sistema per ogni paradigma pedagogico.</p>
    </div>
    <a href="{{ route('allenatore.paradigma.settings') }}" class="btn btn-outline-secondary">← Impostazioni</a>
</div>

@php
    $paradigmColors = ['traditional'=>'primary','ecological'=>'success','hybrid'=>'warning'];
    $paradigmLabels = ['traditional'=>'🔵 Tradizionale','ecological'=>'🟢 Ecologico','hybrid'=>'🟡 Ibrido'];
@endphp

@foreach(['traditional','ecological','hybrid'] as $par)
@if(isset($templates[$par]))
<div class="mb-4">
    <h5>
        <span class="badge bg-{{ $paradigmColors[$par] }} me-2">{{ $paradigmLabels[$par] }}</span>
        @if($coach->paradigm === $par)
        <span class="badge bg-dark">Il tuo paradigma</span>
        @endif
    </h5>
    <div class="row g-3">
        @foreach($templates[$par] as $tpl)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong class="small">{{ $tpl->name }}</strong>
                    @if($tpl->is_system)
                    <span class="badge bg-secondary" style="font-size:.65rem">sistema</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($tpl->description)
                    <p class="small text-muted mb-2">{{ $tpl->description }}</p>
                    @endif
                    <div class="d-flex flex-column gap-1">
                        @foreach($tpl->blocks->take(4) as $block)
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-{{ \App\Models\SessionTemplateBlock::blockTypeColor($block->block_type) }}"
                                  style="font-size:.65rem;min-width:70px;text-align:center">
                                {{ \App\Models\SessionTemplateBlock::blockTypeLabel($block->block_type, $par) }}
                            </span>
                            <small class="text-muted">{{ $block->block_name }}</small>
                            @if($block->suggested_duration_minutes)
                            <small class="text-muted ms-auto">{{ $block->suggested_duration_minutes }}'</small>
                            @endif
                        </div>
                        @endforeach
                        @if($tpl->blocks->count() > 4)
                        <small class="text-muted">+ {{ $tpl->blocks->count() - 4 }} altri blocchi...</small>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('allenatore.paradigma.preview', $tpl) }}" class="btn btn-sm btn-outline-secondary">
                        Vedi anteprima completa
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endforeach
@endsection
