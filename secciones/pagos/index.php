<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PagosService.php');

/* =========================
   Verificar sesión
========================= */
$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

/* =========================
   Permisos
========================= */
$puedeCrear    = tienePermiso($conexionBD, $idRol, 'pagos', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'pagos', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'pagos', 'eliminar');

/* =========================
   Consulta pagos
========================= */
$consulta = $conexionBD->prepare("
SELECT 
    l.codigo AS contrato,
    MAX(p.fecha_pago) AS fecha_pago,
    COALESCE(SUM(p.monto),0) AS monto,
    
    CASE 
        WHEN SUM(CASE WHEN p.estatus = 'Vencido' THEN 1 ELSE 0 END) > 0 THEN 'Vencido'
        WHEN SUM(CASE WHEN p.estatus = 'Pendiente' THEN 1 ELSE 0 END) > 0 THEN 'Pendiente'
        WHEN COUNT(p.id_pago) = 0 THEN 'Sin pagos'
        ELSE 'Pagado'
    END AS estatus

FROM contratos r

INNER JOIN locales l 
    ON r.id_local = l.id_local

LEFT JOIN pagos p 
    ON p.id_contrato = r.id_contrato

GROUP BY r.id_contrato, l.codigo

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
                <a class="btn btn-success" href="crear.php">
                    + Nuevo Pago
                </a>
            <?php endif; ?>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover">

                    <thead class="table-dark">

                        <tr>
                            <th>Contrato</th>
                            <th>Último pago</th>
                            <th>Total rentas</th>
                            <th>Estatus</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($listaPagos)): ?>

                            <tr>
                                <td colspan="4" class="text-center">
                                    No hay registros
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($listaPagos as $value): ?>

                                <tr>

                                    <td>
                                        <a href="detalle_pagos.php?contrato=<?= urlencode($value['contrato']) ?>">
                                            <?= htmlspecialchars($value['contrato']) ?>
                                        </a>
                                    </td>

                                    <td>

                                        <?php
                                        if (!empty($value['fecha_pago'])) {
                                            echo date('d/m/Y', strtotime($value['fecha_pago']));
                                        } else {
                                            echo "Sin pagos";
                                        }
                                        ?>

                                    </td>

                                    <td>$<?= number_format($value['monto'], 2) ?></td>

                                    <td>

                                        <?php if ($value['estatus'] == 'Pagado'): ?>

                                            <span class="badge bg-success">Pagado</span>

                                        <?php elseif ($value['estatus'] == 'Pendiente'): ?>

                                            <span class="badge bg-warning text-dark">Pendiente</span>

                                        <?php elseif ($value['estatus'] == 'Vencido'): ?>

                                            <span class="badge bg-danger">Vencido</span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">Sin pagos</span>

                                        <?php endif; ?>

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