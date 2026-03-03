<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/RestriccionService.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

// 🔐 Permiso para crear
verificarPermiso($conexionBD, $idRol, 'restricciones', 'crear');

// ✅ Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

// 🔐 Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$id_local     = filter_input(INPUT_POST, 'id_local', FILTER_VALIDATE_INT);
$restriccion  = trim($_POST['restriccion'] ?? '');

if (!$id_local || empty($restriccion)) {
    header("Location: crear.php?mensaje=error");
    exit();
}

$service = new RestriccionService($conexionBD);

$service->crearRestriccion($id_local, $restriccion);

header("Location:index.php?mensaje=creado");
exit();
?>