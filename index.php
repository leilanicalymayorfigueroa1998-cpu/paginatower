<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include('templates/cabecera.php');
?>

<div class="mb-4">
    <h2 class="fw-semibold">Panel de Control</h2>
    <p class="text-muted">
        Bienvenida, <?php echo $_SESSION['usuario']; ?>.
        Gestiona las operaciones del sistema desde este panel.
    </p>
</div>

<?php include('templates/pie.php'); ?>