<?php
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

verificarPermiso($conexionBD, $idRol, 'servicios', 'eliminar');

$id = filter_input(INPUT_GET, 'txtID', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location:index.php");
    exit();
}

$service = new ServiciosService($conexionBD);
$service->eliminar($id);

header("Location:index.php?mensaje=eliminado");
exit();