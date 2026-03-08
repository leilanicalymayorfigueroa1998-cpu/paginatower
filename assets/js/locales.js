/**
 * LOCALES / INMUEBLES — locales.js
 *
 * Variables inyectadas desde view.php:
 *   LOC_PUEDE_EDITAR   → boolean
 *   LOC_PUEDE_ELIMINAR → boolean
 */

/* ── Estado ──────────────────────────────── */
let _dtLocales = null;

/* ── DataTable ───────────────────────────── */
$(document).ready(function () {
  if (!$.fn.DataTable) return;

  const $tabla = $('#tablaLocales');
  if (!$tabla.length) return;

  // Destruir si ya existe (previene "Cannot reinitialise DataTable")
  if ($.fn.DataTable.isDataTable($tabla)) {
    $tabla.DataTable().destroy();
  }

  _dtLocales = $tabla.DataTable({
    language: {
      url: 'https://cdn.datatables.net/plug-ins/2.0.1/i18n/es-MX.json'
    },
    order: [[0, 'asc']],
    pageLength: 10,
    columnDefs: [
      { orderable: false, targets: -1 }   // columna Acciones
    ]
  });
});

/* ── Filtro rápido por estatus ───────────── */
function locFiltrar(valor, btn) {
  // Activar chip seleccionado
  document.querySelectorAll('.loc-chips .chip-filtro')
    .forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  // Filtrar DataTable
  if (_dtLocales) {
    _dtLocales.search(valor === 'todos' ? '' : valor).draw();
  }
}
