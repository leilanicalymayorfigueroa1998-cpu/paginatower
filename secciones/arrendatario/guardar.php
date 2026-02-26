<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

require_once __DIR__ . '/../../services/ArrendatarioService.php';

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'arrendatarios', 'crear');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $service = new ArrendatarioService($conexionBD);

    try {

        $service->crear([
            'nombre' => trim($_POST['nombre'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'aval' => trim($_POST['aval'] ?? ''),
            'correoaval' => trim($_POST['correoaval'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'ciudad' => trim($_POST['ciudad'] ?? '')
        ]);

        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
