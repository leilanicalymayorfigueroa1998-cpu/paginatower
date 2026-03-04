<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once('../../app/services/ServiciosService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'servicios', 'crear');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$id_local = filter_input(INPUT_POST, 'id_local', FILTER_VALIDATE_INT);
$cfe = (int)($_POST['cfe'] ?? 0);
$agua = (int)($_POST['agua'] ?? 0);

$contrato_cfe = trim($_POST['contrato_cfe'] ?? '');
$contrato_agua = trim($_POST['contrato_agua'] ?? '');

if (!$id_local) {
    header("Location: crear.php?mensaje=error");
    exit();
}

$service = new ServiciosService($conexionBD);

try {
    $service->crear($id_local, $cfe, $agua, $contrato_cfe, $contrato_agua);
    header("Location:index.php?mensaje=creado");
    exit();
} catch (Exception $e) {
    header("Location: crear.php?mensaje=error&detalle=" . urlencode($e->getMessage()));
    exit();
}

?>