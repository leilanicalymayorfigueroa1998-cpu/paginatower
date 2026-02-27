<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PropiedadService.php');

if (!isset($_SESSION['id_rol'])) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'eliminar');

if (!isset($_GET['txtID']) || !is_numeric($_GET['txtID'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['txtID']);

$service = new PropiedadService($conexionBD);

$propiedad = $service->obtenerPorId($id);

if (!$propiedad) {
    header("Location: index.php");
    exit();
}

if ($service->eliminar($id)) {
    header("Location: index.php?mensaje=eliminado");
} else {
    header("Location: index.php?mensaje=error");
}

exit();

?>