<?php
/* ════════════════════════════════════════════
   MOVIMIENTOS FINANCIEROS — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/movimientos.css
   JS  → assets/js/movimientos.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/MovimientoService.php';

$idRol = $_SESSION['id_rol'] ?? null;
if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'movimientos_financieros', 'ver');

$service          = new MovimientoService($conexionBD);
$listaMovimientos = $service->obtenerTodos();

$puedeCrear    = tienePermiso($conexionBD, $idRol, 'movimientos_financieros', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'movimientos_financieros', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'movimientos_financieros', 'eliminar');

/* ─── Cálculo de estadísticas ─── */
$totalAbonos = array_sum(array_column($listaMovimientos, 'abono'));
$totalCargos = array_sum(array_column($listaMovimientos, 'cargo'));
$totalMov    = count($listaMovimientos);
$balance     = $totalAbonos - $totalCargos;

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/movimientos.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
