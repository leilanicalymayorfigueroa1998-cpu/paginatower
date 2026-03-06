<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/MovimientoService.php';

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos_financieros', 'crear');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}
unset($_SESSION['csrf_token']);

$fecha             = trim($_POST['fecha'] ?? '');
$id_propiedad      = intval($_POST['id_propiedad'] ?? 0);
$id_tipo_operacion = intval($_POST['id_tipo_operacion'] ?? 0);
$nota              = trim($_POST['nota'] ?? '');
$abono             = floatval($_POST['abono'] ?? 0);
$cargo             = floatval($_POST['cargo'] ?? 0);
$origen            = trim($_POST['origen'] ?? '');

if (!$fecha || !$id_propiedad || !$id_tipo_operacion || !$origen) {
    die("Faltan datos obligatorios.");
}

$service = new MovimientoService($conexionBD);

try {
    $service->crear([
        'fecha'             => $fecha,
        'id_propiedad'      => $id_propiedad,
        'id_tipo_operacion' => $id_tipo_operacion,
        'nota'              => $nota,
        'abono'             => $abono,
        'cargo'             => $cargo,
        'origen'            => $origen,
        'id_pago'           => null,
    ]);

    header("Location: index.php?msg=guardado");
    exit();

} catch (Exception $e) {
    die("Error al guardar el movimiento: " . htmlspecialchars($e->getMessage()));
}
