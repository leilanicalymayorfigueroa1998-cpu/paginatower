<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PropiedadService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'ver');

$service = new PropiedadService($conexionBD);
$listaPropiedades = $service->obtenerTodos();

// Permisos para botones
$puedeCrear    = tienePermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'eliminar');

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">

        <?php if (isset($_GET['mensaje'])): ?>

            <?php if ($_GET['mensaje'] == "creado"): ?>
                <div class="alert alert-success">Propiedad creada correctamente ✅</div>
            <?php endif; ?>

            <?php if ($_GET['mensaje'] == "editado"): ?>
                <div class="alert alert-primary">Propiedad actualizada ✏️</div>
            <?php endif; ?>

            <?php if ($_GET['mensaje'] == "eliminado"): ?>
                <div class="alert alert-danger">Propiedad eliminada 🗑</div>
            <?php endif; ?>

        <?php endif; ?>

        <div class="card-header">
            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php">Agregar</a>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Dueño</th>
                            <th>Tipo</th>
                            <th>Dirección</th>
                            <th>Latitud</th>
                            <th>Longitud</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($listaPropiedades as $value): ?>
                            <tr>

                                <td><?= htmlspecialchars($value['codigo']) ?></td>

                                <td><?= htmlspecialchars($value['dueno']) ?></td>

                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($value['tipo']) ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($value['direccion']) ?></td>

                                <td><?= htmlspecialchars($value['latitud']) ?></td>

                                <td><?= htmlspecialchars($value['longitud']) ?></td>

                                <td class="text-center">

                                    <?php if ($puedeEditar): ?>
                                        <a class="btn btn-primary btn-sm"
                                            href="editar.php?txtID=<?= $value['id_propiedad']; ?>">
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($puedeEliminar): ?>
                                        <a class="btn btn-danger btn-sm"
                                            href="eliminar.php?txtID=<?= $value['id_propiedad']; ?>"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta propiedad?');">
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