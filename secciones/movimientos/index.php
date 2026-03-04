<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/MovimientoService.php';

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

// Verificar permiso
verificarPermiso($conexionBD, $idRol, 'movimientos_financieros', 'ver');

$service = new MovimientoService($conexionBD);
$listaMovimientos = $service->obtenerTodos();

// Permisos
$puedeCrear    = tienePermiso($conexionBD, $idRol, 'movimientos_financieros', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'movimientos_financieros', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'movimientos_financieros', 'eliminar');

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">Movimientos Financieros</h5>

            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php">
                    Agregar
                </a>
            <?php endif; ?>

        </div>

        <div class="card-body">

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'guardado'): ?>
                <div class="alert alert-success">
                    Movimiento guardado correctamente.
                </div>
            <?php endif; ?>

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Fecha</th>
                            <th>Propiedad</th>
                            <th>Operación</th>
                            <th>Nota</th>
                            <th class="text-end">Abono</th>
                            <th class="text-end">Cargo</th>
                            <th>Origen</th>
                            <th width="160">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($listaMovimientos as $mov): ?>

                            <tr>

                                <td><?= htmlspecialchars($mov['fecha']) ?></td>

                                <td><?= htmlspecialchars($mov['propiedad']) ?></td>

                                <td><?= htmlspecialchars($mov['tipo_codigo']) ?></td>

                                <td><?= htmlspecialchars($mov['nota']) ?></td>

                                <td class="text-success fw-bold text-end">
                                    $<?= number_format($mov['abono'] ?? 0, 2) ?>
                                </td>

                                <td class="text-danger fw-bold text-end">
                                    $<?= number_format($mov['cargo'] ?? 0, 2) ?>
                                </td>

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