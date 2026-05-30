@extends('layouts.allenatore')
@section('title', 'Impostazioni Sport')

@section('content')
<h2 class="mb-4">Impostazioni — Sport</h2>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header">Aggiungi sport</div>
            <div class="card-body">
                <form action="{{ route('allenatore.sports.store') }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2">
                        <input type="text" name="nome" class="form-control" placeholder="es. Pallavolo" required>
                        <button type="submit" class="btn btn-primary">Aggiungi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header">Sport esistenti</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                    @foreach($sports as $s)
                    <tr>
                        <td>{{ $s->nome }}</td>
                        <td>
                            @if($s->attivo)<span class="badge bg-success">Attivo</span>@else<span class="badge bg-secondary">Inattivo</span>@endif
                        </td>
                        <td>
                            <form action="{{ route('allenatore.sports.destroy', $s) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Eliminare?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Elimina</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('allenatore.gesti-tecnici.index') }}" class="btn btn-outline-secondary">Gesti Tecnici →</a>
</div>
@endsection
