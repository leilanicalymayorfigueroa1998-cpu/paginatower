<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once '../../services/DuenoService.php';

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'duenos', 'eliminar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location:index.php");
    exit();
}

$service = new DuenoService($conexionBD);

try {
    $service->eliminar($id);
} catch (Exception $e) {
    die($e->getMessage());
}

header("Location:index.php");
exit();