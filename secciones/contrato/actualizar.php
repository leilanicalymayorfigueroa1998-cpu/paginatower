<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

require_once __DIR__ . '/../../services/ContratoService.php';

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'contratos', 'editar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$id_contrato = (int) ($_POST['txtID'] ?? 0);

if ($id_contrato <= 0) {
    header("Location: index.php");
    exit();
}

$service = new ContratoService($conexionBD);

try {

    $duracion = $_POST['duracion'] ?? '6'; // valor por defecto

    $fecha_inicio = $_POST['fecha_inicio'];

    $data = [
        'id_local' => (int) $_POST['id_local'],
        'id_arrendatario' => (int) $_POST['id_arrendatario'],
        'renta' => (float) $_POST['renta'],
        'deposito' => isset($_POST['deposito']) ? (float) $_POST['deposito'] : 0,
        'adicional' => isset($_POST['adicional']) ? (float) $_POST['adicional'] : 0,
        'fecha_inicio' => $fecha_inicio,
        'estatus'      => $_POST['estatus'],
        'duracion'     => $duracion,
        'dia_pago'     => isset($_POST['dia_pago']) ? max(1, min(28, (int)$_POST['dia_pago'])) : 1,
    ];

    // 🔹 Manejo correcto de fecha_fin según duracion
    if ($duracion === 'Indefinido') {
        $data['fecha_fin'] = null;
    } else {
        // Fijo: requiere fecha_fin enviada desde el formulario
        if (empty($_POST['fecha_fin'])) {
            throw new Exception("Debe seleccionar fecha fin para contratos fijos.");
        }
        $data['fecha_fin'] = $_POST['fecha_fin'];
    }

    $service->actualizarContrato($id_contrato, $data);

    header("Location: index.php");
    exit();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>