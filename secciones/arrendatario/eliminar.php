<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'arrendatarios', 'eliminar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

try {

    $consulta = $conexionBD->prepare("
        DELETE FROM arrendatarios
        WHERE id_arrendatario = :id
    ");

    $consulta->execute([':id' => $id]);

} catch (PDOException $e) {
    die("Error al eliminar el arrendatario.");
}

header("Location: index.php");
exit();

?>