<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos', 'crear');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $fecha = $_POST['fecha'] ?? '';
    $id_propiedad = $_POST['id_propiedad'] ?? '';
    $id_tipo_operacion = $_POST['id_tipo_operacion'] ?? '';
    $nota = trim($_POST['nota'] ?? '');
    $abono = floatval($_POST['abono'] ?? 0);
    $cargo = floatval($_POST['cargo'] ?? 0);
    $origen = $_POST['origen'] ?? '';

    // Validaciones financieras básicas
    if ($abono > 0 && $cargo > 0) {
        die("No puede registrar abono y cargo al mismo tiempo.");
    }

    if ($abono <= 0 && $cargo <= 0) {
        die("Debe registrar un abono o un cargo.");
    }

    $consulta = $conexionBD->prepare("
        INSERT INTO movimientos_financieros
        (fecha, id_propiedad, id_tipo_operacion, nota, abono, cargo, origen)
        VALUES
        (:fecha, :id_propiedad, :id_tipo_operacion, :nota, :abono, :cargo, :origen)
    ");

    $consulta->execute([
        ':fecha' => $fecha,
        ':id_propiedad' => $id_propiedad,
        ':id_tipo_operacion' => $id_tipo_operacion,
        ':nota' => $nota,
        ':abono' => $abono,
        ':cargo' => $cargo,
        ':origen' => $origen
    ]);

    header("Location:index.php");
    exit();
}

?>