<?php
include('includes/auth.php');
include('includes/helpers.php');
include('bd.php');

$rol = $_SESSION['rol'] ?? '';
$color = obtenerColorRol($rol);

$idUsuario = $_SESSION['id'] ?? null;
$totalAlertas = 0;

if ($idUsuario) {
    $consulta = $conexionBD->prepare("
        SELECT COUNT(*) 
        FROM alertas 
        WHERE id_usuario = :id 
        AND leida = 0
    ");
    $consulta->bindParam(':id', $idUsuario);
    $consulta->execute();
    $totalAlertas = $consulta->fetchColumn();
}

include('templates/cabecera.php');
include('templates/topbar.php');
include('templates/sidebar.php');
?>

<div class="content">
    <div class="container-fluid">
        <h2>Panel de Control</h2>
    </div>
</div>

<?php include('templates/pie.php'); ?>
