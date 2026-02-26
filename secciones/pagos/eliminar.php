<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/PagoService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

$id_pago = intval($_POST['txtID']);

if ($id_pago <= 0) {
    die("ID inválido.");
}

$service = new PagoService($conexionBD);

try {
    $service->eliminarPago($id_pago);
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>