/**
 * MOVIMIENTOS FINANCIEROS — movimientos.js
 *
 * Variables inyectadas desde view.php:
 *   MOV_PUEDE_EDITAR   → boolean
 *   MOV_PUEDE_ELIMINAR → boolean
 */

/* ── DataTable ───────────────────────────── */
$(document).ready(function () {
  if (!$.fn.DataTable) return;

  $('#tablaMovimientos').DataTable({
    language: {
      url: 'https://cdn.datatables.net/plug-ins/2.0.1/i18n/es-MX.json'
    },
    order: [[0, 'desc']],
    pageLength: 10,
    columnDefs: [
      { orderable: false, targets: -1 }   // columna Acciones no ordenable
    ]
  });
});
