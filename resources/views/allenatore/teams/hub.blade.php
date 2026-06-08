@extends('layouts.allenatore')
@section('title', $team->nome)

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="mb-0">{{ $team->nome }}</h2>
        <small class="text-muted">{{ $team->sport->nome }} · {{ __('Stagione') }} {{ $team->stagione }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('allenatore.teams.edit', $team) }}" class="btn btn-sm btn-outline-secondary">{{ __('Modifica') }}</a>
        <a href="{{ route('allenatore.teams.show', $team) }}" class="btn btn-sm btn-outline-secondary">{{ __('Gestisci atleti') }}</a>
    </div>
</div>

{{-- ── ACCESSO RAPIDO ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <a href="{{ route('allenatore.stagioni.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 hover-lift">
                <div style="font-size:2rem">📅</div>
                <div class="fw-semibold mt-1">{{ __('Pianificazione') }}</div>
                <small class="text-muted">{{ __('Stagioni') }} · {{ __('Macrocicli') }}</small>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('allenatore.sedute.create') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 hover-lift">
                <div style="font-size:2rem">➕</div>
                <div class="fw-semibold mt-1">{{ __('Nuova Seduta') }}</div>
                <small class="text-muted">{{ __('Aggiungi allenamento') }}</small>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('allenatore.unita-didattiche.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 hover-lift">
                <div style="font-size:2rem">📚</div>
                <div class="fw-semibold mt-1">{{ __('Unità Didattiche') }}</div>
                <small class="text-muted">{{ __('Obiettivi') }} · {{ __('Progressione') }}</small>
            </div>
        </a>
    </div>
</div>

{{-- ── CALENDARIO ──────────────────────────────────────────────────────────── --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
        <div class="d-flex align-items-center gap-2">
            <button id="btnPrev" class="btn btn-sm btn-outline-secondary px-2" style="line-height:1">‹</button>
            <span id="calTitle" class="fw-semibold text-center" style="min-width:9rem"></span>
            <button id="btnNext" class="btn btn-sm btn-outline-secondary px-2" style="line-height:1">›</button>
            <button id="btnToday" class="btn btn-sm btn-outline-secondary ms-1">{{ __('Oggi') }}</button>
        </div>

        {{-- Mobile (< md): dropdown — i bottoni sforano in larghezza --}}
        <select id="viewSelect" class="form-select form-select-sm d-md-none" style="width:auto">
            <option value="month" selected>{{ __('Mese') }}</option>
            <option value="week">{{ __('Settimana') }}</option>
            <option value="year">{{ __('Anno') }}</option>
            <option value="season" {{ $stagioneDates ? '' : 'disabled' }}>{{ __('Stagione') }}</option>
        </select>

        {{-- Tablet/Desktop (≥ md): gruppo bottoni --}}
        <div class="btn-group btn-group-sm d-none d-md-inline-flex" role="group">
            <button id="btnMonth"   type="button" class="btn btn-primary">{{ __('Mese') }}</button>
            <button id="btnWeek"    type="button" class="btn btn-outline-primary">{{ __('Settimana') }}</button>
            <button id="btnYear"    type="button" class="btn btn-outline-primary">{{ __('Anno') }}</button>
            <button id="btnSeason"  type="button" class="btn btn-outline-primary"
                    {{ $stagioneDates ? '' : 'disabled' }}
                    title="{{ $stagioneDates ? $stagioneDates['nome'] : __('Nessuna stagione') }}">
                {{ __('Stagione') }}
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="calendar" style="min-height:320px"></div>
    </div>
</div>

{{-- Legenda --}}
<div class="d-flex flex-wrap gap-3 mb-4" style="font-size:.8rem">
    @foreach($macrocicli as $m)
    <span class="d-flex align-items-center gap-1">
        <span class="rounded-pill d-inline-block"
              style="width:.9rem;height:.9rem;background:{{ $m['colore'] }}"></span>
        <span class="fw-semibold">{{ $m['nome'] }}</span>
        <span class="text-muted">{{ \Carbon\Carbon::parse($m['da'])->format('M') }}–{{ \Carbon\Carbon::parse($m['a'])->format('M Y') }}</span>
    </span>
    @endforeach
    @if($macrocicli->isNotEmpty())<span class="text-muted" style="font-size:.7rem">|</span>@endif
    <span><span class="badge rounded-pill me-1" style="background:#94a3b8">●</span>{{ __('Bozza') }}</span>
    <span><span class="badge rounded-pill me-1" style="background:#3b82f6">●</span>{{ __('Pubblicata') }}</span>
    <span><span class="badge rounded-pill me-1" style="background:#10b981">●</span>{{ __('Completata') }}</span>
</div>


{{-- ── PROSSIME SEDUTE ─────────────────────────────────────────────────────── --}}
@if($prossime->isNotEmpty())
<h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.72rem;letter-spacing:.08em">
    {{ __('Prossime sedute') }}
</h6>
@php $statoColore = ['bozza'=>'#94a3b8','pubblicata'=>'#3b82f6','completata'=>'#10b981']; @endphp
<div class="row g-2 mb-4">
    @foreach($prossime as $s)
    <div class="col-md-4">
        <a href="{{ route('allenatore.sedute.show', $s) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm" style="border-left:3px solid {{ $statoColore[$s->stato] ?? '#64748b' }} !important;border-left-style:solid !important">
                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="fw-semibold text-dark d-block">{{ $s->titolo }}</small>
                        <small class="text-muted">{{ $s->data->translatedFormat('D d/m') }}</small>
                    </div>
                    <span class="badge rounded-pill" style="background:{{ $statoColore[$s->stato] ?? '#64748b' }};font-size:.65rem">
                        {{ ucfirst($s->stato) }}
                    </span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endif

{{-- ── ATLETI ───────────────────────────────────────────────────────────────── --}}
@if($team->atleti->isNotEmpty())
<h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.72rem;letter-spacing:.08em">
    {{ __('Atleti') }} ({{ $team->atleti->count() }})
</h6>
<div class="d-flex flex-wrap gap-2">
    @foreach($team->atleti as $atleta)
    <span class="badge bg-light text-dark border" style="font-size:.8rem">{{ $atleta->name }}</span>
    @endforeach
</div>
@endif

@endsection

@push('styles')
<style>
.hover-lift { transition:.15s; cursor:pointer; }
.hover-lift:hover { box-shadow:0 .5rem 1.5rem rgba(0,0,0,.12) !important; transform:translateY(-2px); }

/* ── Calendario ──────────────────────────────────────── */
#calendar { font-size:.85rem; }

.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-left: 1px solid #e9ecef;
    border-top: 1px solid #e9ecef;
}

.cal-header-cell {
    padding: .4rem .5rem;
    text-align: center;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #6c757d;
    background: #f8f9fa;
    border-right: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
}

.cal-cell {
    min-height: 80px;
    padding: .3rem .4rem;
    vertical-align: top;
    border-right: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    background: #fff;
}

.cal-cell.other-month { background: #fafafa; }
.cal-cell.today-cell  { background: #eff6ff; }

.cal-day-num {
    font-size: .72rem;
    font-weight: 600;
    color: #495057;
    line-height: 1.6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.cal-day-num.today-num {
    color: #fff;
}
.cal-day-num .today-badge {
    background: #3b82f6;
    color: #fff;
    border-radius: 50%;
    width: 1.4rem;
    height: 1.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
}

.cal-event {
    display: block;
    font-size: .68rem;
    padding: .1rem .35rem;
    border-radius: .25rem;
    margin-bottom: .15rem;
    color: #fff;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
    font-weight: 500;
}
.cal-event:hover { filter: brightness(.9); color: #fff; }

/* Week view */
.cal-week-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-left: 1px solid #e9ecef;
    border-top: 1px solid #e9ecef;
}
.cal-week-cell {
    min-height: 140px;
    padding: .4rem;
    border-right: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    background: #fff;
}
.cal-week-cell.today-cell { background: #eff6ff; }
.cal-week-cell .week-day-header {
    text-align: center;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: .25rem;
}
.cal-week-cell .week-day-num {
    text-align: center;
    font-size: .9rem;
    font-weight: 600;
    margin-bottom: .4rem;
    color: #343a40;
}
.cal-week-cell.today-cell .week-day-num {
    background: #3b82f6;
    color: #fff;
    border-radius: 50%;
    width: 1.8rem;
    height: 1.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto .4rem;
}

/* ── Vista Anno / Stagione (mini mesi) ─────────────────── */
.mini-year-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 1.25rem;
    background: #f8f9fa;
}
.mini-month-wrap {
    background: #fff;
    border-radius: .4rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.07);
    overflow: hidden;
}
.mini-month-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #495057;
    padding: .35rem .5rem .2rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}
.cal-grid-mini {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}
.cal-cell-mini {
    padding: .15rem .1rem;
    border-right: 1px solid #f1f3f5;
    border-bottom: 1px solid #f1f3f5;
    min-height: 26px;
    position: relative;
}
.cal-cell-mini.other-month { background: #fafafa; opacity:.5; }
.cal-cell-mini.today-cell  { background: #eff6ff; }
.mini-day-num {
    font-size: .6rem;
    font-weight: 600;
    color: #6c757d;
    display: block;
    text-align: center;
    line-height: 1.4;
}
.mini-day-num.today-mini {
    background: #3b82f6;
    color: #fff;
    border-radius: 50%;
    width: 1.1rem;
    height: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: .55rem;
}
.mini-dots {
    display: flex;
    flex-wrap: wrap;
    gap: 1px;
    justify-content: center;
    padding: .05rem 0;
}
.mini-dot {
    width: .55rem;
    height: .55rem;
    border-radius: 50%;
    display: inline-block;
    text-decoration: none;
    flex-shrink: 0;
    transition: transform .1s;
}
.mini-dot:hover { transform: scale(1.4); }

/* Cella intera cliccabile (1 seduta) */
.cal-cell-mini--link {
    display: block;
    text-decoration: none;
    cursor: pointer;
}
.cal-cell-mini--link:hover {
    box-shadow: inset 0 0 0 2px #3b82f6;
    border-radius: .15rem;
}
/* Numero giorno con seduta (1 seduta): leggero underline colorato */
.mini-day-num--event {
    text-decoration: underline;
    text-decoration-color: #3b82f6;
    text-decoration-thickness: 2px;
    color: #1d4ed8;
}
.mini-dot-more {
    font-size: .5rem;
    color: #6c757d;
    line-height: 1.2;
    align-self: center;
}

/* ── Mobile: celle a larghezza fissa + scroll laterale del solo calendario ── */
@media (max-width: 767.98px) {
    /* Lo scroll orizzontale resta DENTRO il calendario: la pagina scorre fino in fondo */
    #calendar {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    /* Vista settimana: larghezza minima -> scorre lateralmente (mese/anno/stagione invece entrano a schermo) */
    .cal-week-grid {
        min-width: 600px;
    }
    /* Sedute ed eventi: vanno a capo e si adattano invece di troncare con i puntini */
    .cal-event {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        line-height: 1.25;
    }

    /* ── Vista MESE mobile: griglia fissa a schermo, pallini, tap → giorno ── */
    .mob-month-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border-left: 1px solid #e9ecef;
        border-top: 1px solid #e9ecef;
    }
    .mob-month-head {
        text-align: center;
        font-size: .6rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
        background: #f8f9fa;
        padding: .3rem 0;
        border-right: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
    }
    .mob-month-cell {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 52px;
        padding: .25rem 0;
        text-decoration: none;
        color: #343a40;
        background: #fff;
        border-right: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
    }
    .mob-month-cell.other-month { background: #fafafa; color: #adb5bd; }
    .mob-month-cell.today-cell  { background: #eff6ff; }
    .mob-day-num { font-size: .85rem; font-weight: 600; line-height: 1.4; }
    .mob-day-num.today-mini {
        background: #3b82f6;
        color: #fff;
        border-radius: 50%;
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mob-dots {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        justify-content: center;
        margin-top: 3px;
    }
    .mob-dot {
        width: .45rem;
        height: .45rem;
        border-radius: 50%;
    }
    .mob-dot-more { font-size: .55rem; color: #6c757d; line-height: 1; }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    // ── Dati dal PHP ──────────────────────────────────────────────────────────
    const SEDUTE     = @json($sedutePerData);
    const MACROCICLI = @json($macrocicli);
    const STAGIONE   = @json($stagioneDates);   // {nome, da, a, url} | null
    const DAY_URL    = @json(route('allenatore.teams.giorno', ['team' => $team->id, 'data' => '__DATA__']));
    const IS_MOBILE  = () => window.matchMedia('(max-width: 767.98px)').matches;
    function dayUrl(key) { return DAY_URL.replace('__DATA__', key); }
    const STATO_COLORE = { bozza:'#94a3b8', pubblicata:'#3b82f6', completata:'#10b981' };
    const GIORNI_SHORT = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
    // Header griglia mese: parte da Lunedì (la griglia parte sempre da Lun)
    const GIORNI_MON   = ['Lun','Mar','Mer','Gio','Ven','Sab','Dom'];
    const MESI_LONG  = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
                        'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
    const MESI_SHORT = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];

    let view     = 'month';
    let curYear  = new Date().getFullYear();
    let curMonth = new Date().getMonth();
    let curWeek  = weekStart(new Date());

    const container = document.getElementById('calendar');
    const titleEl   = document.getElementById('calTitle');

    // ── Helpers ───────────────────────────────────────────────────────────────
    function pad(n) { return String(n).padStart(2,'0'); }
    function dateKey(d) { return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`; }
    function isToday(d) { return dateKey(d) === dateKey(new Date()); }

    function weekStart(d) {
        const day = d.getDay();
        const diff = (day === 0 ? -6 : 1 - day);
        const m = new Date(d);
        m.setDate(m.getDate() + diff);
        m.setHours(0,0,0,0);
        return m;
    }

    function eventChip(s, compact) {
        if (compact) {
            // Vista anno/stagione: solo pallino colorato
            return `<a href="${s.url}" class="mini-dot"
                       style="background:${STATO_COLORE[s.stato]||'#64748b'}"
                       title="${s.titolo}"></a>`;
        }
        return `<a href="${s.url}" class="cal-event"
                   style="background:${STATO_COLORE[s.stato]||'#64748b'}"
                   title="${s.titolo} (${s.stato})">${s.titolo}</a>`;
    }

    function macroBgColor(key) {
        for (const m of MACROCICLI) {
            if (key >= m.da && key <= m.a) {
                const r = parseInt(m.colore.slice(1,3),16);
                const g = parseInt(m.colore.slice(3,5),16);
                const b = parseInt(m.colore.slice(5,7),16);
                return `rgba(${r},${g},${b},0.13)`;
            }
        }
        return null;
    }

    function macroBorderColor(key) {
        for (const m of MACROCICLI) {
            if (key >= m.da && key <= m.a) return m.colore;
        }
        return null;
    }

    // ── Render mese (pieno) ──────────────────────────────────────────────────
    function renderMonth() {
        titleEl.textContent = `${MESI_LONG[curMonth]} ${curYear}`;
        // Mobile: griglia compatta a larghezza schermo, pallini colorati, tap → pagina giorno
        container.innerHTML = IS_MOBILE()
            ? buildMonthGridMobile(curYear, curMonth)
            : buildMonthGrid(curYear, curMonth, false);
    }

    // ── Griglia mese MOBILE: celle fisse a schermo, pallini eventi, tap → giorno ──
    function buildMonthGridMobile(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay  = new Date(year, month + 1, 0);
        const fd       = firstDay.getDay();
        let start = new Date(firstDay);
        start.setDate(start.getDate() - (fd === 0 ? 6 : fd - 1));

        let html = '<div class="mob-month-grid">';
        GIORNI_MON.forEach(g => html += `<div class="mob-month-head">${g}</div>`);

        let day = new Date(start);
        while (day <= lastDay || day.getDay() !== 1) {
            const key   = dateKey(day);
            const oth   = day.getMonth() !== month;
            const tod   = isToday(day);
            const evts  = SEDUTE[key] || [];
            const bgCol = !oth ? macroBgColor(key) : null;
            const brCol = !oth ? macroBorderColor(key) : null;
            const sty   = bgCol ? `background:${bgCol};${brCol ? `border-top:3px solid ${brCol};` : ''}` : '';

            html += `<a href="${dayUrl(key)}" class="mob-month-cell ${oth ? 'other-month' : ''} ${tod ? 'today-cell' : ''}" style="${sty}">`;
            html += `<span class="mob-day-num ${tod ? 'today-mini' : ''}">${day.getDate()}</span>`;
            if (evts.length) {
                html += '<span class="mob-dots">';
                evts.slice(0, 4).forEach(s =>
                    html += `<span class="mob-dot" style="background:${STATO_COLORE[s.stato] || '#64748b'}" title="${s.titolo}"></span>`);
                if (evts.length > 4) html += `<span class="mob-dot-more">+${evts.length - 4}</span>`;
                html += '</span>';
            }
            html += '</a>';

            day.setDate(day.getDate() + 1);
            if (day > lastDay && day.getDay() === 1) break;
        }
        html += '</div>';
        return html;
    }

    // ── Render settimana ─────────────────────────────────────────────────────
    function renderWeek() {
        const monday = new Date(curWeek);
        const sunday = new Date(monday);
        sunday.setDate(sunday.getDate() + 6);
        const fmt = d => `${pad(d.getDate())}/${pad(d.getMonth()+1)}`;
        titleEl.textContent = `${fmt(monday)} – ${fmt(sunday)} ${sunday.getFullYear()}`;

        let html = '<div class="cal-week-grid">';
        for (let i = 0; i < 7; i++) {
            const day   = new Date(monday);
            day.setDate(day.getDate() + i);
            const key   = dateKey(day);
            const evts  = SEDUTE[key] || [];
            const tod   = isToday(day);
            const bgCol = macroBgColor(key);
            const brCol = macroBorderColor(key);
            const sty   = bgCol ? `style="background:${bgCol};${brCol?`border-top:3px solid ${brCol};`:''}"` : '';

            html += `<div class="cal-week-cell ${tod?'today-cell':''}" ${sty}>`;
            html += `<div class="week-day-header">${GIORNI_SHORT[day.getDay()]}</div>`;
            html += `<div class="week-day-num">${day.getDate()}</div>`;
            evts.forEach(s => html += `<a href="${s.url}" class="cal-event d-block mb-1"
                style="background:${STATO_COLORE[s.stato]||'#64748b'};white-space:normal;font-size:.72rem;line-height:1.3">${s.titolo}</a>`);
            html += '</div>';
        }
        html += '</div>';
        container.innerHTML = html;
    }

    // ── Render anno ──────────────────────────────────────────────────────────
    function renderYear() {
        titleEl.textContent = `Anno ${curYear}`;
        let html = '<div class="mini-year-grid p-3">';
        for (let m = 0; m < 12; m++) {
            html += `<div class="mini-month-wrap">`;
            html += `<div class="mini-month-title">${MESI_SHORT[m]}</div>`;
            html += buildMonthGrid(curYear, m, true);
            html += '</div>';
        }
        html += '</div>';
        container.innerHTML = html;
    }

    // ── Render stagione ──────────────────────────────────────────────────────
    function renderSeason() {
        if (!STAGIONE) return;
        const da = new Date(STAGIONE.da);
        const a  = new Date(STAGIONE.a);
        titleEl.innerHTML = `<a href="${STAGIONE.url}" class="text-decoration-none">${STAGIONE.nome}</a>
            <small class="text-muted ms-2" style="font-size:.75rem">
              ${pad(da.getDate())}/${pad(da.getMonth()+1)}/${da.getFullYear()} –
              ${pad(a.getDate())}/${pad(a.getMonth()+1)}/${a.getFullYear()}
            </small>`;

        // Mesi che compongono la stagione
        const months = [];
        let cur = new Date(da.getFullYear(), da.getMonth(), 1);
        while (cur <= a) {
            months.push([cur.getFullYear(), cur.getMonth()]);
            cur.setMonth(cur.getMonth() + 1);
        }

        let html = '<div class="mini-year-grid p-3">';
        months.forEach(([y, m]) => {
            html += `<div class="mini-month-wrap">`;
            html += `<div class="mini-month-title">${MESI_SHORT[m]} ${y}</div>`;
            html += buildMonthGrid(y, m, true);
            html += '</div>';
        });
        html += '</div>';
        container.innerHTML = html;
    }

    // ── Builder griglia mese (riusabile in tutte le viste) ───────────────────
    // compact=true → celle piccole con solo pallini, compact=false → celle piene con chip
    function buildMonthGrid(year, month, compact) {
        const firstDay = new Date(year, month, 1);
        const lastDay  = new Date(year, month + 1, 0);
        const fd       = firstDay.getDay();

        let start = new Date(firstDay);
        start.setDate(start.getDate() - (fd === 0 ? 6 : fd - 1));

        const gridClass = compact ? 'cal-grid cal-grid-mini' : 'cal-grid';
        let html = `<div class="${gridClass}">`;

        if (!compact) {
            GIORNI_MON.forEach(g => html += `<div class="cal-header-cell">${g}</div>`);
        } else {
            GIORNI_MON.forEach(g => html += `<div class="cal-header-cell" style="padding:.1rem;font-size:.55rem">${g}</div>`);
        }

        let day = new Date(start);
        while (day <= lastDay || day.getDay() !== 1) {
            const key   = dateKey(day);
            const oth   = day.getMonth() !== month;
            const tod   = isToday(day);
            const evts  = SEDUTE[key] || [];
            const bgCol = !oth ? macroBgColor(key) : null;
            const brCol = !oth ? macroBorderColor(key) : null;

            const sty = bgCol
                ? `style="background:${bgCol};${brCol?`border-top:${compact?'2':'2'}px solid ${brCol};`:''}"` : '';

            if (compact) {
                if (IS_MOBILE()) {
                    // Mobile (anno/stagione): cella intera = link alla pagina GIORNO,
                    // pallini per ogni evento (stessa logica della vista mese mobile)
                    const numCls = evts.length
                        ? `mini-day-num ${tod?'today-mini':''} mini-day-num--event`
                        : `mini-day-num ${tod?'today-mini':''}`;
                    html += `<a href="${dayUrl(key)}"
                                class="cal-cell-mini cal-cell-mini--link ${oth?'other-month':''} ${tod?'today-cell':''}"
                                ${sty}>`;
                    html += `<span class="${numCls}">${day.getDate()}</span>`;
                    if (evts.length) {
                        html += '<div class="mini-dots">';
                        evts.slice(0, 4).forEach(s =>
                            html += `<span class="mini-dot" style="background:${STATO_COLORE[s.stato]||'#64748b'}" title="${s.titolo}"></span>`);
                        if (evts.length > 4) html += `<span class="mini-dot-more">+${evts.length-4}</span>`;
                        html += '</div>';
                    }
                    html += '</a>';
                } else {
                    // Desktop anno/stagione: comportamento esistente
                    if (evts.length === 1) {
                        // Cella intera = link alla seduta
                        html += `<a href="${evts[0].url}"
                                    class="cal-cell-mini cal-cell-mini--link ${oth?'other-month':''} ${tod?'today-cell':''}"
                                    ${sty}
                                    title="${evts[0].titolo}">`;
                    } else {
                        html += `<div class="cal-cell-mini ${oth?'other-month':''} ${tod?'today-cell':''}" ${sty}>`;
                    }
                    const numCls = evts.length === 1
                        ? `mini-day-num ${tod?'today-mini':''} mini-day-num--event`
                        : `mini-day-num ${tod?'today-mini':''}`;
                    html += `<span class="${numCls}">${day.getDate()}</span>`;
                    if (evts.length > 1) {
                        // Più sedute: pallini cliccabili con tooltip
                        html += '<div class="mini-dots">';
                        evts.slice(0, 4).forEach(s => html += eventChip(s, true));
                        if (evts.length > 4) html += `<span class="mini-dot-more">+${evts.length-4}</span>`;
                        html += '</div>';
                    }
                    html += evts.length === 1 ? '</a>' : '</div>';
                }
            } else {
                html += `<div class="cal-cell ${oth?'other-month':''} ${tod?'today-cell':''}" ${sty}>`;
                html += '<div class="cal-day-num">';
                if (tod) html += `<span class="today-badge">${day.getDate()}</span>`;
                else     html += `<span>${day.getDate()}</span>`;
                if (evts.length > 1) html += `<span style="font-size:.6rem;color:#94a3b8">${evts.length}×</span>`;
                html += '</div>';
                evts.slice(0, 3).forEach(s => html += eventChip(s, false));
                if (evts.length > 3) html += `<span style="font-size:.65rem;color:#6c757d">+${evts.length-3} altri</span>`;
                html += '</div>';
            }

            day.setDate(day.getDate() + 1);
            if (day > lastDay && day.getDay() === 1) break;
        }
        html += '</div>';
        return html;
    }

    // ── Render dispatcher ─────────────────────────────────────────────────────
    function render() {
        if      (view === 'month')  renderMonth();
        else if (view === 'week')   renderWeek();
        else if (view === 'year')   renderYear();
        else if (view === 'season') renderSeason();
    }

    // ── Navigazione ──────────────────────────────────────────────────────────
    document.getElementById('btnPrev').onclick = () => {
        if (view === 'month')       { curMonth--; if (curMonth < 0) { curMonth = 11; curYear--; } }
        else if (view === 'week')   { curWeek.setDate(curWeek.getDate() - 7); }
        else if (view === 'year')   { curYear--; }
        // season: nav non ha senso
        if (view !== 'season') render();
    };

    document.getElementById('btnNext').onclick = () => {
        if (view === 'month')       { curMonth++; if (curMonth > 11) { curMonth = 0; curYear++; } }
        else if (view === 'week')   { curWeek.setDate(curWeek.getDate() + 7); }
        else if (view === 'year')   { curYear++; }
        if (view !== 'season') render();
    };

    document.getElementById('btnToday').onclick = () => {
        const now = new Date();
        curYear  = now.getFullYear();
        curMonth = now.getMonth();
        curWeek  = weekStart(now);
        if (view === 'season') { view = 'month'; setActive('btnMonth'); }
        render();
    };

    function setActive(id) {
        ['btnMonth','btnWeek','btnYear','btnSeason'].forEach(b => {
            const el = document.getElementById(b);
            if (el) el.className = (b === id) ? 'btn btn-primary' : 'btn btn-outline-primary';
        });
        // Sync dropdown mobile (btnMonth -> "month", ecc.)
        const vs = document.getElementById('viewSelect');
        if (vs) vs.value = id.replace('btn', '').toLowerCase();
    }

    document.getElementById('btnMonth').onclick  = () => { view = 'month';  setActive('btnMonth');  render(); };
    document.getElementById('btnWeek').onclick   = () => { view = 'week';   setActive('btnWeek');   render(); };
    document.getElementById('btnYear').onclick   = () => { view = 'year';   setActive('btnYear');   render(); };
    const btnSeason = document.getElementById('btnSeason');
    if (btnSeason && !btnSeason.disabled) {
        btnSeason.onclick = () => { view = 'season'; setActive('btnSeason'); render(); };
    }

    // Dropdown mobile: stessa logica dei bottoni
    const viewSelect = document.getElementById('viewSelect');
    if (viewSelect) {
        viewSelect.onchange = () => {
            view = viewSelect.value; // month | week | year | season
            setActive('btn' + view.charAt(0).toUpperCase() + view.slice(1));
            render();
        };
    }

    // ── Init ─────────────────────────────────────────────────────────────────
    render();
})();
</script>
@endpush
