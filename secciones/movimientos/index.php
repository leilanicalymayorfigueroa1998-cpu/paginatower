<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once('../../app/services/MovimientoService.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'movimientos', 'ver');

$service = new MovimientoService($conexionBD);
$listaMovimientos = $service->obtenerTodos();

// Permisos botones
$puedeCrear    = tienePermiso($conexionBD, $idRol, 'movimientos', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'movimientos', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'movimientos', 'eliminar');

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>


<div class="content">


    <div class="card">
        <div class="card-header">
            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php">Agregar</a>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div
                class="table-responsive">
                <table
                    class="table">
                    <thead>

                        <tr>
                            <th>Fecha</th>
                            <th>Propiedad</th>
                            <th>Operacion</th>
                            <th>Nota</th>
                            <th>Abono</th>
                            <th>Cargo</th>
                            <th>Origen</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>
                        <?php foreach ($listaMovimientos as $mov): ?>
                            <tr>
                                <td><?= htmlspecialchars($mov['fecha']) ?></td>
                                <td><?= htmlspecialchars($mov['propiedad']) ?></td>
                                <td><?= htmlspecialchars($mov['tipo_codigo']) ?></td>
                                <td><?= htmlspecialchars($mov['nota']) ?></td>
                                <td class="text-success"><?= number_format($mov['abono'], 2) ?></td>
                                <td class="text-danger"><?= number_format($mov['cargo'], 2) ?></td>
                                <td><?= htmlspecialchars($mov['origen']) ?></td>
                                <td>
                                    <?php if ($puedeEditar): ?>
                                        <a class="btn btn-primary btn-sm"
                                            href="editar.php?txtID=<?= $mov['id_movimiento'] ?>">
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($puedeEliminar): ?>
                                        <a class="btn btn-danger btn-sm"
                                            href="eliminar.php?txtID=<?= $mov['id_movimiento'] ?>"
                                            onclick="return confirm('¿Seguro que deseas eliminar este movimiento?')">
                                            Borrar
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>