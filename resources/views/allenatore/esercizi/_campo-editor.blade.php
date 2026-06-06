{{--
    Partial: _campo-editor.blade.php
    Editor interattivo campo di pallavolo (SVG + JS puro, no dipendenze).
    Layout: campo a sinistra, textarea descrizione a destra.
    Variabile opzionale: $esercizio (solo in edit).
--}}

@php
    $campoVisivo    = '';
    $descrizioneVal = old('descrizione', isset($esercizio) ? ($esercizio->descrizione ?? '') : '');
    if (isset($esercizio) && $esercizio->campo_visivo) {
        $campoVisivo = json_encode($esercizio->campo_visivo);
    } elseif (old('campo_visivo')) {
        $campoVisivo = old('campo_visivo');
    }
@endphp

<div class="col-12 mt-1">
<div class="card border-0 bg-light p-3" style="border-radius:.5rem">

    {{-- Header + controlli --}}
    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
        <span class="fw-semibold">🏐 Campo di gioco</span>

        {{-- Layout toggle --}}
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="cv-layout btn btn-primary" data-layout="full">Campo intero</button>
            <button type="button" class="cv-layout btn btn-outline-primary" data-layout="half">Metà campo</button>
        </div>

        {{-- Strumenti --}}
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="cv-tool btn btn-secondary active" data-tool="move" title="Sposta elementi">✋ Sposta</button>
            <button type="button" class="cv-tool btn btn-outline-secondary" data-tool="arrow" title="Disegna freccia">↗ Freccia</button>
        </div>

        <button type="button" class="btn btn-sm btn-outline-danger" id="cv-clear">✕ Pulisci tutto</button>
    </div>

    {{-- Palette aggiungi elementi --}}
    <div class="d-flex align-items-center gap-1 mb-2 flex-wrap">
        <small class="text-muted me-1">Aggiungi:</small>
        <button type="button" class="cv-add btn btn-sm fw-bold" data-team="A"
                style="background:#f97316;color:#fff;border:none;min-width:2.2rem">A</button>
        <button type="button" class="cv-add btn btn-sm fw-bold" data-team="D"
                style="background:#3b82f6;color:#fff;border:none;min-width:2.2rem">D</button>
        <button type="button" class="cv-add btn btn-sm fw-bold" data-team="B"
                style="background:#fbbf24;color:#222;border:none;min-width:2.2rem">🏐</button>
        <small class="text-muted ms-1" style="font-size:.75rem">· tasto dx su elemento = elimina · Esc = annulla freccia</small>
    </div>

    {{-- Riga: campo SVG + descrizione --}}
    <div class="d-flex gap-3 align-items-stretch flex-wrap flex-md-nowrap">

        {{-- SVG court --}}
        <div style="flex:0 0 auto;width:100%;max-width:500px">
            <div id="cv-wrap" style="border-radius:.4rem;overflow:hidden;background:#f1f5f9;
                                      border:1px solid #cbd5e1;touch-action:none">
                <svg id="cv-svg" width="100%" xmlns="http://www.w3.org/2000/svg"
                     style="display:block;user-select:none;-webkit-user-select:none">
                    <defs>
                        <marker id="cv-arrowhead" markerWidth="9" markerHeight="7"
                                refX="8" refY="3.5" orient="auto">
                            <polygon points="0 0, 9 3.5, 0 7" fill="#dc2626"/>
                        </marker>
                        <marker id="cv-arrowhead-preview" markerWidth="9" markerHeight="7"
                                refX="8" refY="3.5" orient="auto">
                            <polygon points="0 0, 9 3.5, 0 7" fill="#dc262666"/>
                        </marker>
                    </defs>
                </svg>
            </div>
        </div>

        {{-- Descrizione esercizio --}}
        <div style="flex:1 1 200px;min-width:180px;display:flex;flex-direction:column">
            <label class="form-label fw-semibold mb-1">Descrizione / Note metodologiche</label>
            <textarea name="descrizione" class="form-control flex-grow-1"
                      style="min-height:180px;resize:vertical;font-size:.9rem"
                      placeholder="Descrivi l'esercizio: posizioni iniziali, compiti, varianti, punti chiave...">{{ $descrizioneVal }}</textarea>
            <small class="text-muted mt-1" style="font-size:.75rem">Varianti, progressioni, errori frequenti...</small>
        </div>

    </div>{{-- /riga --}}

    <input type="hidden" name="campo_visivo" id="cv-input" value="{{ $campoVisivo }}">
</div>
</div>

@push('scripts')
<script>
(function () {
'use strict';

var svg   = document.getElementById('cv-svg');
var input = document.getElementById('cv-input');
if (!svg || !input) return;

// ── Stato ─────────────────────────────────────────────────────────────────
var state  = { layout: 'full', players: [], arrows: [] };
var nextId = 1;
var tool   = 'move';
var dragging    = null;
var arrowStart  = null;
var previewLine = null;

// Carica stato salvato
(function load() {
    var raw = input.value;
    if (!raw) return;
    try {
        var s = JSON.parse(raw);
        if (s && s.layout) {
            state = s;
            state.players = state.players || [];
            state.arrows  = state.arrows  || [];
            state.players.forEach(function(p) {
                var n = parseInt(p.id.replace(/\D/g,''), 10);
                if (n >= nextId) nextId = n + 1;
            });
            state.arrows.forEach(function(a) {
                var n = parseInt(a.id.replace(/\D/g,''), 10);
                if (n >= nextId) nextId = n + 1;
            });
        }
    } catch(e) {}
})();

// ── Dimensioni campo ──────────────────────────────────────────────────────
function dims() {
    return state.layout === 'full' ? { w:540, h:270 } : { w:270, h:270 };
}

// ── Helpers SVG ───────────────────────────────────────────────────────────
var NS = 'http://www.w3.org/2000/svg';
function el(tag, attrs) {
    var e = document.createElementNS(NS, tag);
    for (var k in attrs) e.setAttribute(k, attrs[k]);
    return e;
}
function txt(tag, attrs, text) {
    var e = el(tag, attrs);
    e.textContent = text;
    return e;
}

function svgPt(evt) {
    var pt  = svg.createSVGPoint();
    var src = (evt.touches && evt.touches.length) ? evt.touches[0] : evt;
    pt.x = src.clientX;
    pt.y = src.clientY;
    return pt.matrixTransform(svg.getScreenCTM().inverse());
}

// ── Render ────────────────────────────────────────────────────────────────
function render() {
    var d = dims();
    svg.setAttribute('viewBox', '0 0 ' + d.w + ' ' + d.h);

    var toRemove = [];
    for (var i = 0; i < svg.children.length; i++) {
        if (svg.children[i].tagName !== 'defs') toRemove.push(svg.children[i]);
    }
    toRemove.forEach(function(c){ c.remove(); });

    // Sfondo bianco/chiaro
    svg.appendChild(el('rect', { x:0, y:0, width:d.w, height:d.h, fill:'#f1f5f9' }));

    if (state.layout === 'full') drawFullCourt(d);
    else drawHalfCourt(d);

    // Frecce (sotto i giocatori)
    state.arrows.forEach(function(a) {
        var g = el('g', { 'data-id': a.id, class: 'cv-arrow-grp' });
        g.appendChild(el('line', {
            x1: a.x1, y1: a.y1, x2: a.x2, y2: a.y2,
            stroke: '#dc2626', 'stroke-width': 2.5,
            'marker-end': 'url(#cv-arrowhead)', 'pointer-events': 'none'
        }));
        var hit = el('line', {
            x1: a.x1, y1: a.y1, x2: a.x2, y2: a.y2,
            stroke: 'transparent', 'stroke-width': 14, cursor: 'pointer'
        });
        hit.addEventListener('contextmenu', function(e) {
            e.preventDefault(); removeArrow(a.id);
        });
        g.appendChild(hit);
        svg.appendChild(g);
    });

    // Giocatori (in cima)
    state.players.forEach(renderPlayer);
}

function drawFullCourt(d) {
    // Superficie campo (leggermente più scura del bg)
    svg.appendChild(el('rect', { x:3, y:3, width:d.w-6, height:d.h-6,
        fill:'#e2e8f0', stroke:'#1e293b', 'stroke-width':2.5 }));
    // Rete
    svg.appendChild(el('rect', { x:267, y:0, width:6, height:d.h, fill:'#475569' }));
    svg.appendChild(el('circle', { cx:270, cy:3, r:5, fill:'#334155' }));
    svg.appendChild(el('circle', { cx:270, cy:d.h-3, r:5, fill:'#334155' }));
    // Linee d'attacco
    svg.appendChild(el('line', {
        x1:180, y1:3, x2:180, y2:d.h-3,
        stroke:'#64748b', 'stroke-width':1.5, 'stroke-dasharray':'10 5'
    }));
    svg.appendChild(el('line', {
        x1:360, y1:3, x2:360, y2:d.h-3,
        stroke:'#64748b', 'stroke-width':1.5, 'stroke-dasharray':'10 5'
    }));
    // Label zone
    svg.appendChild(txt('text', { x:135, y:16, 'text-anchor':'middle',
        fill:'rgba(0,0,0,.25)', 'font-size':11, 'pointer-events':'none' }, 'SQUADRA A'));
    svg.appendChild(txt('text', { x:405, y:16, 'text-anchor':'middle',
        fill:'rgba(0,0,0,.25)', 'font-size':11, 'pointer-events':'none' }, 'SQUADRA B'));
}

function drawHalfCourt(d) {
    svg.appendChild(el('rect', { x:3, y:3, width:d.w-6, height:d.h-6,
        fill:'#e2e8f0', stroke:'#1e293b', 'stroke-width':2.5 }));
    // Rete in alto
    svg.appendChild(el('rect', { x:0, y:0, width:d.w, height:5, fill:'#475569' }));
    svg.appendChild(el('circle', { cx:4, cy:2, r:4, fill:'#334155' }));
    svg.appendChild(el('circle', { cx:d.w-4, cy:2, r:4, fill:'#334155' }));
    // Linea d'attacco 3m = 90px
    svg.appendChild(el('line', {
        x1:3, y1:90, x2:d.w-3, y2:90,
        stroke:'#64748b', 'stroke-width':1.5, 'stroke-dasharray':'10 5'
    }));
    svg.appendChild(txt('text', { x:d.w/2, y:20, 'text-anchor':'middle',
        fill:'rgba(0,0,0,.25)', 'font-size':11, 'pointer-events':'none' }, 'RETE'));
    svg.appendChild(txt('text', { x:d.w/2, y:84, 'text-anchor':'middle',
        fill:'rgba(0,0,0,.2)', 'font-size':10, 'pointer-events':'none' }, '— 3 m —'));
}

function renderPlayer(p) {
    var colorMap = { A:'#f97316', D:'#3b82f6', B:'#fbbf24' };
    var color    = colorMap[p.team] || '#aaa';
    var r        = p.team === 'B' ? 10 : 13;
    var fontSize = p.team === 'B' ? 10 : 11;
    var txtColor = p.team === 'B' ? '#222' : '#fff';

    var g = el('g', { 'data-id': p.id, class: 'cv-player',
        cursor: tool === 'move' ? 'grab' : 'default' });
    g.appendChild(el('circle', {
        cx:p.x, cy:p.y, r:r,
        fill:color, stroke:'#fff', 'stroke-width':1.5
    }));
    g.appendChild(txt('text', {
        x:p.x, y:p.y,
        'text-anchor':'middle', 'dominant-baseline':'central',
        fill:txtColor, 'font-size':fontSize, 'font-weight':'bold',
        'font-family':'sans-serif', 'pointer-events':'none'
    }, p.label));
    svg.appendChild(g);

    g.addEventListener('mousedown', startDrag);
    g.addEventListener('contextmenu', function(e) {
        e.preventDefault(); removePlayer(p.id);
    });
}

// ── Drag ─────────────────────────────────────────────────────────────────
function startDrag(e) {
    if (tool !== 'move') return;
    e.preventDefault();
    e.stopPropagation();
    var gEl = e.currentTarget;
    var id  = gEl.getAttribute('data-id');
    var p   = state.players.find(function(x){ return x.id === id; });
    if (!p) return;
    var pt  = svgPt(e);
    dragging = { gEl: gEl, player: p, ox: pt.x - p.x, oy: pt.y - p.y };
    gEl.setAttribute('cursor', 'grabbing');
}

svg.addEventListener('mousemove', function(e) {
    if (!dragging) {
        if (arrowStart && previewLine) {
            var pt = svgPt(e);
            previewLine.setAttribute('x2', pt.x);
            previewLine.setAttribute('y2', pt.y);
        }
        return;
    }
    var d  = dims();
    var pt = svgPt(e);
    dragging.player.x = Math.max(14, Math.min(d.w-14, pt.x - dragging.ox));
    dragging.player.y = Math.max(14, Math.min(d.h-14, pt.y - dragging.oy));

    var circles = dragging.gEl.querySelectorAll('circle');
    var texts   = dragging.gEl.querySelectorAll('text');
    circles.forEach(function(c){
        c.setAttribute('cx', dragging.player.x);
        c.setAttribute('cy', dragging.player.y);
    });
    texts.forEach(function(t){
        t.setAttribute('x', dragging.player.x);
        t.setAttribute('y', dragging.player.y);
    });
});

svg.addEventListener('mouseup', function() {
    if (dragging) { dragging.gEl.setAttribute('cursor','grab'); dragging=null; save(); }
});
svg.addEventListener('mouseleave', function() {
    if (dragging) { dragging.gEl.setAttribute('cursor','grab'); dragging=null; save(); }
});

// ── Arrow tool ────────────────────────────────────────────────────────────
svg.addEventListener('click', function(e) {
    if (tool !== 'arrow') return;
    if (e.target.closest && e.target.closest('.cv-player')) return;

    var pt = svgPt(e);
    if (!arrowStart) {
        arrowStart = { x: pt.x, y: pt.y };
        previewLine = el('line', {
            x1:pt.x, y1:pt.y, x2:pt.x, y2:pt.y,
            stroke:'#dc262666', 'stroke-width':2, 'stroke-dasharray':'8 4',
            'pointer-events':'none', 'marker-end':'url(#cv-arrowhead-preview)'
        });
        svg.appendChild(previewLine);
    } else {
        if (previewLine) { previewLine.remove(); previewLine = null; }
        var dx = pt.x - arrowStart.x, dy = pt.y - arrowStart.y;
        if (Math.sqrt(dx*dx + dy*dy) > 8) {
            state.arrows.push({ id:'a'+(nextId++),
                x1:arrowStart.x, y1:arrowStart.y, x2:pt.x, y2:pt.y });
            save();
        }
        arrowStart = null;
        render();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && arrowStart) {
        if (previewLine) { previewLine.remove(); previewLine = null; }
        arrowStart = null;
    }
});

// ── Aggiungi / Rimuovi ────────────────────────────────────────────────────
function removePlayer(id) { state.players=state.players.filter(function(p){return p.id!==id;}); render();save(); }
function removeArrow(id)  { state.arrows=state.arrows.filter(function(a){return a.id!==id;});   render();save(); }

document.querySelectorAll('.cv-add').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var team = btn.dataset.team;
        var d    = dims();
        var cx, cy;
        if (team === 'B') {
            cx = d.w/2 + (Math.random()*30-15);
            cy = d.h/2 + (Math.random()*30-15);
            state.players.push({ id:'p'+(nextId++), label:'●', team:'B', x:cx, y:cy });
        } else {
            var count = state.players.filter(function(p){return p.team===team;}).length;
            if (count >= 6) return;
            var num = count + 1;
            cx = (state.layout==='full')
                ? ((team==='A') ? d.w*0.25 : d.w*0.75)
                : (d.w*0.25 + (team==='D' ? d.w*0.5 : 0));
            cy = d.h*0.5 + (Math.random()*60-30);
            state.players.push({ id:'p'+(nextId++), label:team+num, team:team, x:cx, y:cy });
        }
        render(); save();
    });
});

// ── Layout / Tool toggle ──────────────────────────────────────────────────
document.querySelectorAll('.cv-layout').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.cv-layout').forEach(function(b) {
            b.classList.toggle('btn-primary', b===btn);
            b.classList.toggle('btn-outline-primary', b!==btn);
            b.classList.toggle('active', b===btn);
        });
        state.layout = btn.dataset.layout;
        if (previewLine) { previewLine.remove(); previewLine=null; }
        arrowStart = null;
        render(); save();
    });
});

document.querySelectorAll('.cv-tool').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.cv-tool').forEach(function(b) {
            b.classList.toggle('btn-secondary', b===btn);
            b.classList.toggle('btn-outline-secondary', b!==btn);
            b.classList.toggle('active', b===btn);
        });
        tool = btn.dataset.tool;
        if (tool!=='arrow' && arrowStart) {
            if (previewLine) { previewLine.remove(); previewLine=null; }
            arrowStart = null;
        }
        document.querySelectorAll('.cv-player').forEach(function(g) {
            g.setAttribute('cursor', tool==='move' ? 'grab' : 'default');
        });
    });
});

document.getElementById('cv-clear').addEventListener('click', function() {
    state.players=[]; state.arrows=[];
    if (previewLine) { previewLine.remove(); previewLine=null; }
    arrowStart=null;
    render(); save();
});

// ── Serializza ────────────────────────────────────────────────────────────
function save() { input.value = JSON.stringify(state); }

// ── Init ──────────────────────────────────────────────────────────────────
document.querySelectorAll('.cv-layout').forEach(function(btn) {
    var active = btn.dataset.layout === state.layout;
    btn.classList.toggle('btn-primary', active);
    btn.classList.toggle('btn-outline-primary', !active);
    btn.classList.toggle('active', active);
});

render();
if (!input.value && (state.players.length || state.arrows.length)) save();

})();
</script>
@endpush
