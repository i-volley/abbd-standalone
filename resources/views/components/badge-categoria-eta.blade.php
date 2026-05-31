@props(['categoria'])
@if($categoria)
<span class="badge rounded-pill"
      style="background-color: {{ \App\Models\Esercizio::catEtaColore($categoria) }}; font-size:.72rem; letter-spacing:.03em">
    {{ $categoria }}
</span>
@endif
