<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'dashboard', 'ver');

$hoy    = date('Y-m-d');
$periodo = date('Y-m-01');

/* ── Contratos ─────────────────────────────────────── */
$totalContratos = $conexionBD->query("SELECT COUNT(*) FROM contratos")->fetchColumn();
$activos        = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus='Activa'")->fetchColumn();
$vencidos       = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus='Activa' AND fecha_fin IS NOT NULL AND fecha_fin < CURDATE()")->fetchColumn();
$porVencer      = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus='Activa' AND fecha_fin IS NOT NULL AND fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
$indefinidos    = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus='Activa' AND duracion='Indefinido'")->fetchColumn();
$finalizados    = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus IN ('Finalizada','Cancelada')")->fetchColumn();

/* ── Pagos del mes actual ───────────────────────────── */
$pagosMes = $conexionBD->prepare("
    SELECT
        SUM(CASE WHEN estatus='Pagado'    THEN monto ELSE 0 END) AS cobrado,
        SUM(CASE WHEN estatus='Pendiente' THEN monto ELSE 0 END) AS pendiente,
        SUM(CASE WHEN estatus='Vencido'   THEN monto ELSE 0 END) AS vencido,
        COUNT(CASE WHEN estatus='Pagado'    THEN 1 END) AS n_pagados,
        COUNT(CASE WHEN estatus='Pendiente' THEN 1 END) AS n_pendientes,
        COUNT(CASE WHEN estatus='Vencido'   THEN 1 END) AS n_vencidos
    FROM pagos WHERE periodo = :p
");
$pagosMes->execute([':p' => $periodo]);
$pm = $pagosMes->fetch(PDO::FETCH_ASSOC);

/* ── Balance financiero del mes ─────────────────────── */
$balance = $conexionBD->prepare("
    SELECT
        SUM(abono) AS ingresos,
        SUM(cargo)  AS egresos,
        SUM(abono) - SUM(cargo) AS neto
    FROM movimientos_financieros
    WHERE fecha BETWEEN :inicio AND :fin
");
$balance->execute([':inicio' => $periodo, ':fin' => $hoy]);
$bal = $balance->fetch(PDO::FETCH_ASSOC);

/* ── Renta potencial mensual (todos los contratos activos) ── */
$rentaPotencial = $conexionBD->query("
    SELECT SUM(renta) FROM contratos WHERE estatus='Activa'
")->fetchColumn();

/* ── Contratos por vencer en 30 días ───────────────── */
$proximosVencer = $conexionBD->query("
    SELECT l.codigo AS local, a.nombre AS arrendatario,
           c.fecha_fin, c.renta,
           DATEDIFF(c.fecha_fin, CURDATE()) AS dias_restantes
    FROM contratos c
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    WHERE c.estatus = 'Activa'
    AND c.fecha_fin IS NOT NULL
    AND c.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
    ORDER BY c.fecha_fin ASC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

/* ── Pagos pendientes/vencidos del mes ─────────────── */
$pagosPendientes = $conexionBD->prepare("
    SELECT l.codigo AS local, a.nombre AS arrendatario,
           p.monto, p.estatus, p.fecha_pago, c.dia_pago
    FROM pagos p
    INNER JOIN contratos c     ON p.id_contrato     = c.id_contrato
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    WHERE p.periodo  = :periodo
    AND p.estatus IN ('Pendiente','Vencido')
    ORDER BY p.estatus DESC, l.codigo ASC
    LIMIT 10
");
$pagosPendientes->execute([':periodo' => $periodo]);
$pendientes = $pagosPendientes->fetchAll(PDO::FETCH_ASSOC);

/* ── Últimos 6 meses de ingresos (para gráfica) ───── */
$meses6 = [];
for ($i = 5; $i >= 0; $i--) {
    $ts  = strtotime("-$i month", strtotime($periodo));
    $ini = date('Y-m-01', $ts);
    $fin = date('Y-m-t', $ts);
    $ing = $conexionBD->prepare("SELECT COALESCE(SUM(abono),0) FROM movimientos_financieros WHERE fecha BETWEEN :i AND :f");
    $ing->execute([':i' => $ini, ':f' => $fin]);
    $eg  = $conexionBD->prepare("SELECT COALESCE(SUM(cargo),0) FROM movimientos_financieros WHERE fecha BETWEEN :i AND :f");
    $eg->execute([':i' => $ini, ':f' => $fin]);
    $meses6[] = [
        'mes'      => date('M', $ts),
        'ingresos' => (float)$ing->fetchColumn(),
        'egresos'  => (float)$eg->fetchColumn(),
    ];
}

/* ── % cobrado del mes ──────────────────────────────── */
$pctCobrado = $rentaPotencial > 0
    ? round(($pm['cobrado'] / $rentaPotencial) * 100)
    : 0;

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

<!-- ══════════════════════════════════════════
     FILA 1 — KPIs principales
══════════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Renta potencial -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-3">💰</div>
                <div>
                    <div class="text-muted small">Renta potencial / mes</div>
                    <div class="fw-bold fs-5">$<?= number_format($rentaPotencial, 2) ?></div>
                    <div class="text-muted small"><?= $activos ?> contratos activos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cobrado este mes -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary fs-3">📥</div>
                <div>
                    <div class="text-muted small">Cobrado en <?= date('F') ?></div>
                    <div class="fw-bold fs-5 text-primary">$<?= number_format($pm['cobrado'], 2) ?></div>
                    <div class="small">
                        <span class="text-success"><?= $pm['n_pagados'] ?> pagados</span>
                        &nbsp;·&nbsp;
                        <div class="progress mt-1" style="height:5px;width:100px">
                            <div class="progress-bar bg-primary" style="width:<?= $pctCobrado ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendiente/Vencido -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger fs-3">⚠️</div>
                <div>
                    <div class="text-muted small">Por cobrar este mes</div>
                    <div class="fw-bold fs-5 text-danger">$<?= number_format($pm['pendiente'] + $pm['vencido'], 2) ?></div>
                    <div class="small text-muted">
                        <?= $pm['n_pendientes'] ?> pendientes · <?= $pm['n_vencidos'] ?> vencidos
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance neto -->
    <div class="col-xl-3 col-md-6">
        <?php $neto = (float)($bal['neto'] ?? 0); ?>
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 <?= $neto >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' ?> fs-3">
                    <?= $neto >= 0 ? '📈' : '📉' ?>
                </div>
                <div>
                    <div class="text-muted small">Balance neto del mes</div>
                    <div class="fw-bold fs-5 <?= $neto >= 0 ? 'text-success' : 'text-danger' ?>">
                        $<?= number_format(abs($neto), 2) ?>
                    </div>
                    <div class="small text-muted">
                        Ing: $<?= number_format($bal['ingresos'] ?? 0, 2) ?> &nbsp;·&nbsp;
                        Eg: $<?= number_format($bal['egresos'] ?? 0, 2) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════
     FILA 2 — Contratos badges + gráfica
══════════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Gráfica 6 meses -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h6 class="fw-semibold mb-0">Ingresos vs Egresos — últimos 6 meses</h6>
            </div>
            <div class="card-body">
                <canvas id="graficaMeses" height="110"></canvas>
            </div>
        </div>
    </div>

    <!-- Contratos resumen -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h6 class="fw-semibold mb-0">Estado de contratos</h6>
            </div>
            <div class="card-body">
                <canvas id="graficaDonut" height="160"></canvas>
                <div class="row text-center mt-3 g-2">
                    <div class="col-4">
                        <div class="small text-muted">Activos</div>
                        <div class="fw-bold text-success fs-5"><?= $activos ?></div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Vencidos</div>
                        <div class="fw-bold text-danger fs-5"><?= $vencidos ?></div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Indefinidos</div>
                        <div class="fw-bold text-primary fs-5"><?= $indefinidos ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════
     FILA 3 — Tablas alertas
══════════════════════════════════════════ -->
<div class="row g-3">

    <!-- Pagos pendientes/vencidos del mes -->
    <div class="col-xl-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">⏳ Pagos pendientes — <?= date('F Y') ?></h6>
                <a href="../pagos/index.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Local</th>
                            <th>Arrendatario</th>
                            <th>Monto</th>
                            <th>Día pago</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($pendientes)): ?>
                        <tr><td colspan="5" class="text-center text-success py-3">
                            ✅ Todos los pagos del mes están al corriente
                        </td></tr>
                    <?php else: foreach ($pendientes as $p): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($p['local']) ?></td>
                            <td><?= htmlspecialchars($p['arrendatario']) ?></td>
                            <td>$<?= number_format($p['monto'], 2) ?></td>
                            <td>Día <?= $p['dia_pago'] ?></td>
                            <td>
                                <?php if ($p['estatus'] === 'Vencido'): ?>
                                    <span class="badge bg-danger">Vencido</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Contratos por vencer -->
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">📅 Contratos por vencer (60 días)</h6>
                <a href="../contrato/index.php" class="btn btn-sm btn-outline-warning">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Local</th>
                            <th>Arrendatario</th>
                            <th>Vence</th>
                            <th>Días</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($proximosVencer)): ?>
                        <tr><td colspan="4" class="text-center text-success py-3">
                            ✅ Sin contratos por vencer en 60 días
                        </td></tr>
                    <?php else: foreach ($proximosVencer as $cv): ?>
                        <?php
                            $dias = (int)$cv['dias_restantes'];
                            $colorDias = $dias <= 15 ? 'text-danger fw-bold' : ($dias <= 30 ? 'text-warning fw-semibold' : 'text-muted');
                        ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($cv['local']) ?></td>
                            <td><?= htmlspecialchars(explode(' ', $cv['arrendatario'])[0]) ?></td>
                            <td><?= date('d/m/Y', strtotime($cv['fecha_fin'])) ?></td>
                            <td class="<?= $colorDias ?>"><?= $dias ?>d</td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</div><!-- /content -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Gráfica barras 6 meses ────────────────────────────
const mesesLabels  = <?= json_encode(array_column($meses6, 'mes')) ?>;
const mesesIngr    = <?= json_encode(array_column($meses6, 'ingresos')) ?>;
const mesesEgr     = <?= json_encode(array_column($meses6, 'egresos')) ?>;

new Chart(document.getElementById('graficaMeses'), {
    type: 'bar',
    data: {
        labels: mesesLabels,
        datasets: [
            {
                label: 'Ingresos',
                data: mesesIngr,
                backgroundColor: 'rgba(25,135,84,0.7)',
                borderRadius: 6,
            },
            {
                label: 'Egresos',
                data: mesesEgr,
                backgroundColor: 'rgba(220,53,69,0.7)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, ticks: {
                callback: v => '$' + v.toLocaleString('es-MX')
            }}
        }
    }
});

// ── Gráfica donut contratos ───────────────────────────
new Chart(document.getElementById('graficaDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Activos fijos', 'Indefinidos', 'Vencidos', 'Finalizados'],
        datasets: [{
            data: [
                <?= $activos - $indefinidos ?>,
                <?= $indefinidos ?>,
                <?= $vencidos ?>,
                <?= $finalizados ?>
            ],
            backgroundColor: ['#198754','#0d6efd','#dc3545','#6c757d'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
        }
    }
});
</script>

<?php include('../../templates/pie.php'); ?>
