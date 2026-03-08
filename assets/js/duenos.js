/**
 * duenos.js — Lógica del módulo Dueños
 *
 * Variables globales inyectadas desde index.php (inline <script>):
 *   dnoDatos        → array de dueños con rentas
 *   dnoProps        → objeto { id_dueno: [propiedades] }
 *   MES_ACTUAL      → número de mes actual (1-12)
 *   ANIO_ACTUAL     → año actual (ej. 2025)
 *   DNO_PUEDE_EDITAR
 *   DNO_PUEDE_ELIMINAR
 *   DNO_CSRF_TOKEN
 */

/* ── CONSTANTES ────────────────────────────────────────── */
const MESES   = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
const COLORES = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#f43f5e','#06b6d4','#ec4899','#84cc16'];

/* ── ESTADO ────────────────────────────────────────────── */
let dnoFiltered = [];
let dnoPage     = 1;
let dnoView     = 'table';
let dnoExpanded = null;

/* ── HELPERS ───────────────────────────────────────────── */

function tipoIcon(tipo) {
  if (!tipo) return '🏠';
  const t = tipo.toLowerCase();
  if (t.includes('casa'))                                 return '🏠';
  if (t.includes('depto') || t.includes('departamento')) return '🏢';
  if (t.includes('local'))                               return '🏪';
  if (t.includes('terreno'))                             return '🌳';
  if (t.includes('oficina'))                             return '🖥️';
  return '🏠';
}

function initials(nombre) {
  return nombre.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
}

function fmt(n) {
  return '$' + Number(n).toLocaleString('es-MX');
}

/* ── RENDER TABLA ──────────────────────────────────────── */

function dnoRender() {
  const pageSize = parseInt(document.getElementById('dnoPageSize').value);
  const start    = (dnoPage - 1) * pageSize;
  const slice    = dnoFiltered.slice(start, start + pageSize);
  const tbody    = document.getElementById('dnoDuenos');
  tbody.innerHTML = '';

  slice.forEach(owner => {
    const globalIdx = dnoDatos.indexOf(owner);
    const color     = COLORES[globalIdx % COLORES.length];
    const rentaM    = parseFloat(owner.renta_activa) || 0;

    /* ─ Fila principal ─ */
    const tr = document.createElement('tr');
    tr.id = 'dno-row-' + owner.id_dueno;
    if (dnoExpanded === owner.id_dueno) tr.classList.add('dno-expanded');

    tr.innerHTML = `
      <td>
        <div class="dno-owner-cell">
          <div class="dno-avatar" style="background:${color}">${initials(owner.nombre)}</div>
          <div>
            <div class="dno-owner-name">${owner.nombre}</div>
            <div class="dno-owner-id">#DUE-${String(owner.id_dueno).padStart(3,'0')}</div>
          </div>
          <span class="dno-chevron ${dnoExpanded === owner.id_dueno ? 'open' : ''}"
                id="chev-${owner.id_dueno}">›</span>
        </div>
      </td>
      <td><span class="dno-phone">📞 ${owner.telefono || '—'}</span></td>
      <td>${owner.correo
        ? `<a class="dno-email" href="mailto:${owner.correo}">${owner.correo}</a>`
        : '<span style="color:var(--text3)">—</span>'}</td>
      <td>
        <span class="dno-badge">🏠 ${owner.total_props} prop${owner.total_props != 1 ? 's' : ''}</span>
      </td>
      <td style="font-family:'DM Mono',monospace;font-size:12.5px;color:var(--green);">
        ${rentaM > 0 ? fmt(rentaM) : '<span style="color:var(--text3)">Sin contratos</span>'}
      </td>
      <td>
        <div style="display:flex;gap:6px;align-items:center;">
          <a class="tw-act"
             style="background:rgba(16,185,129,.08);color:var(--green);border:1px solid rgba(16,185,129,.15);"
             onclick="dnoToggle(event,${owner.id_dueno})">📊 Ver</a>
          ${DNO_PUEDE_EDITAR
            ? `<a class="tw-act tw-act-edit" href="editar.php?txtID=${owner.id_dueno}">Editar</a>`
            : ''}
          ${DNO_PUEDE_ELIMINAR
            ? `<form method="POST" action="eliminar.php" style="display:inline;"
                     onsubmit="return confirm('¿Eliminar este dueño?');">
                 <input type="hidden" name="id" value="${owner.id_dueno}">
                 <input type="hidden" name="csrf_token" value="${DNO_CSRF_TOKEN}">
                 <button type="submit" class="tw-act tw-act-del">Borrar</button>
               </form>`
            : ''}
        </div>
      </td>
    `;

    tr.addEventListener('click', e => {
      if (!e.target.closest('a') && !e.target.closest('button') && !e.target.closest('form')) {
        dnoToggle(e, owner.id_dueno);
      }
    });

    tbody.appendChild(tr);

    /* ─ Fila panel ─ */
    const props   = dnoProps[owner.id_dueno] || [];
    const panelTr = document.createElement('tr');
    panelTr.id    = 'dno-panel-' + owner.id_dueno;
    panelTr.style.display = (dnoExpanded === owner.id_dueno) ? 'table-row' : 'none';
    panelTr.innerHTML = `<td colspan="6" style="padding:0;">${buildPanel(owner, props, color)}</td>`;
    tbody.appendChild(panelTr);
  });

  /* ─ Paginación ─ */
  const total   = dnoFiltered.length;
  const pages   = Math.ceil(total / pageSize) || 1;
  const endIdx  = Math.min(dnoPage * pageSize, total);
  const startIdx = total === 0 ? 0 : start + 1;

  document.getElementById('dnoInfo').textContent =
    `Mostrando ${startIdx} a ${endIdx} de ${total} entradas`;

  const pagDiv = document.getElementById('dnoPag');
  pagDiv.innerHTML = '';

  const prev = Object.assign(document.createElement('div'), { className:'dno-pag-btn', textContent:'‹' });
  prev.onclick = () => { if (dnoPage > 1) { dnoPage--; dnoRender(); } };
  pagDiv.appendChild(prev);

  for (let i = 1; i <= pages; i++) {
    const btn = Object.assign(document.createElement('div'), {
      className : 'dno-pag-btn' + (i === dnoPage ? ' active' : ''),
      textContent: i
    });
    btn.onclick = (pg => () => { dnoPage = pg; dnoRender(); })(i);
    pagDiv.appendChild(btn);
  }

  const next = Object.assign(document.createElement('div'), { className:'dno-pag-btn', textContent:'›' });
  next.onclick = () => { if (dnoPage < pages) { dnoPage++; dnoRender(); } };
  pagDiv.appendChild(next);
}

/* ── CONSTRUIR PANEL DE PROPIEDADES + PROYECCIÓN ───────── */

function buildPanel(owner, props, color) {
  const rentaM           = parseFloat(owner.renta_activa) || 0;
  const rentaAnual       = rentaM * 12;
  const totalRentaPosible = props.reduce((s, p) => s + (parseFloat(p.renta) || 0), 0) * 12;
  const acumulado        = rentaM * MES_ACTUAL;
  const pct = rentaAnual > 0 && totalRentaPosible > 0
    ? Math.round((rentaAnual / totalRentaPosible) * 100)
    : (rentaM > 0 ? 100 : 0);

  /* ─ Tarjetas de propiedades ─ */
  const propCards = props.length > 0
    ? props.map(p => {
        const renta    = parseFloat(p.renta) || 0;
        const statusCls = renta > 0 ? 'activo' : (!p.estatus_local || p.estatus_local === 'Disponible' ? 'libre' : 'otro');
        const statusTxt = renta > 0 ? 'Rentado' : (p.estatus_local || 'Disponible');
        return `
          <div class="dno-prop-card">
            <div class="dno-prop-bar" style="background:${renta > 0 ? 'var(--green)' : 'var(--blue)'}"></div>
            <div class="dno-prop-hdr">
              <span class="dno-prop-icon">${tipoIcon(p.tipo)}</span>
              <span class="dno-prop-status ${statusCls}">${statusTxt}</span>
            </div>
            <div class="dno-prop-code">${p.codigo}</div>
            <div class="dno-prop-addr">📍 ${p.direccion || '—'}</div>
            <div class="dno-prop-tipo">Tipo: ${p.tipo || 'N/A'}</div>
            ${renta > 0
              ? `<div class="dno-prop-renta-lbl">Renta mensual</div>
                 <div class="dno-prop-renta">${fmt(renta)} <span style="font-size:10px;color:var(--text3)">MXN</span></div>
                 <div class="dno-prop-renta-anual">📅 Anual: <span style="color:var(--green);font-weight:700">${fmt(renta * 12)}</span></div>`
              : `<div style="font-size:11px;color:var(--text3);margin-top:4px;">Sin contrato activo</div>`
            }
          </div>`;
      }).join('')
    : `<div style="font-size:12px;color:var(--text3);padding:10px;">No hay propiedades registradas.</div>`;

  /* ─ Calendario mensual ─ */
  const monthDots = MESES.map((m, i) => {
    const n   = i + 1;
    const cls = n < MES_ACTUAL ? 'past' : n === MES_ACTUAL ? 'current' : 'future';
    const tip = n <= MES_ACTUAL ? `${m}: ${fmt(rentaM)} MXN` : `${m}: Pendiente`;
    return `<div class="dno-month ${cls}" title="${tip}">${m}</div>`;
  }).join('');

  return `
    <div class="dno-panel">
      <div class="dno-panel-hdr">
        <div class="dno-panel-title">🏠 Propiedades de ${owner.nombre.split(' ')[0]}</div>
        <span style="font-size:10.5px;color:var(--text3);">${owner.total_props} propiedad(es)</span>
      </div>

      <div class="dno-props-grid">${propCards}</div>

      <div class="dno-annual">
        <div class="dno-annual-title">📈 Proyección de Ganancias ${ANIO_ACTUAL}</div>
        <div class="dno-annual-row">
          <div class="dno-annual-item">
            <div class="dno-annual-item-lbl">💰 Ingreso anual (activo)</div>
            <div class="dno-annual-item-val">${fmt(rentaAnual)}</div>
            <div class="dno-annual-item-sub">MXN / año</div>
          </div>
          <div class="dno-annual-item">
            <div class="dno-annual-item-lbl">📆 Acumulado a ${MESES[MES_ACTUAL - 1]}</div>
            <div class="dno-annual-item-val blue">${fmt(acumulado)}</div>
            <div class="dno-annual-item-sub">MXN cobrados</div>
          </div>
          <div class="dno-annual-item">
            <div class="dno-annual-item-lbl">🚀 Potencial máximo</div>
            <div class="dno-annual-item-val amber">${fmt(totalRentaPosible)}</div>
            <div class="dno-annual-item-sub">Si todo rentado</div>
          </div>
          <div class="dno-bar-wrap">
            <div class="dno-bar-lbl">
              <span>Ocupación rentada</span>
              <span>${pct}%</span>
            </div>
            <div class="dno-bar-bg">
              <div class="dno-bar-fill" style="width:${pct}%"></div>
            </div>
            <div style="font-size:10px;color:var(--text3);margin-top:3px;">
              ${owner.total_props} propiedad(es) registrada(s)
            </div>
          </div>
        </div>
        <div class="dno-months">${monthDots}</div>
        <div style="font-size:9.5px;color:var(--text3);margin-top:7px;">
          🟩 Pasado &nbsp; ✅ Mes actual &nbsp; ◻️ Pendiente &nbsp;·&nbsp; Basado en contratos activos
        </div>
      </div>
    </div>`;
}

/* ── TOGGLE PANEL ──────────────────────────────────────── */

function dnoToggle(e, id) {
  if (e) e.stopPropagation();

  const panelTr = document.getElementById('dno-panel-' + id);
  const chev    = document.getElementById('chev-' + id);
  const row     = document.getElementById('dno-row-' + id);
  const isOpen  = panelTr && panelTr.style.display !== 'none';

  /* Cerrar el panel abierto anterior */
  if (dnoExpanded && dnoExpanded !== id) {
    const prevPanel = document.getElementById('dno-panel-' + dnoExpanded);
    const prevChev  = document.getElementById('chev-' + dnoExpanded);
    const prevRow   = document.getElementById('dno-row-' + dnoExpanded);
    if (prevPanel) prevPanel.style.display = 'none';
    if (prevChev)  prevChev.classList.remove('open');
    if (prevRow)   prevRow.classList.remove('dno-expanded');
  }

  if (isOpen) {
    panelTr.style.display = 'none';
    if (chev) chev.classList.remove('open');
    if (row)  row.classList.remove('dno-expanded');
    dnoExpanded = null;
  } else {
    if (panelTr) panelTr.style.display = 'table-row';
    if (chev) chev.classList.add('open');
    if (row)  row.classList.add('dno-expanded');
    dnoExpanded = id;
  }
}

/* ── RENDER TARJETAS ───────────────────────────────────── */

function dnoRenderCards() {
  const grid = document.getElementById('dnoCardsGrid');
  grid.innerHTML = '';

  dnoFiltered.forEach(owner => {
    const color  = COLORES[dnoDatos.indexOf(owner) % COLORES.length];
    const props  = dnoProps[owner.id_dueno] || [];
    const rentaM = parseFloat(owner.renta_activa) || 0;

    const miniProps = props.map(p => {
      const renta    = parseFloat(p.renta) || 0;
      const hasRenta = renta > 0;
      return `
        <div class="dno-mini-prop">
          <span class="dno-mini-icon">${tipoIcon(p.tipo)}</span>
          <div style="flex:1;min-width:0;">
            <div class="dno-mini-name">${p.codigo}</div>
            <div class="dno-mini-addr">${p.direccion || ''}</div>
          </div>
          <div style="text-align:right;flex-shrink:0;">
            ${hasRenta ? `<div class="dno-mini-renta">${fmt(renta)}</div>` : ''}
            <div class="dno-mini-status"
                 style="${hasRenta
                   ? 'background:var(--green-g);color:var(--green);'
                   : 'background:var(--blue-g);color:var(--blue);'
                 }font-size:9px;font-weight:700;padding:1px 5px;border-radius:8px;">
              ${hasRenta ? 'Rentado' : 'Libre'}
            </div>
          </div>
        </div>`;
    }).join('') || `<div style="font-size:11px;color:var(--text3);padding:4px 0">Sin propiedades.</div>`;

    const card = document.createElement('div');
    card.className = 'dno-card';
    card.innerHTML = `
      <div class="dno-card-hdr">
        <div class="dno-card-avatar" style="background:${color}">${initials(owner.nombre)}</div>
        <div>
          <div class="dno-card-name">${owner.nombre}</div>
          <div class="dno-card-email">✉️ ${owner.correo || '—'}</div>
          <div class="dno-card-phone">📞 ${owner.telefono || '—'}</div>
        </div>
      </div>
      <div class="dno-card-props">
        <div class="dno-card-props-title">🏠 Propiedades (${owner.total_props})</div>
        ${miniProps}
      </div>
      <div class="dno-card-annual">
        <div>
          <div class="dno-card-annual-lbl">📈 Ganancia anual estimada</div>
          <div style="font-size:9.5px;color:var(--text3);">Contratos activos · ${ANIO_ACTUAL}</div>
        </div>
        <div class="dno-card-annual-val">
          ${fmt(rentaM * 12)}
          <span style="font-size:9px;font-weight:400;color:var(--text3)">MXN</span>
        </div>
      </div>
      <div class="dno-card-actions">
        ${DNO_PUEDE_EDITAR
          ? `<a class="tw-act tw-act-edit" href="editar.php?txtID=${owner.id_dueno}"
                style="flex:1;text-align:center;">✏️ Editar</a>`
          : ''}
        ${DNO_PUEDE_ELIMINAR
          ? `<form method="POST" action="eliminar.php"
                   onsubmit="return confirm('¿Eliminar este dueño?');">
               <input type="hidden" name="id" value="${owner.id_dueno}">
               <input type="hidden" name="csrf_token" value="${DNO_CSRF_TOKEN}">
               <button type="submit" class="tw-act tw-act-del">🗑️</button>
             </form>`
          : ''}
      </div>`;

    grid.appendChild(card);
  });
}

/* ── BÚSQUEDA EN TIEMPO REAL ───────────────────────────── */

function dnoFilter(q) {
  const lq = q.toLowerCase();
  dnoFiltered = dnoDatos.filter(o =>
    o.nombre.toLowerCase().includes(lq) ||
    (o.correo   || '').toLowerCase().includes(lq) ||
    (o.telefono || '').includes(lq)
  );
  dnoPage = 1;
  dnoRender();
  dnoRenderCards();
}

/* ── CAMBIAR VISTA ─────────────────────────────────────── */

function dnoSetView(view, btn) {
  dnoView = view;
  document.querySelectorAll('.dno-view-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('dnoTableView').classList.toggle('hidden', view !== 'table');
  document.getElementById('dnoCardsView').classList.toggle('hidden', view !== 'cards');
}

/* ── MODAL ─────────────────────────────────────────────── */

function dnoOpenModal()  { document.getElementById('dnoModal').classList.add('open'); }
function dnoCloseModal() { document.getElementById('dnoModal').classList.remove('open'); }

document.getElementById('dnoModal').addEventListener('click', e => {
  if (e.target === document.getElementById('dnoModal')) dnoCloseModal();
});

/* ── INICIALIZACIÓN ────────────────────────────────────── */

dnoFiltered = [...dnoDatos];
dnoRender();
dnoRenderCards();
