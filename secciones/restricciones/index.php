<?php
/* ════════════════════════════════════════════
   RESTRICCIONES — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/restricciones.css
   JS  → assets/js/restricciones.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/RestriccionService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'restricciones', 'ver');

$service            = new RestriccionService($conexionBD);
$listaRestricciones = $service->obtenerTodas();

$puedeCrear    = tienePermiso($conexionBD, $_SESSION['id_rol'], 'restricciones', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $_SESSION['id_rol'], 'restricciones', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $_SESSION['id_rol'], 'restricciones', 'eliminar');

/* ─── Estadísticas ─── */
$total     = count($listaRestricciones);
$inmuebles = count(array_unique(array_column($listaRestricciones, 'codigo')));
$promedio  = ($total > 0 && $inmuebles > 0) ? round($total / $inmuebles, 1) : 0;

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/restricciones.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
