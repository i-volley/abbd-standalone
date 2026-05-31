@props(['categoria'])
@if($categoria)
<span class="badge rounded-pill"
      style="background-color: {{ $categoria->colore }}; font-size:.72rem">
    {{ $categoria->nome }}
</span>
@else
<span class="badge rounded-pill bg-secondary" style="font-size:.72rem">—</span>
@endif
