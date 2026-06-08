@extends('layouts.allenatore')
@section('title', __('Modifica Unità Didattica'))

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<h2 class="mb-4">{{ __('Modifica') }}: {{ $unitaDidattica->titolo }}</h2>

<form action="{{ route('allenatore.unita-didattiche.update', $unitaDidattica) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">

        <div class="col-12">
            <label class="form-label">{{ __('Titolo *') }}</label>
            <input type="text" name="titolo" class="form-control"
                   value="{{ old('titolo', $unitaDidattica->titolo) }}" required>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Obiettivo permanente') }} *</label>
            <textarea name="obiettivo_permanente" class="form-control" rows="3" required>{{ old('obiettivo_permanente', $unitaDidattica->obiettivo_permanente) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('Team *') }}</label>
            <select name="team_id" id="ud-team-id" class="form-select" required>
                @foreach($teams as $t)
                    <option value="{{ $t->id }}" {{ old('team_id', $unitaDidattica->team_id) == $t->id ? 'selected' : '' }}>{{ $t->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('Colore unità') }}</label>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <input type="color" name="colore" id="ud-colore" class="form-control form-control-color"
                       value="{{ old('colore', $unitaDidattica->colore ?? '#6366f1') }}"
                       style="width:2.6rem;height:2.2rem;padding:.15rem;cursor:pointer"
                       title="{{ __('Colore della barra nel calendario') }}">
                @foreach(['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316','#64748b'] as $c)
                <button type="button" class="swatch-btn rounded-circle border border-white"
                        data-color="{{ $c }}"
                        style="width:1.35rem;height:1.35rem;background:{{ $c }};cursor:pointer;box-shadow:0 0 0 1px rgba(0,0,0,.15);flex-shrink:0"></button>
                @endforeach
            </div>
        </div>

        {{-- Date + stagione calendar widget --}}
        <div class="col-12">
            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Data inizio') }}</label>
                    <input type="date" name="data_inizio" id="ud-data-inizio" class="form-control"
                           value="{{ old('data_inizio', $unitaDidattica->data_inizio?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Data fine') }}</label>
                    <input type="date" name="data_fine" id="ud-data-fine" class="form-control"
                           value="{{ old('data_fine', $unitaDidattica->data_fine?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <small class="text-muted" id="ud-cal-hint" style="font-size:.78rem">
                        {{ __('Clicca sul calendario per impostare inizio e fine') }}
                    </small>
                </div>
            </div>
            <div id="ud-stagione-cal" class="border rounded p-2 mt-1" style="background:#f8f9fa;display:none">
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <small id="ud-cal-stagione-nome" class="fw-semibold text-muted" style="font-size:.78rem"></small>
                    <div class="d-flex gap-1" id="ud-cal-legenda"></div>
                </div>
                <div id="ud-cal-grid" style="display:grid;gap:.5rem"></div>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Note') }}</label>
            <textarea name="note" class="form-control" rows="2">{{ old('note', $unitaDidattica->note) }}</textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ __('Salva modifiche') }}</button>
            <a href="{{ route('allenatore.unita-didattiche.show', $unitaDidattica) }}" class="btn btn-outline-secondary ms-2">{{ __('Annulla') }}</a>
        </div>

    </div>
</form>

<hr class="mt-4">
<form action="{{ route('allenatore.unita-didattiche.destroy', $unitaDidattica) }}" method="POST"
      data-confirm="{{ __('Eliminare questa unità? Le sedute collegate diventeranno indipendenti.') }}">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Elimina unità didattica') }}</button>
</form>
</div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const TEAMS_DATA   = @json($teamsData);
    const inpInizio    = document.getElementById('ud-data-inizio');
    const inpFine      = document.getElementById('ud-data-fine');
    const inpColore    = document.getElementById('ud-colore');
    const teamSel      = document.getElementById('ud-team-id');
    const calWrap      = document.getElementById('ud-stagione-cal');
    const calGrid      = document.getElementById('ud-cal-grid');
    const calNome      = document.getElementById('ud-cal-stagione-nome');
    const calLegenda   = document.getElementById('ud-cal-legenda');
    const calHint      = document.getElementById('ud-cal-hint');

    let nextClick = inpInizio.value ? 'fine' : 'inizio';

    const MESI_SHORT = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
    const GIORNI_MON = ['L','M','M','G','V','S','D'];

    function pad(n) { return String(n).padStart(2,'0'); }
    function dateKey(d) { return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`; }
    function getUdColor() { return inpColore.value || '#6366f1'; }

    function macroBgForKey(key, macrocicli) {
        for (const m of macrocicli) {
            if (key >= m.da && key <= m.a) {
                const r = parseInt(m.colore.slice(1,3),16);
                const g = parseInt(m.colore.slice(3,5),16);
                const b = parseInt(m.colore.slice(5,7),16);
                return { bg: `rgba(${r},${g},${b},0.18)`, border: m.colore };
            }
        }
        return null;
    }

    function hexAlpha(hex, alpha) {
        const r = parseInt(hex.slice(1,3),16);
        const g = parseInt(hex.slice(3,5),16);
        const b = parseInt(hex.slice(5,7),16);
        return `rgba(${r},${g},${b},${alpha})`;
    }

    function buildMonthMini(year, month, macrocicli, daKey, aKey, udColore) {
        const firstDay = new Date(year, month, 1);
        const lastDay  = new Date(year, month + 1, 0);
        const fd       = firstDay.getDay();
        let start = new Date(firstDay);
        start.setDate(start.getDate() - (fd === 0 ? 6 : fd - 1));

        let html = `<div style="background:#fff;border-radius:.35rem;box-shadow:0 1px 3px rgba(0,0,0,.07);overflow:hidden;min-width:140px">`;
        html += `<div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#495057;background:#f8f9fa;padding:.3rem .5rem;border-bottom:1px solid #e9ecef">${MESI_SHORT[month]} ${year}</div>`;
        html += `<div style="display:grid;grid-template-columns:repeat(7,1fr)">`;
        GIORNI_MON.forEach(g => {
            html += `<div style="text-align:center;font-size:.58rem;font-weight:700;color:#adb5bd;padding:.15rem 0;border-bottom:1px solid #f1f3f5">${g}</div>`;
        });

        let day = new Date(start);
        const today = dateKey(new Date());
        while (day <= lastDay || (day.getDay() !== 1)) {
            const key  = dateKey(day);
            const oth  = day.getMonth() !== month;
            const tod  = key === today;
            const inRange = daKey && aKey && key >= daKey && key <= aKey;
            const isStart = key === daKey;
            const isEnd   = key === aKey;
            const macro   = !oth ? macroBgForKey(key, macrocicli) : null;

            let cellBg = '#fff';
            let cellBorder = '';
            if (macro) { cellBg = macro.bg; cellBorder = `border-top:2px solid ${macro.border};`; }
            if (inRange) { cellBg = hexAlpha(udColore, 0.25); }
            if (isStart || isEnd) { cellBg = udColore; }

            const numColor = (isStart || isEnd) ? '#fff' : (oth ? '#ced4da' : tod ? '#3b82f6' : '#343a40');
            const numWeight = (tod || isStart || isEnd) ? '700' : '400';
            const cursor = oth ? 'default' : 'pointer';
            const dataKeyAttr = oth ? '' : `data-key="${key}"`;

            html += `<div class="ud-cal-cell" ${dataKeyAttr} style="min-height:26px;text-align:center;padding:.2rem .05rem;border-right:1px solid #f1f3f5;border-bottom:1px solid #f1f3f5;${cellBorder}background:${cellBg};cursor:${cursor}">`;
            html += `<span style="font-size:.65rem;color:${numColor};font-weight:${numWeight};display:block;line-height:1.4">${day.getDate()}</span>`;
            html += `</div>`;

            day.setDate(day.getDate() + 1);
            if (day > lastDay && day.getDay() === 1) break;
        }
        html += '</div></div>';
        return html;
    }

    function renderCal() {
        const tid  = teamSel.value;
        const td   = TEAMS_DATA[tid];
        if (!td || !td.stagione) { calWrap.style.display = 'none'; return; }

        calWrap.style.display = '';
        calNome.textContent = td.stagione.nome;

        const daKey = inpInizio.value || null;
        const aKey  = inpFine.value   || null;
        const udCol = getUdColor();

        const daDate = new Date(td.stagione.da);
        const aDate  = new Date(td.stagione.a);
        const months = [];
        let cur = new Date(daDate.getFullYear(), daDate.getMonth(), 1);
        while (cur <= aDate) {
            months.push([cur.getFullYear(), cur.getMonth()]);
            cur.setMonth(cur.getMonth() + 1);
        }

        const cols = Math.min(months.length, window.innerWidth < 768 ? 2 : 4);
        calGrid.style.gridTemplateColumns = `repeat(${cols}, minmax(140px, 1fr))`;
        calGrid.innerHTML = months.map(([y, m]) => buildMonthMini(y, m, td.macrocicli, daKey, aKey, udCol)).join('');

        calLegenda.innerHTML = td.macrocicli.map(m =>
            `<span style="display:flex;align-items:center;gap:.25rem;font-size:.7rem">
                <span style="width:.7rem;height:.7rem;border-radius:2px;background:${m.colore};display:inline-block"></span>
                ${m.nome}
            </span>`
        ).join('');

        updateHint();

        calGrid.querySelectorAll('.ud-cal-cell[data-key]').forEach(function(cell) {
            cell.addEventListener('click', function() {
                const key = this.dataset.key;
                if (!key) return;
                if (nextClick === 'inizio') {
                    inpInizio.value = key;
                    if (inpFine.value && inpFine.value < key) inpFine.value = '';
                    nextClick = 'fine';
                } else {
                    if (key < inpInizio.value) {
                        inpFine.value   = inpInizio.value;
                        inpInizio.value = key;
                    } else {
                        inpFine.value = key;
                    }
                    nextClick = 'inizio';
                }
                renderCal();
            });
        });
    }

    function updateHint() {
        if (nextClick === 'inizio') {
            calHint.textContent = '{{ __("Clicca per impostare data inizio") }}';
            inpInizio.style.boxShadow = '0 0 0 2px #6366f1';
            inpFine.style.boxShadow   = '';
        } else {
            calHint.textContent = '{{ __("Clicca per impostare data fine") }}';
            inpFine.style.boxShadow   = '0 0 0 2px #6366f1';
            inpInizio.style.boxShadow = '';
        }
    }

    teamSel.addEventListener('change', renderCal);
    inpInizio.addEventListener('change', function() { nextClick = 'fine'; renderCal(); });
    inpFine.addEventListener('change',   function() { nextClick = 'inizio'; renderCal(); });
    inpColore.addEventListener('input', renderCal);

    document.querySelectorAll('.swatch-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            inpColore.value = this.dataset.color;
            renderCal();
        });
    });

    inpInizio.addEventListener('focus', function() { nextClick = 'inizio'; updateHint(); });
    inpFine.addEventListener('focus',   function() { nextClick = 'fine';   updateHint(); });

    renderCal();
})();
</script>
@endpush
