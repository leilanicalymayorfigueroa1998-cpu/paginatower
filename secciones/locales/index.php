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

// 🔐 Permiso para ver módulo
verificarPermiso($conexionBD, $idRol, 'locales', 'ver');

$service = new LocalService($conexionBD);
$listaLocales = $service->obtenerTodos();

// 🔎 Permisos para botones
$puedeCrear    = tienePermiso($conexionBD, $idRol, 'locales', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'locales', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'locales', 'eliminar');

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
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">

                    <tr>

                        <th>Propiedad</th>
                        <th>Codigo</th>
                        <th>Medidas</th>
                        <th>Descripcion</th>
                        <th>Estacionamiento</th>
                        <th>Estatus</th>
                        <th style="width: 120px;">Acciones</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($listaLocales as $value) { ?>
                        <tr>
                            <td><?= $value['propiedad']; ?></td>
                            <td><?= $value['codigo']; ?></td>
                            <td><?= $value['medidas']; ?></td>
                            <td><?= $value['descripcion']; ?></td>
                            <td><?= $value['estacionamiento']; ?></td>
                            <td>
                                <?php if ($value['estatus'] === 'Disponible'): ?>
                                    <span class="badge bg-success">Disponible</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Ocupado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($puedeEditar): ?>
                                    <a class="btn btn-primary btn-sm"
                                        href="editar.php?txtID=<?= $value['id_local']; ?>">
                                        Editar
                                    </a>
                                <?php endif; ?>

                                <?php if ($puedeEliminar): ?>
                                    <a class="btn btn-danger btn-sm"
                                        href="eliminar.php?txtID=<?= $value['id_local']; ?>"
                                        onclick="return confirm('¿Seguro que deseas eliminar este local?')">
                                        Borrar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>

    </div>
</div>
</div>

<?php include('../../templates/pie.php'); ?>