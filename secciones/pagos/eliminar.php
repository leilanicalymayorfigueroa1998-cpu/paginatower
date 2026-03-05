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
   Verificar permiso
========================= */
verificarPermiso($conexionBD, $idRol, 'pagos', 'eliminar');

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
$csrf = $_POST['csrf_token'] ?? '';

if (!validarTokenCSRF($csrf)) {
    echo "<div class='alert alert-danger'>Acceso inválido (CSRF).</div>";
    exit();
}

/* =========================
   Obtener ID
========================= */
$id_pago = intval($_POST['txtID'] ?? 0);

if ($id_pago <= 0) {

    echo "<div class='alert alert-danger'>ID de pago inválido.</div>";
    exit();
}

/* =========================
   Eliminar pago
========================= */
$service = new PagoService($conexionBD);

try {

    $service->eliminarPago($id_pago);

    header("Location: index.php?msg=eliminado");
    exit();

} catch (Exception $e) {

    echo "<div class='alert alert-danger'>
    Error al eliminar el pago: " . htmlspecialchars($e->getMessage()) . "
    </div>";

}

?>
