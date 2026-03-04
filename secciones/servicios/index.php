<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/ServiciosService.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

// 🔐 Permiso para ver módulo
verificarPermiso($conexionBD, $idRol, 'servicios', 'ver');

$service = new ServiciosService($conexionBD);
$lista_servicios = $service->obtenerTodos();

// 🔐 Permisos botones
$puedeCrear    = tienePermiso($conexionBD, $idRol, 'servicios', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'servicios', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'servicios', 'eliminar');

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">

        <div class="card-header">
            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php">+ Nuevo Servicio</a>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Local</th>
                            <th>CFE</th>
                            <th>Numero CFE</th>
                            <th>Agua</th>
                            <th>Contrato Agua</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($lista_servicios as $value): ?>
                            <tr>

                                <td><?= htmlspecialchars($value['codigo']); ?></td>

                                <td>
                                    <?= $value['cfe']
                                        ? '<span class="badge bg-success">Activo</span>'
                                        : '<span class="badge bg-danger">Inactivo</span>'; ?>
                                </td>

                                <td><?= htmlspecialchars($value['contrato_cfe']); ?></td>

                                <td>
                                    <?= $value['agua']
                                        ? '<span class="badge bg-success">Activo</span>'
                                        : '<span class="badge bg-danger">Inactivo</span>'; ?>
                                </td>

                                <td><?= htmlspecialchars($value['contrato_agua']); ?></td>

                                <td class="text-center">

                                    <?php if ($puedeEditar): ?>
                                        <a class="btn btn-primary btn-sm"
                                            href="editar.php?txtID=<?= $value['id_servicio']; ?>">
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($puedeEliminar): ?>
                                        <a class="btn btn-danger btn-sm"
                                            href="eliminar.php?txtID=<?= $value['id_servicio']; ?>"
                                            onclick="return confirm('¿Seguro que deseas eliminar este servicio?');">
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