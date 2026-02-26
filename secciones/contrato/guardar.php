<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/ContratoService.php';

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'contratos', 'crear');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 🔐 Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

// 🔎 Validar obligatorios básicos
$camposRequeridos = [
    'id_local',
    'id_arrendatario',
    'renta',
    'fecha_inicio',
    'estatus'
];

foreach ($camposRequeridos as $campo) {
    if (empty($_POST[$campo])) {
        die("El campo {$campo} es obligatorio.");
    }
}

// 🔎 Manejo de duración
$duracion = $_POST['duracion'] ?? 'Fijo';

// 🔄 Sanitizar y tipar datos
$data = [
    'id_local' => (int) $_POST['id_local'],
    'id_arrendatario' => (int) $_POST['id_arrendatario'],
    'renta' => (float) $_POST['renta'],
    'deposito' => isset($_POST['deposito']) ? (float) $_POST['deposito'] : 0,
    'adicional' => isset($_POST['adicional']) ? (float) $_POST['adicional'] : 0,
    'fecha_inicio' => $_POST['fecha_inicio'],
    'estatus' => $_POST['estatus']
];

// 🔹 Manejo correcto de fecha_fin
if ($duracion === 'Indefinido') {

    $data['fecha_fin'] = null;

} else {

    if (empty($_POST['fecha_fin'])) {
        die("Debe seleccionar fecha fin para contratos fijos.");
    }

    $data['fecha_fin'] = $_POST['fecha_fin'];
}

$service = new ContratoService($conexionBD);

try {

    $service->crearContrato($data);

    header("Location: index.php");
    exit();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>