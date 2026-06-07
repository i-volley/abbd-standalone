{{--
    Partial: _campo-preview.blade.php
    Render read-only SVG del campo visivo salvato.
    Variabili richieste:
      $campoPreview  — array (dal model, già castato) oppure null
      $previewKey    — stringa unica per l'ID SVG (es. $esercizio->id)
      $previewHeight — opzionale, altezza max contenitore (default: auto)
--}}
@php
    if (empty($campoPreview)) return;
    $pid      = 'cvp-' . ($previewKey ?? uniqid());
    $jsonData = json_encode($campoPreview, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
@endphp

<div class="cv-preview-container" style="background:#f1f5f9;border:1px solid #cbd5e1;border-radius:.4rem;overflow:hidden">
    <svg id="{{ $pid }}" width="100%" xmlns="http://www.w3.org/2000/svg" style="display:block"></svg>
</div>

<script>
(function () {
    var svg = document.getElementById('{{ $pid }}');
    if (!svg) return;
    var state;
    try { state = {!! $jsonData !!}; } catch(e) { return; }
    if (!state || !state.layout) return;

    var MARGIN = 48;
    var NS = 'http://www.w3.org/2000/svg';

    function el(tag, attrs) {
        var e = document.createElementNS(NS, tag);
        for (var k in attrs) e.setAttribute(k, attrs[k]);
        return e;
    }
    function tx(tag, attrs, text) { var e = el(tag, attrs); e.textContent = text; return e; }

    function dims() {
        var cw = state.layout === 'full' ? 540 : 270;
        return { w: cw + MARGIN*2, h: 270 + MARGIN*2, cx: MARGIN, cy: MARGIN, cw: cw, ch: 270 };
    }

    function render() {
        var d = dims();
        svg.setAttribute('viewBox', '0 0 ' + d.w + ' ' + d.h);

        // Sfondo zona libera
        svg.appendChild(el('rect', {x:0, y:0, width:d.w, height:d.h, fill:'#cbd5e1'}));

        // Griglia leggera zona libera
        var step = 24;
        for (var x = 0; x <= d.w; x += step)
            svg.appendChild(el('line', {x1:x, y1:0, x2:x, y2:d.h, stroke:'rgba(0,0,0,.07)', 'stroke-width':.8}));
        for (var y = 0; y <= d.h; y += step)
            svg.appendChild(el('line', {x1:0, y1:y, x2:d.w, y2:y, stroke:'rgba(0,0,0,.07)', 'stroke-width':.8}));

        // Superficie campo
        svg.appendChild(el('rect', {x:d.cx, y:d.cy, width:d.cw, height:d.ch, fill:'#e8ecf0'}));

        if (state.layout === 'full') drawFull(d); else drawHalf(d);

        // Frecce
        (state.arrows || []).forEach(function(a) {
            var isBall = a.color === 'ball', isBlue = a.color === 'blue';
            var color  = isBall ? '#ca8a04' : (isBlue ? '#2563eb' : '#dc2626');
            var mid    = 'cvp-ah-' + (isBall ? 'ball' : (isBlue ? 'blue' : 'red')) + '-{{ $pid }}';
            var line   = el('line', {x1:a.x1, y1:a.y1, x2:a.x2, y2:a.y2,
                stroke:color, 'stroke-width':2.5, 'marker-end':'url(#'+mid+')'});
            if (isBlue || isBall) line.setAttribute('stroke-dasharray','8 4');
            svg.appendChild(line);

            if (isBall && a.num != null) {
                var mx = (a.x1+a.x2)/2, my = (a.y1+a.y2)/2;
                var dx = a.x2-a.x1, dy = a.y2-a.y1;
                var len = Math.sqrt(dx*dx+dy*dy)||1;
                var ox = (-dy/len)*13, oy = (dx/len)*13;
                svg.appendChild(el('circle', {cx:mx+ox, cy:my+oy, r:9,
                    fill:'#fef08a', stroke:'#ca8a04', 'stroke-width':1.5}));
                svg.appendChild(tx('text', {x:mx+ox, y:my+oy,
                    'text-anchor':'middle', 'dominant-baseline':'central',
                    fill:'#92400e', 'font-size':10, 'font-weight':'bold', 'font-family':'sans-serif'
                }, String(a.num)));
            }
        });

        // Giocatori
        (state.players || []).forEach(function(p) {
            renderPlayer(p, d);
        });
    }

    function drawFull(d) {
        var x=d.cx, y=d.cy, w=d.cw, h=d.ch;
        svg.appendChild(el('rect', {x:x,y:y,width:w,height:h, fill:'none', stroke:'#1e293b','stroke-width':2.5}));
        svg.appendChild(el('rect', {x:x+w/2-3,y:y,width:6,height:h, fill:'#475569'}));
        svg.appendChild(el('circle', {cx:x+w/2,cy:y-2,r:5,fill:'#334155'}));
        svg.appendChild(el('circle', {cx:x+w/2,cy:y+h+2,r:5,fill:'#334155'}));
        svg.appendChild(el('line', {x1:x+180,y1:y,x2:x+180,y2:y+h, stroke:'#64748b','stroke-width':1.5,'stroke-dasharray':'10 5'}));
        svg.appendChild(el('line', {x1:x+360,y1:y,x2:x+360,y2:y+h, stroke:'#64748b','stroke-width':1.5,'stroke-dasharray':'10 5'}));
    }

    function drawHalf(d) {
        var x=d.cx, y=d.cy, w=d.cw, h=d.ch;
        svg.appendChild(el('rect', {x:x,y:y,width:w,height:h, fill:'none', stroke:'#1e293b','stroke-width':2.5}));
        svg.appendChild(el('rect', {x:x,y:y,width:w,height:5, fill:'#475569'}));
        svg.appendChild(el('circle', {cx:x,cy:y+2,r:5,fill:'#334155'}));
        svg.appendChild(el('circle', {cx:x+w,cy:y+2,r:5,fill:'#334155'}));
        svg.appendChild(el('line', {x1:x,y1:y+90,x2:x+w,y2:y+90, stroke:'#64748b','stroke-width':1.5,'stroke-dasharray':'10 5'}));
    }

    function renderPlayer(p, d) {
        var isR1 = p.team==='R1', isR2 = p.team==='R2';
        var color, stroke, sw, txtCol, r;
        if (isR1 || isR2) {
            r = 14;
            var isL = p.role==='L';
            if (isR1) { color=isL?'#dc2626':'#7c3aed'; stroke='#fff'; sw=1.5; txtCol='#fff'; }
            else      { color='#fff'; stroke=isL?'#dc2626':'#1e293b'; sw=2.5; txtCol=isL?'#dc2626':'#1e293b'; }
        } else {
            var cm = {A:'#f97316',D:'#3b82f6',B:'#fbbf24',C:'#1e293b',X:'#6b7280'};
            color=cm[p.team]||'#aaa'; stroke='#fff'; sw=1.5; txtCol=p.team==='B'?'#222':'#fff';
            r = (p.team==='B'||p.team==='X') ? 10 : 13;
        }
        if (p.team==='X') {
            svg.appendChild(el('rect', {x:p.x-r,y:p.y-r,width:r*2,height:r*2, fill:color,stroke:stroke,'stroke-width':sw,rx:3}));
        } else {
            svg.appendChild(el('circle', {cx:p.x,cy:p.y,r:r, fill:color,stroke:stroke,'stroke-width':sw}));
        }
        svg.appendChild(tx('text', {x:p.x,y:p.y,'text-anchor':'middle','dominant-baseline':'central',
            fill:txtCol,'font-size':r>12?11:10,'font-weight':'bold','font-family':'sans-serif'}, p.label));
    }

    // Aggiunge marcatori arrowhead inline nelle defs
    var defs = document.createElementNS(NS, 'defs');
    [['red','#dc2626'],['blue','#2563eb'],['ball','#ca8a04']].forEach(function(pair) {
        var m = el('marker', {id:'cvp-ah-'+pair[0]+'-{{ $pid }}',
            markerWidth:9,markerHeight:7,refX:8,refY:3.5,orient:'auto'});
        var poly = document.createElementNS(NS,'polygon');
        poly.setAttribute('points','0 0, 9 3.5, 0 7');
        poly.setAttribute('fill', pair[1]);
        m.appendChild(poly);
        defs.appendChild(m);
    });
    svg.appendChild(defs);

    render();
}());
</script>
