@extends('layouts.allenatore')
@section('title', 'Sedute')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Sedute</h2>
    <a href="{{ route('allenatore.sedute.create') }}" class="btn btn-primary">+ Nuova seduta</a>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Titolo</th><th>Data</th><th>Team</th><th>Stato</th>
                <th>Visibile</th><th>Scadenza</th><th>Feedback</th><th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($sedute as $s)
            <tr>
                <td><a href="{{ route('allenatore.sedute.show', $s) }}">{{ $s->titolo }}</a></td>
                <td>{{ $s->data->format('d/m/Y') }}</td>
                <td>{{ $s->team->nome }}</td>
                <td><x-stato-seduta :stato="$s->stato" /></td>
                <td>
                    @if($s->visibile_atleti)
                        <span class="badge bg-success">Si</span>
                    @else
                        <span class="badge bg-light text-dark border">No</span>
                    @endif
                </td>
                <td>
                    @if($s->scadenza_feedback)
                        <x-countdown-scadenza :scadenza="$s->scadenza_feedback" />
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-info">{{ $s->feedback_count ?? 0 }}</span>
                </td>
                <td>
                    <a href="{{ route('allenatore.sedute.show', $s) }}" class="btn btn-sm btn-outline-primary">Apri</a>
                    <form action="{{ route('allenatore.sedute.destroy', $s) }}" method="POST" class="d-inline"
                          data-confirm="Eliminare la seduta?">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Elimina</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">Nessuna seduta ancora.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $sedute->links() }}
@endsection
