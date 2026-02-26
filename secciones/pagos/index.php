<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

$idRol = $_SESSION['id_rol'] ?? null;

$puedeCrear   = false;
$puedeEditar  = false;
$puedeEliminar = false;

if ($idRol) {
    $puedeCrear    = tienePermiso($conexionBD, $idRol, 'pagos', 'crear');
    $puedeEditar   = tienePermiso($conexionBD, $idRol, 'pagos', 'editar');
    $puedeEliminar = tienePermiso($conexionBD, $idRol, 'pagos', 'eliminar');
}

$consulta = $conexionBD->prepare("
    SELECT p.id_pago,
           l.codigo AS contrato,
           p.fecha_pago,
           p.monto,
           p.metodo_pago,
           p.estatus
    FROM pagos p
    INNER JOIN contratos r ON p.id_contrato = r.id_contrato
    INNER JOIN locales l ON r.id_local = l.id_local
    ORDER BY p.id_pago DESC
");

$consulta->execute();
$listaPagos = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">

            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php">
                    + Nuevo Pago
                </a>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div
                class="table-responsive-sm">
                <table
                    class="table">
                    <thead>
                        <tr>

                            <th>Renta</th>
                            <th>Fecha pago</th>
                            <th>Monto</th>
                            <th>Metodo Pago</th>
                            <th>Estatus</th>
                            <th>Acciones</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($listaPagos as $key => $value) { ?>
                            <tr class="">
                                <td><?php echo $value['contrato'] ?></td>
                                <td><?php echo $value['fecha_pago'] ?></td>
                                <td><?php echo $value['monto'] ?></td>
                                <td><?php echo $value['metodo_pago'] ?></td>
                                <td>
                                    <?php if ($value['estatus'] == 'Pagado') { ?>

                                        <span class="badge bg-success">Pagado</span>

                                    <?php } elseif ($value['estatus'] == 'Pendiente') { ?>

                                        <span class="badge bg-warning text-dark">Pendiente</span>

                                    <?php } elseif ($value['estatus'] == 'Vencido') { ?>

                                        <span class="badge bg-danger">Vencido</span>

                                    <?php } else { ?>

                                        <span class="badge bg-secondary">Cancelado</span>

                                    <?php } ?>
                                </td>



                                <td>

                                    <?php if ($puedeEditar): ?>
                                        <a class="btn btn-primary btn-sm"
                                            href="editar.php?txtID=<?= htmlspecialchars($value['id_pago']) ?>">
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($puedeEliminar): ?>
                                        <form action="eliminar.php" method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                                            <input type="hidden" name="txtID" value="<?= $value['id_pago']; ?>">
                                            <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Seguro que deseas eliminar este pago?');">
                                                Borrar
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </td>


                            </tr>

                        <?php   } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>