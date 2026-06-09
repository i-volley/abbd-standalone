@extends('layouts.allenatore')
@section('title', $template->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>{{ $template->name }}</h2>
        @php $paradigmColors = ['traditional'=>'primary','ecological'=>'success','hybrid'=>'warning']; @endphp
        <span class="badge bg-{{ $paradigmColors[$template->paradigm] ?? 'secondary' }}">
            {{ ['traditional'=>'Traditional','ecological'=>'Ecological','hybrid'=>'Hybrid'][$template->paradigm] ?? $template->paradigm }}
        </span>
        @if($template->is_system)
        <span class="badge bg-dark ms-1">{{ __('Template di sistema') }}</span>
        @endif
    </div>
    <a href="{{ route('allenatore.paradigma.templates') }}" class="btn btn-outline-secondary">← {{ __('Tutti i template') }}</a>
</div>

@if($template->description)
<p class="text-muted mb-4">{{ $template->description }}</p>
@endif

{{-- Blocchi --}}
<div class="row g-3">
    @foreach($template->blocks as $block)
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-start gap-3">
                <div class="d-flex flex-column align-items-center" style="min-width:36px">
                    <span class="badge bg-{{ \App\Models\SessionTemplateBlock::blockTypeColor($block->block_type) }} rounded-circle"
                          style="width:32px;height:32px;line-height:22px;font-size:.85rem">
                        {{ $block->position }}
                    </span>
                    @if(!$loop->last)
                    <div style="width:2px;background:#dee2e6;flex:1;margin-top:4px;min-height:20px"></div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <strong>{{ $block->block_name }}</strong>
                        <span class="badge bg-{{ \App\Models\SessionTemplateBlock::blockTypeColor($block->block_type) }}" style="font-size:.7rem">
                            {{ \App\Models\SessionTemplateBlock::blockTypeLabel($block->block_type, $template->paradigm) }}
                        </span>
                        @if($block->constraint_focus && $block->constraint_focus !== 'none')
                        <span class="badge bg-outline-success border border-success text-success" style="font-size:.7rem">
                            {{ __('Vincolo') }}: {{ \App\Services\ParadigmService::constraintLabel($block->constraint_focus) }}
                        </span>
                        @endif
                        @if($block->suggested_duration_minutes)
                        <span class="badge bg-light text-dark" style="font-size:.7rem">
                            ⏱ {{ $block->suggested_duration_minutes }} min
                        </span>
                        @endif
                    </div>
                    @if($block->block_description)
                    <p class="small text-muted mb-0">{{ $block->block_description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Totale durata --}}
@php $totMin = $template->blocks->sum('suggested_duration_minutes'); @endphp
@if($totMin)
<div class="mt-3 text-muted small">
    {{ __('Durata totale stimata') }}: <strong>{{ $totMin }} {{ __('minuti') }}</strong>
</div>
@endif

@if($coach->paradigm === $template->paradigm)
<div class="alert alert-success mt-4">
    <strong>✓ {{ __('Questo è il template suggerito per la tua tipologia.') }}</strong>
    {{ __('Quando crei una nuova seduta troverai questo come struttura di partenza.') }}
</div>
@endif
@endsection
