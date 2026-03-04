<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos_financieros', 'crear');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar token CSRF
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    // Obtener datos del formulario
    $fecha = $_POST['fecha'] ?? '';
    $id_propiedad = $_POST['id_propiedad'] ?? '';
    $id_tipo_operacion = $_POST['id_tipo_operacion'] ?? '';
    $nota = trim($_POST['nota'] ?? '');
    $abono = floatval($_POST['abono'] ?? 0);
    $cargo = floatval($_POST['cargo'] ?? 0);
    $origen = $_POST['origen'] ?? '';

    // Validar campos obligatorios
    if (!$fecha || !$id_propiedad || !$id_tipo_operacion || !$origen) {
        die("Faltan datos obligatorios.");
    }

    // Validaciones financieras
    if ($abono > 0 && $cargo > 0) {
        die("No puede registrar abono y cargo al mismo tiempo.");
    }

    if ($abono <= 0 && $cargo <= 0) {
        die("Debe registrar un abono o un cargo.");
    }

    try {

        // Insertar movimiento financiero
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

        // Descontar deuda del contrato si es abono
        if ($abono > 0) {

            $actualizar = $conexionBD->prepare("
        UPDATE contratos
        SET deuda = GREATEST(deuda - :abono,0)
        WHERE id_local = :id_propiedad
        AND estatus = 'Activa'
    ");

            $actualizar->execute([
                ':abono' => $abono,
                ':id_propiedad' => $id_propiedad
            ]);
        }

        // Redirigir
        header("Location: index.php?msg=guardado");
        exit();
    } catch (Exception $e) {

        die("Error al guardar el movimiento financiero.");
    }
}
