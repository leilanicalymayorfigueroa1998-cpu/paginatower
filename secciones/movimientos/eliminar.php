<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once('../../app/services/MovimientoService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos', 'eliminar');

$id = $_GET['txtID'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location:index.php");
    exit();
}

$service = new MovimientoService($conexionBD);
$service->eliminar($id);

header("Location:index.php");
exit();

?>