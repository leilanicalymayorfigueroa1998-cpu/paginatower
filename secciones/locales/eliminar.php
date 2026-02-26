<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once('../../app/services/LocalService.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

// 🔐 Verificar permiso eliminar
verificarPermiso($conexionBD, $idRol, 'locales', 'eliminar');

$id = $_GET['txtID'] ?? null;

// 🛡 Validar ID
if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit();
}

$service = new LocalService($conexionBD);
$service->eliminar($id);

header("Location: index.php");
exit();
?>

?>