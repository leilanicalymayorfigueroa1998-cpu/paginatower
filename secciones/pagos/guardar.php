<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/PagosService.php';

// 🔐 Verificar sesión
$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

// 🔐 Permiso para crear pagos
verificarPermiso($conexionBD, $idRol, 'pagos', 'crear');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 🔐 Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

$id_contrato = intval($_POST['id_contrato'] ?? 0);
$fecha_pago  = $_POST['fecha_pago'] ?? '';
$monto       = floatval($_POST['monto'] ?? 0);
$metodo_pago = trim($_POST['metodo_pago'] ?? '');
$estatus     = trim($_POST['estatus'] ?? '');

if ($id_contrato <= 0 || $monto <= 0 || empty($fecha_pago)) {
    die("Datos inválidos.");
}

$service = new PagoService($conexionBD);

try {

    $service->crearPago(
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