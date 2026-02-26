<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
require_once __DIR__ . '/../../services/ArrendatarioService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $id = intval($_POST['id'] ?? 0);

    if (!$id) {
        die("ID inválido");
    }

    $service = new ArrendatarioService($conexionBD);

    try {

        $service->actualizar($id, [
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
        die($e->getMessage());
    }
}
