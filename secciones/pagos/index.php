<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PagosService.php');

$idRol = $_SESSION['id_rol'] ?? null;
if (!$idRol) { header("Location: ../../login.php"); exit(); }

$puedeCrear = tienePermiso($conexionBD, $idRol, 'pagos', 'crear');

/* =====================================================
   Marcar pagos vencidos automáticamente
===================================================== */
$conexionBD->prepare("
    UPDATE pagos SET estatus = 'Vencido'
    WHERE estatus = 'Pendiente' AND fecha_pago < CURDATE()
")->execute();

$periodo = date('Y-m-01');

/* =====================================================
   Un renglón por contrato activo con info del mes
===================================================== */
$consulta = $conexionBD->prepare("
    SELECT
        c.id_contrato,
        c.duracion,
        c.dia_pago,
        c.renta,
        l.codigo       AS local,
        a.nombre       AS arrendatario,
        pm.id_pago     AS pago_mes_id,
        pm.fecha_pago  AS fecha_pago_mes,
        pm.monto       AS monto_mes,
        pm.metodo_pago AS metodo_mes,
        pm.estatus     AS estatus_mes,
        COUNT(ph.id_pago)                                          AS total_pagos,
        SUM(CASE WHEN ph.estatus='Pagado'    THEN 1 ELSE 0 END)   AS pagados,
        SUM(CASE WHEN ph.estatus='Pendiente' THEN 1 ELSE 0 END)   AS pendientes,
        SUM(CASE WHEN ph.estatus='Vencido'   THEN 1 ELSE 0 END)   AS vencidos
    FROM contratos c
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    LEFT  JOIN pagos pm        ON pm.id_contrato = c.id_contrato AND pm.periodo = :periodo
    LEFT  JOIN pagos ph        ON ph.id_contrato = c.id_contrato
    WHERE c.estatus = 'Activa'
    GROUP BY c.id_contrato, pm.id_pago
    ORDER BY FIELD(pm.estatus,'Vencido','Pendiente','Pagado',NULL), l.codigo
");
$consulta->execute([':periodo' => $periodo]);
$contratos = $consulta->fetchAll(PDO::FETCH_ASSOC);

$totalVencidos = $totalPendientes = $totalPagados = 0;
foreach ($contratos as $c) {
    if ($c['estatus_mes'] === 'Vencido')        $totalVencidos++;
    elseif ($c['estatus_mes'] === 'Pagado')     $totalPagados++;
    else                                         $totalPendientes++;
}

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <!-- Resumen -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div><div class="small text-white-50">Vencidos</div><div class="fs-3 fw-bold"><?= $totalVencidos ?></div></div>
                    <div class="fs-1 opacity-50">⚠</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-dark bg-warning">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div><div class="small">Pendientes</div><div class="fs-3 fw-bold"><?= $totalPendientes ?></div></div>
                    <div class="fs-1 opacity-50">⏳</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div><div class="small text-white-50">Pagados este mes</div><div class="fs-3 fw-bold"><?= $totalPagados ?></div></div>
                    <div class="fs-1 opacity-50">✓</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div><div class="small text-white-50">Contratos activos</div><div class="fs-3 fw-bold"><?= count($contratos) ?></div></div>
                    <div class="fs-1 opacity-50">🏢</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pagos — <span class="text-muted fs-6"><?= date('F Y') ?></span></h5>
            <?php if ($puedeCrear): ?>
                <a class="btn btn-success btn-sm" href="crear.php">+ Registrar pago</a>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Local</th>
                            <th>Arrendatario</th>
                            <th>Día pago</th>
                            <th>Fecha programada</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Estatus mes</th>
                            <th>Historial</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($contratos as $c):
                        // Calcular fecha programada del mes actual con dia_pago
                        $anio = (int)date('Y'); $mes = (int)date('m');
                        $dia  = max(1, min(28, (int)($c['dia_pago'] ?? 1)));
                        $max  = (int)date('t', mktime(0,0,0,$mes,1,$anio));
                        $fechaProg = date('d/m/Y', mktime(0,0,0,$mes,min($dia,$max),$anio));

                        $estMes = $c['estatus_mes'] ?? null;
                        if ($estMes === 'Pagado')        { $badge = 'bg-success';         $label = 'Pagado'; }
                        elseif ($estMes === 'Vencido')   { $badge = 'bg-danger';          $label = 'Vencido'; }
                        elseif ($estMes === 'Pendiente') { $badge = 'bg-warning text-dark'; $label = 'Pendiente'; }
                        else                             { $badge = 'bg-secondary';        $label = 'Sin generar'; }
                    ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($c['local']) ?></td>
                            <td><?= htmlspecialchars($c['arrendatario']) ?></td>
                            <td>
                                <span class="badge bg-dark">Día <?= $c['dia_pago'] ?></span><br>
                                <small class="text-muted"><?= $c['duracion'] === 'Indefinido' ? '∞ Indef.' : 'Fijo' ?></small>
                            </td>
                            <td><strong><?= $fechaProg ?></strong></td>
                            <td>$<?= number_format($c['renta'], 2) ?></td>
                            <td>
                                <?= !empty($c['metodo_mes'])
                                    ? '<span class="badge bg-light text-dark border">'.htmlspecialchars($c['metodo_mes']).'</span>'
                                    : '<span class="text-muted small">—</span>' ?>
                            </td>
                            <td><span class="badge <?= $badge ?>"><?= $label ?></span></td>
                            <td>
                                <span class="badge bg-success me-1" title="Pagados"><?= $c['pagados'] ?>✓</span>
                                <span class="badge bg-danger me-1"  title="Vencidos"><?= $c['vencidos'] ?>⚠</span>
                                <span class="badge bg-warning text-dark" title="Pendientes"><?= $c['pendientes'] ?>⏳</span>
                            </td>
                            <td>
                                <a class="btn btn-outline-primary btn-sm"
                                   href="detalle_pagos.php?id=<?= $c['id_contrato'] ?>">Ver detalle</a>
                                <?php if ($puedeCrear && $estMes !== 'Pagado'): ?>
                                    <a class="btn btn-success btn-sm ms-1"
                                       href="crear.php?id_contrato=<?= $c['id_contrato'] ?>">Registrar</a>
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
