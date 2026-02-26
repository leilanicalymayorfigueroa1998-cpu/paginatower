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

verificarPermiso($conexionBD, $idRol, 'duenos', 'ver');

$puedeCrear    = tienePermiso($conexionBD, $idRol, 'duenos', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'duenos', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'duenos', 'eliminar');

$consulta = $conexionBD->prepare("SELECT 
        id_dueno,
        nombre,
        telefono,
        correo
    FROM duenos
    ORDER BY id_dueno DESC");

$consulta->execute();
$listaDuenos = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">

            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php">
                    + Nuevo Dueño
                </a>
            <?php endif; ?>

        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($listaDuenos as $value): ?>
                            <tr>
                                <td><?= htmlspecialchars($value['nombre']) ?></td>
                                <td><?= htmlspecialchars($value['telefono']) ?></td>
                                <td><?= htmlspecialchars($value['correo']) ?></td>

                                <td>

                                    <?php if ($puedeEditar): ?>
                                        <a class="btn btn-primary btn-sm"
                                            href="editar.php?txtID=<?= htmlspecialchars($value['id_dueno']) ?>">
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($puedeEliminar): ?>
                                        <form method="POST" action="eliminar.php" style="display:inline;">
                                            <input type="hidden" name="id"
                                                value="<?= htmlspecialchars($value['id_dueno']) ?>">
                                            <input type="hidden" name="csrf_token"
                                                value="<?= generarTokenCSRF(); ?>">
                                            <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar este dueño?');">
                                                Borrar
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </td>

                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($listaDuenos)): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    No hay dueños registrados.
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>