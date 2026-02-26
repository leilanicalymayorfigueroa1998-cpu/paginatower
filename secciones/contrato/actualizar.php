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
        'estatus' => $_POST['estatus'],
        'duracion' => $duracion
    ];

    // 🔹 Calcular fecha_fin automáticamente
    if ($duracion === 'indefinido') {

        $data['fecha_fin'] = null;

    } elseif ($duracion === '6') {

        $data['fecha_fin'] = date('Y-m-d', strtotime('+6 months', strtotime($fecha_inicio)));

    } elseif ($duracion === '12') {

        $data['fecha_fin'] = date('Y-m-d', strtotime('+12 months', strtotime($fecha_inicio)));

    } else {
        throw new Exception("Duración inválida.");
    }

    $service->actualizarContrato($id_contrato, $data);

    header("Location: index.php");
    exit();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>