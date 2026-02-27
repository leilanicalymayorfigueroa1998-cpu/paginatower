<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once('../../app/services/MovimientoService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos', 'editar');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $id = $_POST['txtID'] ?? null;

    if (!$id || !is_numeric($id)) {
        header("Location:index.php");
        exit();
    }

    $service = new MovimientoService($conexionBD);

    try {

        $service->actualizar($id, [
            'fecha' => $_POST['fecha'] ?? '',
            'id_propiedad' => $_POST['id_propiedad'] ?? '',
            'id_tipo_operacion' => $_POST['id_tipo_operacion'] ?? '',
            'nota' => trim($_POST['nota'] ?? ''),
            'abono' => floatval($_POST['abono'] ?? 0),
            'cargo' => floatval($_POST['cargo'] ?? 0),
            'origen' => $_POST['origen'] ?? ''
        ]);

        header("Location:index.php");
        exit();

    } catch (Exception $e) {
        die($e->getMessage());
    }
}