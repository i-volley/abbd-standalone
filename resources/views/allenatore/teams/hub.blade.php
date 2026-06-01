@extends('layouts.allenatore')
@section('title', $team->nome)

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="mb-0">{{ $team->nome }}</h2>
        <small class="text-muted">{{ $team->sport->nome }} · Stagione {{ $team->stagione }}</small>
    </div>
    <a href="{{ route('allenatore.teams.show', $team) }}" class="btn btn-sm btn-outline-secondary">
        Gestisci atleti
    </a>
</div>

{{-- ── ACCESSO RAPIDO ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <a href="{{ route('allenatore.stagioni.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 hover-lift">
                <div style="font-size:2rem">📅</div>
                <div class="fw-semibold mt-1">Pianificazione</div>
                <small class="text-muted">Stagioni · Macrocicli</small>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('allenatore.sedute.create') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 hover-lift">
                <div style="font-size:2rem">➕</div>
                <div class="fw-semibold mt-1">Nuova Seduta</div>
                <small class="text-muted">Aggiungi allenamento</small>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('allenatore.unita-didattiche.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 hover-lift">
                <div style="font-size:2rem">📚</div>
                <div class="fw-semibold mt-1">Unità Didattiche</div>
                <small class="text-muted">Obiettivi · Progressione</small>
            </div>
        </a>
    </div>
</div>

{{-- ── CALENDARIO ──────────────────────────────────────────────────────────── --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
        <div class="d-flex align-items-center gap-2">
            <button id="btnPrev" class="btn btn-sm btn-outline-secondary px-2" style="line-height:1">‹</button>
            <span id="calTitle" class="fw-semibold" style="min-width:12rem;text-align:center"></span>
            <button id="btnNext" class="btn btn-sm btn-outline-secondary px-2" style="line-height:1">›</button>
            <button id="btnToday" class="btn btn-sm btn-outline-secondary ms-1">Oggi</button>
        </div>
        <div class="btn-group btn-group-sm" role="group">
            <button id="btnMonth" type="button" class="btn btn-primary">Mese</button>
            <button id="btnWeek"  type="button" class="btn btn-outline-primary">Settimana</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="calendar" style="min-height:320px"></div>
    </div>
</div>

{{-- Legenda stati --}}
<div class="d-flex gap-3 mb-4" style="font-size:.8rem">
    <span><span class="badge rounded-pill me-1" style="background:#94a3b8">●</span>Bozza</span>
    <span><span class="badge rounded-pill me-1" style="background:#3b82f6">●</span>Pubblicata</span>
    <span><span class="badge rounded-pill me-1" style="background:#10b981">●</span>Completata</span>
</div>


{{-- ── PROSSIME SEDUTE ─────────────────────────────────────────────────────── --}}
@if($prossime->isNotEmpty())
<h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.72rem;letter-spacing:.08em">
    Prossime sedute
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
    Atleti ({{ $team->atleti->count() }})
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
</style>
@endpush

@push('scripts')
<script>
(function () {
    // ── Dati dal PHP ──────────────────────────────────────────────────────────
    const SEDUTE = @json($sedutePerData);
    const STATO_COLORE = {
        bozza:      '#94a3b8',
        pubblicata: '#3b82f6',
        completata: '#10b981',
    };
    const GIORNI_SHORT = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
    const MESI = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
                  'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];

    let view      = 'month';   // 'month' | 'week'
    let curYear   = new Date().getFullYear();
    let curMonth  = new Date().getMonth();  // 0-based
    let curWeek   = weekStart(new Date());  // Monday of current week

    const container = document.getElementById('calendar');
    const titleEl   = document.getElementById('calTitle');

    // ── Helpers ───────────────────────────────────────────────────────────────
    function pad(n) { return String(n).padStart(2,'0'); }
    function dateKey(d) { return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`; }
    function isToday(d) { return dateKey(d) === dateKey(new Date()); }

    function weekStart(d) {
        const day = d.getDay(); // 0=sun
        const diff = (day === 0 ? -6 : 1 - day); // Monday
        const m = new Date(d);
        m.setDate(m.getDate() + diff);
        m.setHours(0,0,0,0);
        return m;
    }

    function eventChip(s) {
        return `<a href="${s.url}" class="cal-event"
                   style="background:${STATO_COLORE[s.stato] || '#64748b'}"
                   title="${s.titolo} (${s.stato})">${s.titolo}</a>`;
    }

    // ── Render mese ──────────────────────────────────────────────────────────
    function renderMonth() {
        titleEl.textContent = `${MESI[curMonth]} ${curYear}`;

        const firstDay = new Date(curYear, curMonth, 1);
        const lastDay  = new Date(curYear, curMonth + 1, 0);

        // Lunedì della settimana del primo giorno
        let start = new Date(firstDay);
        const fd  = firstDay.getDay();
        start.setDate(start.getDate() - (fd === 0 ? 6 : fd - 1));

        let html = '<div class="cal-grid">';
        // Header giorni
        GIORNI_SHORT.forEach(g => html += `<div class="cal-header-cell">${g}</div>`);

        let day = new Date(start);
        while (day <= lastDay || day.getDay() !== 1) {
            const key  = dateKey(day);
            const oth  = day.getMonth() !== curMonth;
            const tod  = isToday(day);
            const evts = SEDUTE[key] || [];

            html += `<div class="cal-cell ${oth ? 'other-month' : ''} ${tod ? 'today-cell' : ''}">`;
            html += `<div class="cal-day-num">`;
            if (tod) {
                html += `<span class="today-badge">${day.getDate()}</span>`;
            } else {
                html += `<span>${day.getDate()}</span>`;
            }
            if (evts.length > 0) html += `<span style="font-size:.6rem;color:#94a3b8">${evts.length > 1 ? evts.length + '×' : ''}</span>`;
            html += '</div>';

            // Max 3 eventi visibili, poi "+N"
            const visible = evts.slice(0, 3);
            visible.forEach(s => html += eventChip(s));
            if (evts.length > 3) {
                html += `<span style="font-size:.65rem;color:#6c757d">+${evts.length - 3} altri</span>`;
            }

            html += '</div>';
            day.setDate(day.getDate() + 1);

            // Evita loop infinito se il mese finisce di domenica
            if (day > lastDay && day.getDay() === 1) break;
        }
        html += '</div>';
        container.innerHTML = html;
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
            const day  = new Date(monday);
            day.setDate(day.getDate() + i);
            const key  = dateKey(day);
            const evts = SEDUTE[key] || [];
            const tod  = isToday(day);

            html += `<div class="cal-week-cell ${tod ? 'today-cell' : ''}">`;
            html += `<div class="week-day-header">${GIORNI_SHORT[day.getDay()]}</div>`;
            html += `<div class="week-day-num">${day.getDate()}</div>`;
            evts.forEach(s => html += `
                <a href="${s.url}" class="cal-event d-block mb-1"
                   style="background:${STATO_COLORE[s.stato]||'#64748b'};white-space:normal;font-size:.72rem;line-height:1.3">
                    ${s.titolo}
                </a>`);
            html += '</div>';
        }
        html += '</div>';
        container.innerHTML = html;
    }

    function render() {
        if (view === 'month') renderMonth();
        else                  renderWeek();
    }

    // ── Navigazione ──────────────────────────────────────────────────────────
    document.getElementById('btnPrev').onclick = () => {
        if (view === 'month') {
            curMonth--; if (curMonth < 0) { curMonth = 11; curYear--; }
        } else {
            curWeek.setDate(curWeek.getDate() - 7);
        }
        render();
    };

    document.getElementById('btnNext').onclick = () => {
        if (view === 'month') {
            curMonth++; if (curMonth > 11) { curMonth = 0; curYear++; }
        } else {
            curWeek.setDate(curWeek.getDate() + 7);
        }
        render();
    };

    document.getElementById('btnToday').onclick = () => {
        const now = new Date();
        curYear  = now.getFullYear();
        curMonth = now.getMonth();
        curWeek  = weekStart(now);
        render();
    };

    document.getElementById('btnMonth').onclick = () => {
        view = 'month';
        document.getElementById('btnMonth').className = 'btn btn-primary';
        document.getElementById('btnWeek').className  = 'btn btn-outline-primary';
        render();
    };

    document.getElementById('btnWeek').onclick = () => {
        view = 'week';
        document.getElementById('btnWeek').className  = 'btn btn-primary';
        document.getElementById('btnMonth').className = 'btn btn-outline-primary';
        render();
    };

    // ── Init ─────────────────────────────────────────────────────────────────
    render();
})();
</script>
@endpush
