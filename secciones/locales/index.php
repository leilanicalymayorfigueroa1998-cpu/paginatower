<?php
/* ════════════════════════════════════════════
   LOCALES / INMUEBLES — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/locales.css
   JS  → assets/js/locales.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/LocalService.php');

$idRol = $_SESSION['id_rol'] ?? null;
if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'locales', 'ver');

$service      = new LocalService($conexionBD);
$listaLocales = $service->obtenerTodos();

$puedeCrear    = tienePermiso($conexionBD, $idRol, 'locales', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'locales', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'locales', 'eliminar');

/* ─── Estadísticas ─── */
$total       = count($listaLocales);
$ocupados    = count(array_filter($listaLocales, fn($l) => ($l['estatus'] ?? '') === 'Ocupado'));
$disponibles = $total - $ocupados;
$pctOcup     = $total > 0 ? round($ocupados / $total * 100) : 0;

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/locales.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
