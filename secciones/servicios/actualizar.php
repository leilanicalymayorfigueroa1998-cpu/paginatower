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

verificarPermiso($conexionBD, $idRol, 'servicios', 'editar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$id = filter_input(INPUT_POST, 'txtID', FILTER_VALIDATE_INT);
$id_local = filter_input(INPUT_POST, 'id_local', FILTER_VALIDATE_INT);

$cfe = (int)($_POST['cfe'] ?? 0);
$agua = (int)($_POST['agua'] ?? 0);

$contrato_cfe = trim($_POST['contrato_cfe'] ?? '');
$contrato_agua = trim($_POST['contrato_agua'] ?? '');

if (!$id || !$id_local) {
    header("Location:index.php");
    exit();
}

if ($cfe === 1 && $contrato_cfe === '') {
    header("Location: editar.php?txtID=$id&mensaje=error");
    exit();
}

if ($agua === 1 && $contrato_agua === '') {
    header("Location: editar.php?txtID=$id&mensaje=error");
    exit();
}

$service = new ServiciosService($conexionBD);
$service->actualizar($id, $id_local, $cfe, $agua, $contrato_cfe, $contrato_agua);

header("Location:index.php?mensaje=editado");
exit();

?>