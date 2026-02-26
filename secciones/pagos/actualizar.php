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

$id_pago     = intval($_POST['txtID']);
$id_contrato = intval($_POST['id_contrato']);
$fecha_pago  = $_POST['fecha_pago'];
$monto       = floatval($_POST['monto']);
$metodo_pago = $_POST['metodo_pago'];
$estatus     = $_POST['estatus'];

if ($id_pago <= 0) {
    die("ID inválido.");
}

$service = new PagoService($conexionBD);

try {
    $service->actualizarPago(
        $id_pago,
        $id_contrato,
        $fecha_pago,
        $monto,
        $metodo_pago,
        $estatus
    );

    header("Location: index.php");
    exit();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>