<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PagosService.php');

$idRol = $_SESSION['id_rol'] ?? null;
if (!$idRol) { header("Location: ../../login.php"); exit(); }

$puedeCrear    = tienePermiso($conexionBD, $idRol, 'pagos', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'pagos', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'pagos', 'eliminar');

// Un registro por inmueble con info del mes actual
$consulta = $conexionBD->prepare("
    SELECT
        r.id_contrato,
        l.codigo                        AS contrato,
        r.renta,
        r.deuda,

        -- Último pago realizado
        MAX(p.fecha_pago)               AS ultimo_pago,

        -- Estatus del mes actual
        (
            SELECT estatus FROM pagos
            WHERE id_contrato = r.id_contrato
              AND periodo = DATE_FORMAT(CURDATE(), '%Y-%m-01')
            LIMIT 1
        )                               AS estatus_mes,

        -- Estatus global del contrato
        CASE
            WHEN SUM(CASE WHEN p.estatus = 'Vencido'  THEN 1 ELSE 0 END) > 0 THEN 'Vencido'
            WHEN SUM(CASE WHEN p.estatus = 'Pendiente' THEN 1 ELSE 0 END) > 0 THEN 'Pendiente'
            WHEN COUNT(p.id_pago) = 0 THEN 'Sin pagos'
            ELSE 'Al corriente'
        END                             AS estatus_global

    FROM contratos r
    INNER JOIN locales l   ON r.id_local    = l.id_local
    LEFT  JOIN pagos   p   ON p.id_contrato = r.id_contrato

    WHERE r.estatus = 'Activa'

    GROUP BY r.id_contrato, l.codigo, r.renta, r.deuda
    ORDER BY l.codigo
");
$consulta->execute();
$listaPagos = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pagos</h5>
            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php">+ Nuevo Pago</a>
            <?php endif; ?>
        </div>

        <div class="card-body">

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'creado'): ?>
                <div class="alert alert-success">Pago registrado correctamente.</div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Inmueble</th>
                            <th class="text-end">Renta</th>
                            <th class="text-end">Deuda</th>
                            <th>Último pago</th>
                            <th>Mes actual</th>
                            <th>Estatus general</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaPagos)): ?>
                            <tr><td colspan="7" class="text-center">No hay registros</td></tr>
                        <?php else: ?>
                            <?php foreach ($listaPagos as $v): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($v['contrato']) ?></td>

                                <td class="text-end">$<?= number_format($v['renta'], 2) ?></td>

                                <td class="text-end <?= $v['deuda'] > 0 ? 'text-danger fw-bold' : 'text-success' ?>">
                                    $<?= number_format($v['deuda'], 2) ?>
                                </td>

                                <td>
                                    <?= $v['ultimo_pago'] ? date('d/m/Y', strtotime($v['ultimo_pago'])) : '<span class="text-muted">Sin pagos</span>' ?>
                                </td>

                                <td>
                                    <?php
                                    $em = $v['estatus_mes'];
                                    if ($em === 'Pagado'):
                                    ?><span class="badge bg-success">Pagado</span>
                                    <?php elseif ($em === 'Pendiente'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php elseif ($em === 'Vencido'): ?>
                                        <span class="badge bg-danger">Vencido</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin registro</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php
                                    $eg = $v['estatus_global'];
                                    if ($eg === 'Al corriente'):
                                    ?><span class="badge bg-success">Al corriente</span>
                                    <?php elseif ($eg === 'Pendiente'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php elseif ($eg === 'Vencido'): ?>
                                        <span class="badge bg-danger">Vencido</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin pagos</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="detalle_pagos.php?contrato=<?= urlencode($v['contrato']) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        Ver pagos
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>
