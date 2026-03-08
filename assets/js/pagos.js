/**
 * PAGOS — pagos.js
 * Variables inyectadas desde view.php:
 *   PAG_PUEDE_CREAR → boolean
 */
$(document).ready(function () {
  if (!$.fn.DataTable) return;

  const $tabla = $('#tablaPagos');
  if (!$tabla.length) return;

  if ($.fn.DataTable.isDataTable($tabla)) $tabla.DataTable().destroy();

  $tabla.DataTable({
    language: { url: 'https://cdn.datatables.net/plug-ins/2.0.1/i18n/es-MX.json' },
    order: [[6, 'asc']],    // ordenar por estatus del mes (Vencido primero)
    pageLength: 10,
    columnDefs: [{ orderable: false, targets: -1 }]
  });
});
