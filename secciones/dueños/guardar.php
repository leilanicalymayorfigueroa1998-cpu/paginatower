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

// Soporta nombre completo en un campo ('nombre')
// o dividido en nombre_p + nombre_a (modal mejorado)
if (!empty($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);
} else {
    $partes = array_filter([
        trim($_POST['nombre_p'] ?? ''),
        trim($_POST['nombre_a'] ?? '')
    ]);
    $nombre = implode(' ', $partes);
}

try {
    $service->crear([
        'nombre'   => $nombre,
        'telefono' => trim($_POST['telefono'] ?? ''),
        'correo'   => trim($_POST['correo'] ?? '')
    ]);

    header("Location:index.php?mensaje=creado");
    exit();
} catch (Exception $e) {
    die($e->getMessage());
}
?>
