<?php
include('../../includes/auth.php');
include('../../bd.php');
require_once('../../app/services/LocalService.php');

$service = new LocalService($conexionBD);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        'id_propiedad' => $_POST['id_propiedad'] ?? '',
        'codigo' => $_POST['codigo'] ?? '',
        'medidas' => $_POST['medidas'] ?? '',
        'descripcion' => $_POST['descripcion'] ?? '',
        'estacionamiento' => $_POST['estacionamiento'] ?? '',
        'estatus' => $_POST['estatus'] ?? ''
    ];

    $service->crear($data);

    header("Location: index.php");
    exit();
}

?>