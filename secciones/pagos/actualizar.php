<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PagosService.php');

/* =========================
   Verificar sesión
========================= */
$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

/* =========================
   Verificar permiso
========================= */
verificarPermiso($conexionBD, $idRol, 'pagos', 'editar');

/* =========================
   Solo método POST
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

/* =========================
   Validar CSRF
========================= */
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

/* =========================
   Obtener datos
========================= */
$id_pago     = intval($_POST['txtID'] ?? 0);
$id_contrato = intval($_POST['id_contrato'] ?? 0);
$fecha_pago  = trim($_POST['fecha_pago'] ?? '');
$monto       = floatval($_POST['monto'] ?? 0);
$metodo_pago = trim($_POST['metodo_pago'] ?? '');
$estatus     = trim($_POST['estatus'] ?? '');

/* =========================
   Validaciones
========================= */
if ($id_pago <= 0) {
    die("ID de pago inválido.");
}

if ($id_contrato <= 0) {
    die("Contrato inválido.");
}

if (empty($fecha_pago)) {
    die("La fecha de pago es obligatoria.");
}

if ($monto <= 0) {
    die("El monto debe ser mayor a 0.");
}

/* Estatus permitidos */
$estatusPermitidos = ['Pendiente','Pagado','Vencido'];

if (!in_array($estatus, $estatusPermitidos)) {
    die("Estatus inválido.");
}

/* Si está pagado debe tener método */
if ($estatus === 'Pagado' && empty($metodo_pago)) {
    die("Debe seleccionar un método de pago.");
}

/* =========================
   Actualizar pago
========================= */
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

    echo "<h3>Error al actualizar el pago</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";

}

?>


