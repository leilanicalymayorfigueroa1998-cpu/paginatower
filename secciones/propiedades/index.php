<?php
/* ════════════════════════════════════════════
   PROPIEDADES — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/propiedades.css
   JS  → assets/js/propiedades_mod.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PropiedadService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'ver');

$service          = new PropiedadService($conexionBD);
$listaPropiedades = $service->obtenerTodos();

$puedeCrear    = tienePermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'eliminar');

/* ─── Estadísticas ─── */
$total = count($listaPropiedades);
$tipos = array_count_values(array_column($listaPropiedades, 'tipo'));
arsort($tipos);

/* ─── Íconos y colores por tipo ─── */
$iconosTipo = [
  'Casa'          => '🏠',
  'Departamento'  => '🏢',
  'Local'         => '🏪',
  'Oficina'       => '🖥️',
  'Terreno'       => '🌳',
];
$coloresTipo = [
  'Casa'          => 'green',
  'Departamento'  => 'blue',
  'Local'         => 'amber',
  'Oficina'       => 'violet',
  'Terreno'       => 'cyan',
];

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/propiedades_mod.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
