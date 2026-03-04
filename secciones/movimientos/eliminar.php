<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/MovimientoService.php';

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos_financieros', 'eliminar');

$id = $_GET['txtID'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location:index.php");
    exit();
}

$service = new MovimientoService($conexionBD);

try {

    $movimiento = $service->obtenerPorId($id);

    if (!$movimiento) {
        header("Location:index.php");
        exit();
    }

    $service->eliminar($id);

    header("Location:index.php?msg=eliminado");
    exit();

} catch (Exception $e) {

    header("Location:index.php?error=1");
    exit();

}
?>