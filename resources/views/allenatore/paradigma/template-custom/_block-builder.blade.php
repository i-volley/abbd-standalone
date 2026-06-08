<script>
(function () {
    const BLOCK_TYPES = [
        { value: 'warmup',                label: 'Riscaldamento',               color: '#f59e0b' },
        { value: 'technical',             label: 'Tecnica',                     color: '#3b82f6' },
        { value: 'tactical',              label: 'Tattica',                     color: '#06b6d4' },
        { value: 'ecological_constraint', label: 'Vincolo ecologico',           color: '#10b981' },
        { value: 'game_form',             label: 'Forma di gioco',              color: '#ef4444' },
        { value: 'cooldown',              label: 'Cool-down / Riflessione',     color: '#6b7280' },
        { value: 'free',                  label: 'Blocco libero',               color: '#1e293b' },
    ];

    const typeMap = Object.fromEntries(BLOCK_TYPES.map(t => [t.value, t]));

    const list    = document.getElementById('blocks-list');
    const btnAdd  = document.getElementById('btn-add-block');
    const durEl   = document.getElementById('tpl-total-dur');
    let   counter = 0;

    // Pre-populate from server (create: empty, edit: existing blocks)
    const EXISTING = @json($existingBlocks ?? []);

    function updateTotal() {
        let tot = 0;
        list.querySelectorAll('.block-dur').forEach(function(inp) {
            const v = parseInt(inp.value, 10);
            if (!isNaN(v) && v > 0) tot += v;
        });
        durEl.textContent = tot > 0 ? tot + ' min' : '—';
    }

    function buildBlockRow(data) {
        const idx  = counter++;
        const bt   = data.block_type || 'free';
        const info = typeMap[bt] || typeMap['free'];

        const row = document.createElement('div');
        row.className = 'block-row card border-0 shadow-sm';
        row.style.borderLeft = '4px solid ' + info.color;

        const typeOptions = BLOCK_TYPES.map(function(t) {
            return '<option value="' + t.value + '"' + (t.value === bt ? ' selected' : '') + '>' + t.label + '</option>';
        }).join('');

        row.innerHTML = `
        <div class="card-body py-2 px-3">
            <div class="row g-2 align-items-center">
                <div class="col-auto" style="cursor:grab;color:#adb5bd;font-size:1.1rem;padding-top:.2rem" title="Trascina per riordinare">⠿</div>
                <div class="col-md-3">
                    <select name="blocks[${idx}][block_type]" class="form-select form-select-sm block-type-sel" required>
                        ${typeOptions}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="blocks[${idx}][block_name]" class="form-control form-control-sm"
                           placeholder="{{ __('Nome blocco') }} *" value="${escHtml(data.block_name || '')}" required>
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <input type="number" name="blocks[${idx}][suggested_duration_minutes]" class="form-control block-dur"
                               placeholder="min" min="1" max="999" value="${escHtml(String(data.suggested_duration_minutes || ''))}">
                        <span class="input-group-text">'</span>
                    </div>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-block">×</button>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-12">
                    <input type="text" name="blocks[${idx}][block_description]" class="form-control form-control-sm"
                           placeholder="{{ __('Descrizione breve (opzionale)') }}" value="${escHtml(data.block_description || '')}">
                </div>
            </div>
        </div>`;

        // Cambia colore bordo sinistro al cambio tipo
        row.querySelector('.block-type-sel').addEventListener('change', function() {
            const inf = typeMap[this.value] || typeMap['free'];
            row.style.borderLeft = '4px solid ' + inf.color;
        });

        row.querySelector('.btn-remove-block').addEventListener('click', function() {
            row.remove();
            renumberNames();
            updateTotal();
        });

        row.querySelector('.block-dur').addEventListener('input', updateTotal);

        return row;
    }

    function renumberNames() {
        let i = 0;
        list.querySelectorAll('.block-row').forEach(function(row) {
            row.querySelectorAll('[name]').forEach(function(el) {
                el.name = el.name.replace(/blocks\[\d+\]/, 'blocks[' + i + ']');
            });
            i++;
        });
        counter = i;
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // Init with existing blocks (or one empty row for create)
    if (EXISTING.length > 0) {
        EXISTING.forEach(function(b) { list.appendChild(buildBlockRow(b)); });
    } else {
        list.appendChild(buildBlockRow({}));
    }
    updateTotal();

    btnAdd.addEventListener('click', function() {
        list.appendChild(buildBlockRow({}));
        updateTotal();
    });

    // ── Drag-to-reorder (semplice, senza librerie) ──────────────────────────
    let dragSrc = null;

    list.addEventListener('dragstart', function(e) {
        dragSrc = e.target.closest('.block-row');
        if (!dragSrc) return;
        dragSrc.style.opacity = '.4';
        e.dataTransfer.effectAllowed = 'move';
    });
    list.addEventListener('dragend', function() {
        if (dragSrc) { dragSrc.style.opacity = ''; dragSrc = null; }
        renumberNames();
    });
    list.addEventListener('dragover', function(e) {
        e.preventDefault();
        const over = e.target.closest('.block-row');
        if (!over || over === dragSrc) return;
        const rect = over.getBoundingClientRect();
        const mid  = rect.top + rect.height / 2;
        if (e.clientY < mid) list.insertBefore(dragSrc, over);
        else list.insertBefore(dragSrc, over.nextSibling);
    });

    list.querySelectorAll('.block-row').forEach(function(row) {
        row.setAttribute('draggable', 'true');
    });

    // Make future rows draggable via MutationObserver
    new MutationObserver(function(muts) {
        muts.forEach(function(m) {
            m.addedNodes.forEach(function(n) {
                if (n.classList && n.classList.contains('block-row')) n.setAttribute('draggable','true');
            });
        });
    }).observe(list, { childList: true });

})();
</script>
