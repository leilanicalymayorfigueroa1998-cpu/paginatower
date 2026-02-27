<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PropiedadService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'editar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$id = $_POST['txtID'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location:index.php");
    exit();
}

$service = new PropiedadService($conexionBD);

try {

    $service->actualizar($id, [
        'codigo'    => trim($_POST['codigo'] ?? ''),
        'direccion' => trim($_POST['direccion'] ?? ''),
        'latitud'   => $_POST['latitud'] !== '' ? floatval($_POST['latitud']) : null,
        'longitud'  => $_POST['longitud'] !== '' ? floatval($_POST['longitud']) : null,
        'id_tipo'   => $_POST['id_tipo'] ?? null,
        'id_dueno'  => $_POST['id_dueno'] ?? null
    ]);

    header("Location:index.php?mensaje=editado");
    exit();
} catch (Exception $e) {
    die($e->getMessage());
}
