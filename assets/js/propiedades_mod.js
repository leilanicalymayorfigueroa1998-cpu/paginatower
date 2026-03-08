/**
 * PROPIEDADES — propiedades_mod.js
 *
 * Variables inyectadas desde view.php:
 *   PROP_PUEDE_EDITAR   → boolean
 *   PROP_PUEDE_ELIMINAR → boolean
 */

/* ── DataTable ───────────────────────────── */
$(document).ready(function () {
  if (!$.fn.DataTable) return;

  const $tabla = $('#tablaPropiedades');
  if (!$tabla.length) return;

  if ($.fn.DataTable.isDataTable($tabla)) {
    $tabla.DataTable().destroy();
  }

  $tabla.DataTable({
    language: {
      url: 'https://cdn.datatables.net/plug-ins/2.0.1/i18n/es-MX.json'
    },
    order: [[0, 'asc']],
    pageLength: 10,
    columnDefs: [
      { orderable: false, targets: -1 }
    ]
  });
});
