<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PagosService.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

// 🔐 Verificar permiso
verificarPermiso($conexionBD, $idRol, 'pagos', 'editar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

$id_pago     = intval($_POST['txtID']);
$id_contrato = intval($_POST['id_contrato']);
$fecha_pago  = $_POST['fecha_pago'] ?? '';
$monto       = floatval($_POST['monto'] ?? 0);
$metodo_pago = trim($_POST['metodo_pago'] ?? '');
$estatus     = trim($_POST['estatus'] ?? '');

if ($id_pago <= 0 || $id_contrato <= 0 || empty($fecha_pago) || $monto <= 0) {
    die("Datos inválidos.");
}

$estatusPermitidos = ['Pendiente','Pagado'];
if (!in_array($estatus, $estatusPermitidos)) {
    die("Estatus inválido.");
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