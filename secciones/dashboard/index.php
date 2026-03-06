<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

require_once __DIR__ . '/../../services/MovimientoService.php';

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'dashboard', 'ver');

// CONTRATOS
$totalContratos = $conexionBD->query("SELECT COUNT(*) FROM contratos")->fetchColumn();
$activos        = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus = 'Activa'")->fetchColumn();
$vencidos       = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus = 'Activa' AND fecha_fin IS NOT NULL AND fecha_fin < CURDATE()")->fetchColumn();
$porVencer      = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus = 'Activa' AND fecha_fin IS NOT NULL AND fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)")->fetchColumn();
$indefinidos    = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus = 'Activa' AND duracion = 'Indefinido'")->fetchColumn();
$finalizados    = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus IN ('Finalizada','Cancelada')")->fetchColumn();

// BALANCE
$movService  = new MovimientoService($conexionBD);
$balanceRows = $movService->obtenerBalance();
$balanceMes  = $movService->obtenerBalanceMes();

// Organizar balance por origen
$balance    = ['CUENTA' => ['abonos'=>0,'cargos'=>0,'balance'=>0], 'EFECTIVO' => ['abonos'=>0,'cargos'=>0,'balance'=>0]];
$balanceMesOrg = ['CUENTA' => ['abonos'=>0,'cargos'=>0,'balance'=>0], 'EFECTIVO' => ['abonos'=>0,'cargos'=>0,'balance'=>0]];

foreach ($balanceRows as $row) {
    $o = strtoupper($row['origen']);
    if (isset($balance[$o])) {
        $balance[$o]['abonos']  = $row['total_abonos'];
        $balance[$o]['cargos']  = $row['total_cargos'];
        $balance[$o]['balance'] = $row['balance'];
    }
}
foreach ($balanceMes as $row) {
    $o = strtoupper($row['origen']);
    if (isset($balanceMesOrg[$o])) {
        $balanceMesOrg[$o]['abonos']  = $row['total_abonos'];
        $balanceMesOrg[$o]['cargos']  = $row['total_cargos'];
        $balanceMesOrg[$o]['balance'] = $row['balance'];
    }
}

$totalBalance    = ($balance['CUENTA']['balance'] + $balance['EFECTIVO']['balance']);
$totalBalanceMes = ($balanceMesOrg['CUENTA']['balance'] + $balanceMesOrg['EFECTIVO']['balance']);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <h4 class="mb-4">Resumen de Contratos</h4>

    <!-- TARJETAS CONTRATOS -->
    <div class="row mb-4">

        <div class="col-md-2 mb-3">
            <div class="card border-start border-dark border-4 shadow-sm">
                <div class="card-body">
                    <small>Total</small>
                    <h4><?= $totalContratos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <small>Activos</small>
                    <h4><?= $activos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <small>Vencidos</small>
                    <h4><?= $vencidos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <small>Por vencer</small>
                    <h4><?= $porVencer ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <small>Indefinidos</small>
                    <h4><?= $indefinidos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-start border-secondary border-4 shadow-sm">
                <div class="card-body">
                    <small>Finalizados</small>
                    <h4><?= $finalizados ?></h4>
                </div>
            </div>
        </div>

    </div>

    <!-- GRÁFICA CONTRATOS -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Contratos por Estado</h5>
            <canvas id="graficaContratos"></canvas>
        </div>
    </div>

    <!-- BALANCE INGRESOS Y EGRESOS -->
    <h4 class="mb-3">Balance Financiero</h4>

    <div class="row mb-3">
        <!-- MES ACTUAL -->
        <div class="col-12 mb-2">
            <span class="text-muted fw-semibold">Mes actual</span>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Ingresos CUENTA</small>
                    <h5 class="text-success">$<?= number_format($balanceMesOrg['CUENTA']['abonos'], 2) ?></h5>
                    <small class="text-muted">Egresos</small>
                    <h6 class="text-danger">$<?= number_format($balanceMesOrg['CUENTA']['cargos'], 2) ?></h6>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Ingresos EFECTIVO</small>
                    <h5 class="text-success">$<?= number_format($balanceMesOrg['EFECTIVO']['abonos'], 2) ?></h5>
                    <small class="text-muted">Egresos</small>
                    <h6 class="text-danger">$<?= number_format($balanceMesOrg['EFECTIVO']['cargos'], 2) ?></h6>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-start border-<?= $totalBalanceMes >= 0 ? 'success' : 'danger' ?> border-4 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Balance neto del mes</small>
                    <h4 class="<?= $totalBalanceMes >= 0 ? 'text-success' : 'text-danger' ?>">
                        $<?= number_format(abs($totalBalanceMes), 2) ?>
                        <?= $totalBalanceMes < 0 ? '<small>(negativo)</small>' : '' ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">Balance general acumulado</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Origen</th>
                        <th class="text-end text-success">Ingresos</th>
                        <th class="text-end text-danger">Egresos</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (['CUENTA', 'EFECTIVO'] as $origen): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= $origen ?></span></td>
                        <td class="text-end text-success fw-bold">$<?= number_format($balance[$origen]['abonos'], 2) ?></td>
                        <td class="text-end text-danger fw-bold">$<?= number_format($balance[$origen]['cargos'], 2) ?></td>
                        <td class="text-end fw-bold <?= $balance[$origen]['balance'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            $<?= number_format(abs($balance[$origen]['balance']), 2) ?>
                            <?= $balance[$origen]['balance'] < 0 ? ' <small>(negativo)</small>' : '' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="table-dark fw-bold">
                        <td>TOTAL</td>
                        <td class="text-end text-success">$<?= number_format($balance['CUENTA']['abonos'] + $balance['EFECTIVO']['abonos'], 2) ?></td>
                        <td class="text-end text-danger">$<?= number_format($balance['CUENTA']['cargos'] + $balance['EFECTIVO']['cargos'], 2) ?></td>
                        <td class="text-end <?= $totalBalance >= 0 ? 'text-success' : 'text-danger' ?>">$<?= number_format(abs($totalBalance), 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
const ctx = document.getElementById('graficaContratos');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Activos', 'Vencidos', 'Por vencer', 'Indefinidos', 'Finalizados'],
        datasets: [{
            label: 'Cantidad',
            data: [<?= $activos ?>, <?= $vencidos ?>, <?= $porVencer ?>, <?= $indefinidos ?>, <?= $finalizados ?>],
            backgroundColor: ['#198754','#dc3545','#ffc107','#0d6efd','#6c757d'],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<?php include('../../templates/pie.php'); ?>
