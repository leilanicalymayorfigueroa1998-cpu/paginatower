<?php
/* ════════════════════════════════════════════
   SERVICIOS — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/servicios_mod.css
   JS  → assets/js/servicios_mod.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/ServiciosService.php');

$idRol = $_SESSION['id_rol'] ?? null;
if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'servicios', 'ver');

$service         = new ServiciosService($conexionBD);
$lista_servicios = $service->obtenerTodos();

$puedeCrear    = tienePermiso($conexionBD, $idRol, 'servicios', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'servicios', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'servicios', 'eliminar');

/* ─── Estadísticas ─── */
$total       = count($lista_servicios);
$cfeActivos  = count(array_filter($lista_servicios, fn($s) => !empty($s['cfe'])));
$aguaActivos = count(array_filter($lista_servicios, fn($s) => !empty($s['agua'])));

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/servicios_mod.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
