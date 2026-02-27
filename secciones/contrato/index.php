<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'contratos', 'ver');

$consulta = $conexionBD->prepare("SELECT
    r.id_contrato,
    l.codigo AS local,
    a.nombre AS arrendatario,
    r.renta,
    r.deposito,
    r.adicional,
    r.fecha_inicio,
    r.fecha_fin,
    r.estatus,
    r.duracion,

    TIMESTAMPDIFF(MONTH, r.fecha_inicio, CURDATE()) AS antiguedad_meses,

    (
        SELECT IFNULL(SUM(p.monto),0)
        FROM pagos p
        WHERE p.id_contrato = r.id_contrato
        AND p.estatus IN ('Pendiente','Vencido')
    ) AS deuda_total

FROM contratos r
INNER JOIN locales l ON l.id_local = r.id_local
INNER JOIN arrendatarios a ON a.id_arrendatario = r.id_arrendatario
ORDER BY r.id_contrato DESC
");

$consulta->execute();
$listaRentas = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">

            <?php if (tienePermiso($conexionBD, $idRol, 'contratos', 'crear')): ?>
                <a class="btn btn-success" href="crear.php">
                    + Nuevo contrato
                </a>
            <?php endif; ?>

        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">

                        <tr>
                            <th>Local</th>
                            <th>Arrendatario</th>
                            <th>Renta</th>
                            <th>Depósito</th>
                            <th>Adicional</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Antigüedad</th>
                            <th>Estatus</th>
                            <th>Duración</th>
                            <th>Deuda</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($listaRentas as $value): ?>
                            <tr>

                                <td><?= htmlspecialchars($value['local']) ?></td>
                                <td><?= htmlspecialchars($value['arrendatario']) ?></td>
                                <td>$<?= number_format($value['renta'], 2) ?></td>
                                <td>$<?= number_format($value['deposito'], 2) ?></td>
                                <td>$<?= number_format($value['adicional'], 2) ?></td>
                                <td><?= date('d/m/Y', strtotime($value['fecha_inicio'])) ?></td>

                                <!-- FECHA FIN -->
                                <td>
                                    <?php if (!empty($value['fecha_fin'])): ?>
                                        <?= date('d/m/Y', strtotime($value['fecha_fin'])) ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>

                                <!-- ANTIGUEDAD -->

                                <td>
                                    <?php
                                    $inicio = new DateTime($value['fecha_inicio']);
                                    $hoy = new DateTime();
                                    $diff = $inicio->diff($hoy);

                                    echo $diff->y . " años, " . $diff->m . " meses";
                                    ?>
                                </td>

                                <!-- ESTATUS -->
                                <td>
                                    <?php
                                    $hoy = date('Y-m-d');
                                    $duracion = $value['duracion'] ?? 'Fijo';

                                    switch ($value['estatus']) {

                                        case 'Finalizada':
                                            echo '<span class="badge bg-secondary">Finalizada</span>';
                                            break;

                                        case 'Cancelada':
                                            echo '<span class="badge bg-dark">Cancelada</span>';
                                            break;

                                        case 'Pendiente':
                                            echo '<span class="badge bg-warning text-dark">Pendiente</span>';
                                            break;

                                        case 'Activa':

                                            if ($duracion === 'Indefinido') {
                                                echo '<span class="badge bg-primary">Activa (Indefinida)</span>';
                                            } elseif (!empty($value['fecha_fin']) && $value['fecha_fin'] < $hoy) {
                                                echo '<span class="badge bg-danger">Vencida</span>';
                                            } elseif (!empty($value['fecha_fin']) && $value['fecha_fin'] <= date('Y-m-d', strtotime('+5 days'))) {
                                                echo '<span class="badge bg-warning text-dark">Por vencer</span>';
                                            } else {
                                                echo '<span class="badge bg-success">Activa</span>';
                                            }

                                            break;
                                    }
                                    ?>
                                </td>

                                <!-- DURACION -->
                                <td>
                                    <?php if ($duracion === 'Indefinido'): ?>
                                        <span class="badge bg-info">Indefinido</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Plazo fijo</span>
                                    <?php endif; ?>
                                </td>

                                <!-- DEUDA -->
                                <td>
                                    <?php if ($value['deuda_total'] > 0): ?>
                                        <span class="badge bg-danger">
                                            $<?= number_format($value['deuda_total'], 2); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success">$0.00</span>
                                    <?php endif; ?>
                                </td>

                                <!-- ACCIONES -->
                                <td>

                                    <?php if (tienePermiso($conexionBD, $idRol, 'contratos', 'editar')): ?>
                                        <a class="btn btn-primary btn-sm"
                                            href="editar.php?txtID=<?= (int)$value['id_contrato']; ?>">
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if (tienePermiso($conexionBD, $idRol, 'contratos', 'eliminar')): ?>
                                        <form action="eliminar.php" method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                                            <input type="hidden" name="txtID" value="<?= (int)$value['id_contrato']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Seguro que deseas eliminar este contrato?');">
                                                Borrar
                                            </button>
                                        </form>
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