@extends('layouts.atleta')
@section('title', 'Season calendar')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Season calendar</h3>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
        {{-- Prev / Title / Next / Oggi --}}
        <div class="d-flex align-items-center gap-2">
            <button id="btnPrev" class="btn btn-sm btn-outline-secondary px-2" style="line-height:1">‹</button>
            <span id="calTitle" class="fw-semibold text-center" style="min-width:9rem"></span>
            <button id="btnNext" class="btn btn-sm btn-outline-secondary px-2" style="line-height:1">›</button>
            <button id="btnToday" class="btn btn-sm btn-outline-secondary ms-1">Today</button>
        </div>

        {{-- Mobile: dropdown --}}
        <select id="viewSelect" class="form-select form-select-sm d-md-none" style="width:auto">
            <option value="month">Month</option>
            <option value="week">Week</option>
            <option value="season" {{ $stagioneDates ? '' : 'disabled' }}>Season</option>
        </select>

        {{-- Desktop: bottoni --}}
        <div class="btn-group btn-group-sm d-none d-md-inline-flex" role="group">
            <button id="btnMonth"  type="button" class="btn btn-primary">Month</button>
            <button id="btnWeek"   type="button" class="btn btn-outline-primary">Week</button>
            <button id="btnSeason" type="button" class="btn {{ $stagioneDates ? 'btn-outline-primary' : 'btn-outline-secondary' }}"
                    {{ $stagioneDates ? '' : 'disabled' }}>Season</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="calendar" style="min-height:320px"></div>
    </div>
</div>

{{-- Legenda --}}
<div class="d-flex flex-wrap gap-3 mb-2" style="font-size:.8rem">
    <span><span class="badge rounded-pill me-1" style="background:#f59e0b">●</span>Feedback to send</span>
    <span><span class="badge rounded-pill me-1" style="background:#10b981">●</span>Feedback sent</span>
    <span><span class="badge rounded-pill me-1" style="background:#e2e8f0;color:#64748b">●</span>Scheduled training</span>
</div>

@endsection

@push('styles')
<style>
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
.cal-cell.other-month  { background: #fafafa; }
.cal-cell.today-cell   { background: #eff6ff; }
.cal-cell.training-day { border-top: 2px solid #cbd5e1; }
.cal-day-num { font-size:.72rem; font-weight:600; color:#495057; line-height:1.6; }
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
.cal-week-cell.today-cell   { background: #eff6ff; }
.cal-week-cell.training-day { border-top: 2px solid #cbd5e1; }
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
/* Season: mini mesi */
.mini-year-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 1.25rem;
    background: #f8f9fa;
}
.mini-month-wrap { background:#fff; border-radius:.4rem; box-shadow:0 1px 3px rgba(0,0,0,.07); overflow:hidden; }
.mini-month-title { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#495057; padding:.35rem .5rem .2rem; background:#f8f9fa; border-bottom:1px solid #e9ecef; }
.cal-grid-mini { display:grid; grid-template-columns:repeat(7,1fr); }
.cal-cell-mini { padding:.15rem .1rem; border-right:1px solid #f1f3f5; border-bottom:1px solid #f1f3f5; min-height:26px; }
.cal-cell-mini.other-month { background:#fafafa; opacity:.5; }
.cal-cell-mini.today-cell  { background:#eff6ff; }
.mini-day-num { font-size:.6rem; font-weight:600; color:#6c757d; display:block; text-align:center; line-height:1.4; }
.mini-day-num.today-mini { background:#3b82f6; color:#fff; border-radius:50%; width:1.1rem; height:1.1rem; display:flex; align-items:center; justify-content:center; margin:0 auto; font-size:.55rem; }
.mini-dots { display:flex; flex-wrap:wrap; gap:1px; justify-content:center; padding:.05rem 0; }
.mini-dot { width:.55rem; height:.55rem; border-radius:50%; display:inline-block; text-decoration:none; flex-shrink:0; }

/* Mobile mese */
@media (max-width: 767.98px) {
    #calendar { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .cal-week-grid { min-width: 600px; }
    .cal-event { white-space:normal; overflow:visible; text-overflow:clip; line-height:1.25; }
    .mob-month-grid { display:grid; grid-template-columns:repeat(7,1fr); border-left:1px solid #e9ecef; border-top:1px solid #e9ecef; }
    .mob-month-head { text-align:center; font-size:.6rem; font-weight:700; text-transform:uppercase; color:#6c757d; background:#f8f9fa; padding:.3rem 0; border-right:1px solid #e9ecef; border-bottom:1px solid #e9ecef; }
    .mob-month-cell { display:flex; flex-direction:column; align-items:center; min-height:52px; padding:.25rem 0; text-decoration:none; color:#343a40; background:#fff; border-right:1px solid #e9ecef; border-bottom:1px solid #e9ecef; }
    .mob-month-cell.other-month { background:#fafafa; color:#adb5bd; }
    .mob-month-cell.today-cell  { background:#eff6ff; }
    .mob-month-cell.training-day { border-top:2px solid #cbd5e1; }
    .mob-day-num { font-size:.85rem; font-weight:600; line-height:1.4; }
    .mob-day-num.today-mini { background:#3b82f6; color:#fff; border-radius:50%; width:1.5rem; height:1.5rem; display:flex; align-items:center; justify-content:center; }
    .mob-dots { display:flex; flex-wrap:wrap; gap:2px; justify-content:center; margin-top:3px; }
    .mob-dot { width:.45rem; height:.45rem; border-radius:50%; }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const SEDUTE   = @json($sedutePerData);   // {date: [{titolo, url, feedback_inviato}]}
    const STAGIONE = @json($stagioneDates);   // {nome, da, a} | null
    const GIORNI   = @json($giorniSettimana); // [0..6] giorni settimana con allenamento

    const GIORNI_SHORT = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
    const GIORNI_MON   = ['Lun','Mar','Mer','Gio','Ven','Sab','Dom'];
    const MESI_LONG  = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
                        'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
    const MESI_SHORT = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];

    // Colori atleta
    const COL_FEEDBACK_SI  = '#10b981';  // verde: feedback inviato
    const COL_FEEDBACK_NO  = '#f59e0b';  // arancio: da inviare
    const COL_TRAINING     = '#cbd5e1';  // grigio: solo programmato

    let view     = STAGIONE ? 'season' : 'month';
    let curYear  = new Date().getFullYear();
    let curMonth = new Date().getMonth();
    let curWeek  = weekStart(new Date());

    const container = document.getElementById('calendar');
    const titleEl   = document.getElementById('calTitle');

    // ── Helpers ───────────────────────────────────────────────────────────────
    function pad(n) { return String(n).padStart(2,'0'); }
    function dateKey(d) { return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`; }
    function isToday(d) { return dateKey(d) === dateKey(new Date()); }
    function isTraining(d) { return GIORNI.includes(d.getDay()); }

    function weekStart(d) {
        const day = d.getDay();
        const diff = (day === 0 ? -6 : 1 - day);
        const m = new Date(d);
        m.setDate(m.getDate() + diff);
        m.setHours(0,0,0,0);
        return m;
    }

    function eventColor(evt) {
        return evt.feedback_inviato ? COL_FEEDBACK_SI : COL_FEEDBACK_NO;
    }

    function eventChip(evt) {
        const col = eventColor(evt);
        const label = evt.feedback_inviato ? '✓ ' + evt.titolo : evt.titolo;
        return `<a href="${evt.url}" class="cal-event" style="background:${col}">${label}</a>`;
    }

    function miniDot(evt) {
        const col = eventColor(evt);
        return `<a href="${evt.url}" class="mini-dot" style="background:${col}" title="${evt.titolo}"></a>`;
    }

    function dayCircle(dayNum, evts, tod, compact) {
        const sz  = compact ? '1.2rem' : '1.55rem';
        const fs  = compact ? '.6rem'  : '.75rem';
        if (tod) {
            const bg = evts.length ? eventColor(evts[0]) : '#3b82f6';
            return `<span style="display:inline-flex;align-items:center;justify-content:center;width:${sz};height:${sz};border-radius:50%;background:${bg};color:#fff;font-size:${fs};font-weight:700">${dayNum}</span>`;
        }
        if (!evts.length) return `<span style="font-size:${fs};color:#495057">${dayNum}</span>`;
        const col = eventColor(evts[0]);
        const tip = evts.map(e => e.titolo).join(', ');
        const bdg = evts.length > 1 ? `<span style="position:absolute;top:-2px;right:-3px;width:.8rem;height:.8rem;border-radius:50%;background:#1e293b;color:#fff;font-size:.46rem;font-weight:700;display:flex;align-items:center;justify-content:center">${evts.length}</span>` : '';
        return `<a href="${evts.length===1?evts[0].url:'#'}" style="position:relative;display:inline-flex;align-items:center;justify-content:center;width:${sz};height:${sz};border-radius:50%;background:${col};color:#fff;font-size:${fs};font-weight:700;text-decoration:none" title="${tip}">${dayNum}${bdg}</a>`;
    }

    // ── Render mese ──────────────────────────────────────────────────────────
    function renderMonth() {
        titleEl.textContent = `${MESI_LONG[curMonth]} ${curYear}`;
        const IS_MOB = window.matchMedia('(max-width:767.98px)').matches;
        container.innerHTML = IS_MOB
            ? buildMonthMobile(curYear, curMonth)
            : buildMonthGrid(curYear, curMonth, false);
    }

    function buildMonthMobile(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay  = new Date(year, month+1, 0);
        const fd = firstDay.getDay();
        let start = new Date(firstDay);
        start.setDate(start.getDate() - (fd===0?6:fd-1));
        let html = '<div class="mob-month-grid">';
        GIORNI_MON.forEach(g => html += `<div class="mob-month-head">${g}</div>`);
        let day = new Date(start);
        while (day <= lastDay || day.getDay() !== 1) {
            const key  = dateKey(day);
            const oth  = day.getMonth() !== month;
            const tod  = isToday(day);
            const evts = SEDUTE[key] || [];
            const tr   = !oth && isTraining(day) && !evts.length;
            html += `<div class="mob-month-cell ${oth?'other-month':''} ${tod?'today-cell':''} ${tr?'training-day':''}">`;
            if (!oth) {
                html += dayCircle(day.getDate(), evts, tod, true);
                if (tr) html += `<div class="mob-dots"><span class="mob-dot" style="background:${COL_TRAINING}"></span></div>`;
            } else {
                html += `<span class="mob-day-num" style="color:#adb5bd">${day.getDate()}</span>`;
            }
            html += '</div>';
            day.setDate(day.getDate()+1);
            if (day > lastDay && day.getDay()===1) break;
        }
        html += '</div>';
        return html;
    }

    // ── Render settimana ─────────────────────────────────────────────────────
    function renderWeek() {
        const monday = new Date(curWeek);
        const sunday = new Date(monday); sunday.setDate(sunday.getDate()+6);
        const fmt = d => `${pad(d.getDate())}/${pad(d.getMonth()+1)}`;
        titleEl.textContent = `${fmt(monday)} – ${fmt(sunday)} ${sunday.getFullYear()}`;
        let html = '<div class="cal-week-grid">';
        for (let i = 0; i < 7; i++) {
            const day  = new Date(monday); day.setDate(day.getDate()+i);
            const key  = dateKey(day);
            const evts = SEDUTE[key] || [];
            const tod  = isToday(day);
            const tr   = isTraining(day) && !evts.length;
            html += `<div class="cal-week-cell ${tod?'today-cell':''} ${tr?'training-day':''}">`;
            html += `<div class="week-day-header">${GIORNI_SHORT[day.getDay()]}</div>`;
            html += `<div class="week-day-num">${day.getDate()}</div>`;
            evts.forEach(e => html += eventChip(e));
            if (tr) html += `<div style="font-size:.65rem;color:#94a3b8;text-align:center;margin-top:.5rem">Scheduled</div>`;
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
        titleEl.innerHTML = `${STAGIONE.nome} <small class="text-muted ms-2" style="font-size:.75rem">${pad(da.getDate())}/${pad(da.getMonth()+1)}/${da.getFullYear()} – ${pad(a.getDate())}/${pad(a.getMonth()+1)}/${a.getFullYear()}</small>`;
        const months = [];
        let cur = new Date(da.getFullYear(), da.getMonth(), 1);
        while (cur <= a) { months.push([cur.getFullYear(), cur.getMonth()]); cur.setMonth(cur.getMonth()+1); }
        let html = '<div class="mini-year-grid p-3">';
        months.forEach(([y,m]) => {
            html += `<div class="mini-month-wrap"><div class="mini-month-title">${MESI_SHORT[m]} ${y}</div>`;
            html += buildMonthGrid(y, m, true);
            html += '</div>';
        });
        html += '</div>';
        container.innerHTML = html;
    }

    // ── Builder griglia mese (riusabile) ─────────────────────────────────────
    function buildMonthGrid(year, month, compact) {
        const firstDay = new Date(year, month, 1);
        const lastDay  = new Date(year, month+1, 0);
        const fd = firstDay.getDay();
        let start = new Date(firstDay);
        start.setDate(start.getDate() - (fd===0?6:fd-1));
        const gc = compact ? 'cal-grid cal-grid-mini' : 'cal-grid';
        let html = `<div class="${gc}">`;
        if (!compact) GIORNI_MON.forEach(g => html += `<div class="cal-header-cell">${g}</div>`);
        else          GIORNI_MON.forEach(g => html += `<div class="cal-header-cell" style="padding:.1rem;font-size:.55rem">${g}</div>`);
        let day = new Date(start);
        while (day <= lastDay || day.getDay() !== 1) {
            const key  = dateKey(day);
            const oth  = day.getMonth() !== month;
            const tod  = isToday(day);
            const evts = SEDUTE[key] || [];
            const tr   = !oth && isTraining(day) && !evts.length;
            if (compact) {
                html += `<div class="cal-cell-mini ${oth?'other-month':''} ${tod?'today-cell':''}">`;
                if (!oth) {
                    html += dayCircle(day.getDate(), evts, tod, true);
                    if (tr) html += `<div class="mini-dots"><span class="mini-dot" style="background:${COL_TRAINING};opacity:.6"></span></div>`;
                    else if (evts.length) {
                        html += `<div class="mini-dots">${evts.map(e=>miniDot(e)).join('')}</div>`;
                    }
                } else {
                    html += `<span class="mini-day-num" style="color:#ced4da">${day.getDate()}</span>`;
                }
                html += '</div>';
            } else {
                html += `<div class="cal-cell ${oth?'other-month':''} ${tod?'today-cell':''} ${tr?'training-day':''}">`;
                html += `<div class="cal-day-num">`;
                if (!oth) html += dayCircle(day.getDate(), evts, tod, false);
                else      html += `<span style="font-size:.72rem;color:#ced4da">${day.getDate()}</span>`;
                html += '</div>';
                evts.forEach(e => html += eventChip(e));
                if (tr) html += `<div style="font-size:.62rem;color:#94a3b8;margin-top:.2rem">Scheduled</div>`;
                html += '</div>';
            }
            day.setDate(day.getDate()+1);
            if (day > lastDay && day.getDay()===1) break;
        }
        html += '</div>';
        return html;
    }

    // ── Dispatcher ───────────────────────────────────────────────────────────
    function render() {
        if      (view==='month')  renderMonth();
        else if (view==='week')   renderWeek();
        else if (view==='season') renderSeason();
    }

    // ── Navigazione ──────────────────────────────────────────────────────────
    document.getElementById('btnPrev').onclick = () => {
        if (view==='month')     { curMonth--; if (curMonth<0) { curMonth=11; curYear--; } }
        else if (view==='week') { curWeek.setDate(curWeek.getDate()-7); }
        if (view!=='season') render();
    };
    document.getElementById('btnNext').onclick = () => {
        if (view==='month')     { curMonth++; if (curMonth>11) { curMonth=0; curYear++; } }
        else if (view==='week') { curWeek.setDate(curWeek.getDate()+7); }
        if (view!=='season') render();
    };
    document.getElementById('btnToday').onclick = () => {
        const now = new Date();
        curYear=now.getFullYear(); curMonth=now.getMonth(); curWeek=weekStart(now);
        if (view==='season') { view='month'; setActive('btnMonth'); }
        render();
    };

    function setActive(id) {
        ['btnMonth','btnWeek','btnSeason'].forEach(b => {
            const el = document.getElementById(b);
            if (el) el.className = (b===id) ? 'btn btn-primary' : 'btn btn-outline-primary';
        });
        const vs = document.getElementById('viewSelect');
        if (vs) vs.value = id.replace('btn','').toLowerCase();
    }

    document.getElementById('btnMonth').onclick  = () => { view='month';  setActive('btnMonth');  render(); };
    document.getElementById('btnWeek').onclick   = () => { view='week';   setActive('btnWeek');   render(); };
    const btnS = document.getElementById('btnSeason');
    if (btnS && !btnS.disabled) btnS.onclick = () => { view='season'; setActive('btnSeason'); render(); };

    const vs = document.getElementById('viewSelect');
    if (vs) vs.onchange = () => {
        view = vs.value;
        setActive('btn' + view.charAt(0).toUpperCase() + view.slice(1));
        render();
    };

    // ── Init ─────────────────────────────────────────────────────────────────
    setActive(STAGIONE ? 'btnSeason' : 'btnMonth');
    render();
})();
</script>
@endpush
