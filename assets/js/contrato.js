/**
 * CONTRATOS — contrato.js
 * Variables inyectadas desde view.php:
 *   CON_PUEDE_EDITAR   → boolean
 *   CON_PUEDE_ELIMINAR → boolean
 */
$(document).ready(function () {
  if (!$.fn.DataTable) return;

  const $tabla = $('#tablaContratos');
  if (!$tabla.length) return;

  if ($.fn.DataTable.isDataTable($tabla)) $tabla.DataTable().destroy();

  $tabla.DataTable({
    language: { url: 'https://cdn.datatables.net/plug-ins/2.0.1/i18n/es-MX.json' },
    order: [[0, 'asc']],
    pageLength: 10,
    columnDefs: [{ orderable: false, targets: -1 }]
  });
});
