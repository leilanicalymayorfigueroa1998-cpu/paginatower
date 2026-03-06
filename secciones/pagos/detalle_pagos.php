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

/* ─── Obtener id_contrato (nuevo: por ?id=, compatible con ?contrato= antiguo) ─── */
$idContrato = null;

if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $idContrato = (int)$_GET['id'];
} elseif (!empty($_GET['contrato'])) {
    $stmt = $conexionBD->prepare("
        SELECT c.id_contrato FROM contratos c
        INNER JOIN locales l ON c.id_local = l.id_local
        WHERE l.codigo = :codigo LIMIT 1
    ");
    $stmt->execute([':codigo' => trim($_GET['contrato'])]);
    $row = $stmt->fetchColumn();
    if ($row) $idContrato = (int)$row;
}

if (!$idContrato) {
    echo "<div class='alert alert-danger m-4'>Contrato inválido.</div>";
    exit();
}

/* ─── Marcar vencidos de este contrato ─── */
$conexionBD->prepare("
    UPDATE pagos SET estatus = 'Vencido'
    WHERE estatus = 'Pendiente'
    AND fecha_pago < CURDATE()
    AND id_contrato = :id
")->execute([':id' => $idContrato]);

/* ─── Datos del contrato ─── */
$stmtC = $conexionBD->prepare("
    SELECT c.*, l.codigo AS local, a.nombre AS arrendatario
    FROM contratos c
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    WHERE c.id_contrato = :id
");
$stmtC->execute([':id' => $idContrato]);
$contrato = $stmtC->fetch(PDO::FETCH_ASSOC);

if (!$contrato) {
    echo "<div class='alert alert-danger m-4'>Contrato no encontrado.</div>";
    exit();
}

/* ─── Todos los pagos del contrato ─── */
$stmtP = $conexionBD->prepare("
    SELECT id_pago, periodo, fecha_pago, monto, metodo_pago, estatus
    FROM pagos
    WHERE id_contrato = :id
    ORDER BY periodo DESC
");
$stmtP->execute([':id' => $idContrato]);
$pagos = $stmtP->fetchAll(PDO::FETCH_ASSOC);

/* ─── Totales ─── */
$totalPagado    = 0;
$totalPendiente = 0;
$totalVencido   = 0;
foreach ($pagos as $p) {
    if ($p['estatus'] === 'Pagado')        $totalPagado    += $p['monto'];
    elseif ($p['estatus'] === 'Pendiente') $totalPendiente += $p['monto'];
    elseif ($p['estatus'] === 'Vencido')   $totalVencido   += $p['monto'];
}

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <!-- Info del contrato -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                📋 Detalle de pagos —
                <strong><?= htmlspecialchars($contrato['local']) ?></strong>
            </h5>
            <a class="btn btn-secondary btn-sm" href="index.php">← Volver</a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="small text-muted">Arrendatario</div>
                    <strong><?= htmlspecialchars($contrato['arrendatario']) ?></strong>
                </div>
                <div class="col-md-2">
                    <div class="small text-muted">Renta mensual</div>
                    <strong>$<?= number_format($contrato['renta'], 2) ?></strong>
                </div>
                <div class="col-md-2">
                    <div class="small text-muted">Día de pago</div>
                    <strong>Día <?= $contrato['dia_pago'] ?> de cada mes</strong>
                </div>
                <div class="col-md-2">
                    <div class="small text-muted">Duración</div>
                    <span class="badge <?= $contrato['duracion'] === 'Indefinido' ? 'bg-info text-dark' : 'bg-secondary' ?>">
                        <?= $contrato['duracion'] === 'Indefinido' ? '∞ Indefinido' : 'Fijo' ?>
                    </span>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">Vigencia</div>
                    <?= date('d/m/Y', strtotime($contrato['fecha_inicio'])) ?>
                    <?= !empty($contrato['fecha_fin']) ? ' → ' . date('d/m/Y', strtotime($contrato['fecha_fin'])) : ' → Indefinido' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body py-2 d-flex justify-content-between">
                    <div><div class="small text-muted">Total cobrado</div><div class="fw-bold text-success fs-5">$<?= number_format($totalPagado, 2) ?></div></div>
                    <span class="fs-2">💰</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body py-2 d-flex justify-content-between">
                    <div><div class="small text-muted">Por cobrar</div><div class="fw-bold text-warning fs-5">$<?= number_format($totalPendiente, 2) ?></div></div>
                    <span class="fs-2">⏳</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body py-2 d-flex justify-content-between">
                    <div><div class="small text-muted">Vencido sin pagar</div><div class="fw-bold text-danger fs-5">$<?= number_format($totalVencido, 2) ?></div></div>
                    <span class="fs-2">⚠</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de pagos mes a mes -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Historial mes a mes (generados automáticamente)</h6>
            <?php if ($puedeCrear): ?>
                <a class="btn btn-success btn-sm"
                   href="crear.php?id_contrato=<?= $idContrato ?>">
                   + Registrar pago
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Periodo</th>
                            <th>Fecha programada</th>
                            <th>Fecha de pago</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (empty($pagos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No hay pagos generados aún.<br>
                                <?php if ($contrato['duracion'] === 'Indefinido'): ?>
                                    <small>Los pagos se generan automáticamente el día 1 de cada mes vía cron.</small>
                                <?php else: ?>
                                    <small>Los pagos se generan al crear o editar el contrato.</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pagos as $p):
                            // Recalcular fecha programada del periodo
                            $anioP = (int)substr($p['periodo'], 0, 4);
                            $mesP  = (int)substr($p['periodo'], 5, 2);
                            $dia   = max(1, min(28, (int)($contrato['dia_pago'] ?? 1)));
                            $maxD  = (int)date('t', mktime(0,0,0,$mesP,1,$anioP));
                            $fechaProg = date('d/m/Y', mktime(0,0,0,$mesP,min($dia,$maxD),$anioP));

                            if ($p['estatus'] === 'Pagado')        { $badge = 'bg-success';            $icon = '✓'; }
                            elseif ($p['estatus'] === 'Vencido')   { $badge = 'bg-danger';             $icon = '⚠'; }
                            elseif ($p['estatus'] === 'Pendiente') { $badge = 'bg-warning text-dark';  $icon = '⏳'; }
                            else                                   { $badge = 'bg-secondary';          $icon = '—'; }

                            $esMesActual = (substr($p['periodo'],0,7) === date('Y-m'));
                        ?>
                            <tr <?= $esMesActual ? 'class="table-active"' : '' ?>>
                                <td>
                                    <?= date('F Y', strtotime($p['periodo'])) ?>
                                    <?php if ($esMesActual): ?>
                                        <span class="badge bg-primary ms-1">Mes actual</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $fechaProg ?></td>
                                <td>
                                    <?= !empty($p['fecha_pago'])
                                        ? date('d/m/Y', strtotime($p['fecha_pago']))
                                        : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td>$<?= number_format($p['monto'], 2) ?></td>
                                <td>
                                    <?= !empty($p['metodo_pago'])
                                        ? '<span class="badge bg-light text-dark border">'.htmlspecialchars($p['metodo_pago']).'</span>'
                                        : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badge ?>"><?= $icon ?> <?= $p['estatus'] ?></span>
                                </td>
                                <td>
                                    <?php if ($puedeEditar): ?>
                                        <a class="btn btn-primary btn-sm"
                                           href="editar.php?txtID=<?= $p['id_pago'] ?>">Editar</a>
                                    <?php endif; ?>
                                    <?php if ($puedeEliminar): ?>
                                        <form action="eliminar.php" method="post" style="display:inline">
                                            <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                                            <input type="hidden" name="txtID" value="<?= $p['id_pago'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar pago de <?= date('F Y', strtotime($p['periodo'])) ?>?')">
                                                Borrar
                                            </button>
                                        </form>
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
