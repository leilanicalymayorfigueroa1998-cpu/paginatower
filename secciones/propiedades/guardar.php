<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PropiedadService.php');

// 🔐 Verificar permiso
verificarPermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'crear');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

// 🔐 Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$service = new PropiedadService($conexionBD);

try {

   $service->crear([
    'codigo'     => trim($_POST['codigo'] ?? ''),
    'direccion'  => trim($_POST['direccion'] ?? ''),
    'latitud'    => $_POST['latitud'] !== '' ? floatval($_POST['latitud']) : null,
    'longitud'   => $_POST['longitud'] !== '' ? floatval($_POST['longitud']) : null,
    'id_tipo'    => $_POST['id_tipo'] ?? null,
    'id_dueno'   => $_POST['id_dueno'] ?? null
]);

    header("Location:index.php?mensaje=creado");
    exit();

} catch (Exception $e) {
    die($e->getMessage());
}

?>