<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/ContratoService.php';

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'contratos', 'eliminar');

// 🚫 Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 🔐 Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']); // 🔹 importante

$id_contrato = intval($_POST['txtID'] ?? 0);

if ($id_contrato <= 0) {
    die("ID inválido.");
}

$service = new ContratoService($conexionBD);

try {

    $service->eliminarContrato($id_contrato);

    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>