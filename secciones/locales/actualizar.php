<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once('../../app/services/LocalService.php');

$idRol = $_SESSION['id_rol'] ?? null;

verificarPermiso($conexionBD, $idRol, 'locales', 'editar');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $id = $_POST['txtID'] ?? null;

    if (!$id || !is_numeric($id)) {
        header("Location: index.php");
        exit();
    }

    $service = new LocalService($conexionBD);

    try {

        $service->actualizar($id, [
            'id_propiedad' => $_POST['id_propiedad'] ?? '',
            'codigo' => trim($_POST['codigo'] ?? ''),
            'medidas' => trim($_POST['medidas'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'estacionamiento' => trim($_POST['estacionamiento'] ?? ''),
            'estatus' => $_POST['estatus'] ?? ''
        ]);

        header("Location:index.php");
        exit();

    } catch (Exception $e) {
        die($e->getMessage());
    }
}

?>