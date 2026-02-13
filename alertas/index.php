<?php

include("../bd.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/* ===========================
   Marcar una como leída
=========================== */
if (isset($_GET['leer'])) {

    $id_alerta = $_GET['leer'];

    $update = $conexionBD->prepare(" UPDATE alertas 
        SET leida = 1 
        WHERE id = :id 
        AND id_usuario = :usuario");

    $update->bindParam(':id', $id_alerta);
    $update->bindParam(':usuario', $_SESSION['id']);
    $update->execute();

    header("Location: index.php");
    exit();
}

/* ===========================
   Marcar todas como leídas
=========================== */
if (isset($_GET['leer_todas'])) {

    $update = $conexionBD->prepare("UPDATE alertas 
        SET leida = 1 
        WHERE id_usuario = :usuario");

    $update->bindParam(':usuario', $_SESSION['id']);
    $update->execute();

    header("Location: index.php");
    exit();
}

/* ===========================
   Obtener alertas del usuario
=========================== */
$consulta = $conexionBD->prepare("SELECT * FROM alertas 
    WHERE id_usuario = :id
    ORDER BY fecha DESC");

$consulta->bindParam(':id', $_SESSION['id']);
$consulta->execute();
$alertas = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<h3 class="mb-3">Mis Alertas</h3>

<a href="?leer_todas=1" class="btn btn-sm btn-primary mb-3">
    Marcar todas como leídas
</a>

<?php if (count($alertas) > 0): ?>

    <?php foreach ($alertas as $alerta): ?>
        <div class="card p-3 mb-2 
            <?php echo $alerta['leida'] ? '' : 'border-start border-4 border-primary'; ?>">

            <div class="d-flex justify-content-between">
                <div>
                    <strong><?php echo htmlspecialchars($alerta['titulo']); ?></strong><br>
                    <?php echo htmlspecialchars($alerta['mensaje']); ?><br>
                    <small class="text-muted"><?php echo $alerta['fecha']; ?></small>
                </div>

                <?php if (!$alerta['leida']): ?>
                    <a href="?leer=<?php echo $alerta['id']; ?>"
                        class="btn btn-sm btn-outline-primary">
                        Marcar como leída
                    </a>
                <?php endif; ?>
            </div>

        </div>
    <?php endforeach; ?>

<?php else: ?>

    <div class="alert alert-info">
        No tienes alertas por el momento.
    </div>

<?php endif; ?>