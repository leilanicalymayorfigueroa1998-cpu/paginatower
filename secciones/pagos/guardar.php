<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/PagosService.php';

/* =========================
   Verificar sesión
========================= */
$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

/* =========================
   Permiso crear pagos
========================= */
verificarPermiso($conexionBD, $idRol, 'pagos', 'crear');

/* =========================
   Solo POST
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

/* =========================
   Validar CSRF
========================= */
$csrf = $_POST['csrf_token'] ?? '';

if (!validarTokenCSRF($csrf)) {
    echo "<div class='alert alert-danger'>Acceso inválido (CSRF)</div>";
    exit();
}

/* =========================
   Obtener datos
========================= */
$id_contrato = intval($_POST['id_contrato'] ?? 0);
$fecha_pago  = trim($_POST['fecha_pago'] ?? '');
$monto       = floatval($_POST['monto'] ?? 0);
$metodo_pago = trim($_POST['metodo_pago'] ?? '');
$estatus     = trim($_POST['estatus'] ?? '');

/* =========================
   Validaciones
========================= */
if ($id_contrato <= 0) {
    die("Contrato inválido.");
}

if ($monto <= 0) {
    die("El monto debe ser mayor a 0.");
}

if (empty($fecha_pago)) {
    die("Debes seleccionar una fecha de pago.");
}

/* Estatus permitidos */
$estatusPermitidos = ['Pendiente', 'Pagado'];

if (!in_array($estatus, $estatusPermitidos)) {
    die("Estatus inválido.");
}

/* Si está pagado debe tener método */
if ($estatus === 'Pagado' && empty($metodo_pago)) {
    die("Debes seleccionar un método de pago.");
}

/* =========================
   Guardar pago
========================= */
$service = new PagoService($conexionBD);

try {

    $service->crearPago(
        $id_contrato,
        $fecha_pago,
        $monto,
        $metodo_pago,
        $estatus
    );

    header("Location: index.php?msg=creado");
    exit();

} catch (Exception $e) {

    echo "<div class='alert alert-danger'>
    Error al guardar el pago: " . htmlspecialchars($e->getMessage()) . "
    </div>";

}
?>