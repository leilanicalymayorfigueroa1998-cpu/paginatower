<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/RestriccionService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'restricciones', 'ver');

$service = new RestriccionService($conexionBD);
$listaRestricciones = $service->obtenerTodas();

// Permisos para botones
$puedeCrear    = tienePermiso($conexionBD, $_SESSION['id_rol'], 'restricciones', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $_SESSION['id_rol'], 'restricciones', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $_SESSION['id_rol'], 'restricciones', 'eliminar');

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">Restricciones</h5>

            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php">+ Nueva Restriccion</a>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Local</th>
                            <th>Restricción</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($listaRestricciones as $value): ?>
                            <tr>

                                <td><?= htmlspecialchars($value['codigo']) ?></td>

                                <td><?= htmlspecialchars($value['restriccion']) ?></td>

                                <td class="text-center">

                                    <?php if ($puedeEditar): ?>
                                        <a class="btn btn-primary btn-sm"
                                            href="editar.php?txtID=<?= $value['id_restriccion']; ?>">
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($puedeEliminar): ?>
                                        <a class="btn btn-danger btn-sm"
                                            href="eliminar.php?txtID=<?= $value['id_restriccion']; ?>"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta restricción?');">
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

    <?php include('../../templates/pie.php'); ?>