<?php
/* ════════════════════════════════════════════
   ARRENDATARIO — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/arrendatario.css
   JS  → assets/js/arrendatario.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

$idRol = $_SESSION['id_rol'] ?? null;
if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'arrendatarios', 'ver');

$puedeCrear    = tienePermiso($conexionBD, $idRol, 'arrendatarios', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'arrendatarios', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'arrendatarios', 'eliminar');

$consulta = $conexionBD->prepare("
    SELECT
        id_arrendatario,
        nombre,
        telefono,
        correo,
        aval,
        correoaval,
        direccion,
        ciudad
    FROM arrendatarios
    ORDER BY id_arrendatario DESC
");
$consulta->execute();
$listaArrendatarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

/* ─── Estadísticas ─── */
$total      = count($listaArrendatarios);
$conAval    = count(array_filter($listaArrendatarios, fn($a) => !empty($a['aval'])));
$sinAval    = $total - $conAval;
$ciudades   = count(array_unique(array_filter(array_column($listaArrendatarios, 'ciudad'))));

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/arrendatario.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
