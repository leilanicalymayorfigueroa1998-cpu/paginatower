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

verificarPermiso($conexionBD, $idRol, 'duenos', 'crear');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

// 🔐 Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$service = new DuenoService($conexionBD);

try {

    $service->crear([
        'nombre'   => trim($_POST['nombre'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'correo'   => trim($_POST['correo'] ?? '')
    ]);

    header("Location:index.php");
    exit();
} catch (Exception $e) {
    die($e->getMessage());
}

?>