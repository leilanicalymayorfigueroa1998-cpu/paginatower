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
    SELECT SUM(abono) AS ingresos, SUM(cargo) AS egresos, SUM(abono) - SUM(cargo) AS neto
    FROM movimientos_financieros WHERE fecha BETWEEN :inicio AND :fin
");
$balance->execute([':inicio' => $periodo, ':fin' => $hoy]);
$bal = $balance->fetch(PDO::FETCH_ASSOC);

/* ── Renta potencial mensual ── */
$rentaPotencial = $conexionBD->query("SELECT SUM(renta) FROM contratos WHERE estatus='Activa'")->fetchColumn();

/* ── Contratos por vencer en 60 días ── */
$proximosVencer = $conexionBD->query("
    SELECT l.codigo AS local, a.nombre AS arrendatario,
           c.fecha_fin, c.renta,
           DATEDIFF(c.fecha_fin, CURDATE()) AS dias_restantes
    FROM contratos c
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    WHERE c.estatus = 'Activa' AND c.fecha_fin IS NOT NULL
    AND c.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
    ORDER BY c.fecha_fin ASC LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

/* ── Pagos pendientes/vencidos del mes ── */
$pagosPendientes = $conexionBD->prepare("
    SELECT l.codigo AS local, a.nombre AS arrendatario,
           p.monto, p.estatus, p.fecha_pago, c.dia_pago
    FROM pagos p
    INNER JOIN contratos c     ON p.id_contrato     = c.id_contrato
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    WHERE p.periodo = :periodo AND p.estatus IN ('Pendiente','Vencido')
    ORDER BY p.estatus DESC, l.codigo ASC LIMIT 10
");
$pagosPendientes->execute([':periodo' => $periodo]);
$pendientes = $pagosPendientes->fetchAll(PDO::FETCH_ASSOC);

/* ── Últimos 6 meses ── */
$meses6 = [];
for ($i = 5; $i >= 0; $i--) {
    $ts  = strtotime("-$i month", strtotime($periodo));
    $ini = date('Y-m-01', $ts);
    $fin = date('Y-m-t', $ts);
    $ing = $conexionBD->prepare("SELECT COALESCE(SUM(abono),0) FROM movimientos_financieros WHERE fecha BETWEEN :i AND :f");
    $ing->execute([':i' => $ini, ':f' => $fin]);
    $eg  = $conexionBD->prepare("SELECT COALESCE(SUM(cargo),0) FROM movimientos_financieros WHERE fecha BETWEEN :i AND :f");
    $eg->execute([':i' => $ini, ':f' => $fin]);
    $meses6[] = ['mes' => date('M', $ts), 'ingresos' => (float)$ing->fetchColumn(), 'egresos' => (float)$eg->fetchColumn()];
}

$pctCobrado = $rentaPotencial > 0 ? round(($pm['cobrado'] / $rentaPotencial) * 100) : 0;
$neto = (float)($bal['neto'] ?? 0);

// Calcular % morosidad
$totalMes = ($pm['cobrado'] ?? 0) + ($pm['pendiente'] ?? 0) + ($pm['vencido'] ?? 0);
$pctMorosidad = $totalMes > 0 ? round((($pm['pendiente'] + $pm['vencido']) / $totalMes) * 100) : 0;
$pctOcupacion = $totalContratos > 0 ? round(($activos / $totalContratos) * 100) : 0;

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

  <!-- Page header -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">📊 Dashboard General</div>
      <div class="tw-page-subtitle">Resumen ejecutivo · <?= date('d/m/Y') ?></div>
    </div>
    <div class="tw-page-actions">
      <a href="../contrato/index.php" class="btn btn-secondary" style="font-size:13px;padding:8px 16px;">📋 Ver contratos</a>
      <a href="../pagos/index.php"    class="btn btn-success"   style="font-size:13px;padding:8px 16px;">+ Registrar pago</a>
    </div>
  </div>

  <!-- ── KPI row ── -->
  <div class="tw-stats tw-stats-4">

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,#3b82f6,#06b6d4)"></div>
      <div class="glow" style="background:#3b82f6"></div>
      <div class="tw-stat-label">💰 Renta potencial / mes</div>
      <div class="tw-stat-value" style="color:var(--blue);">$<?= number_format($rentaPotencial, 0) ?></div>
      <div class="tw-stat-meta"><?= $activos ?> contratos activos</div>
    </div>

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,#10b981,#34d399)"></div>
      <div class="glow" style="background:#10b981"></div>
      <div class="tw-stat-label">📥 Cobrado en <?= date('F') ?></div>
      <div class="tw-stat-value" style="color:var(--green);">$<?= number_format($pm['cobrado'] ?? 0, 0) ?></div>
      <div class="tw-stat-meta">
        <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
          <div style="flex:1;height:4px;background:var(--s3);border-radius:4px;overflow:hidden;">
            <div style="height:100%;width:<?= $pctCobrado ?>%;background:var(--green);border-radius:4px;"></div>
          </div>
          <span><?= $pctCobrado ?>%</span>
        </div>
      </div>
    </div>

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,#f43f5e,#fb923c)"></div>
      <div class="glow" style="background:#f43f5e"></div>
      <div class="tw-stat-label">⚠️ Por cobrar este mes</div>
      <div class="tw-stat-value" style="color:var(--red);">$<?= number_format(($pm['pendiente'] ?? 0) + ($pm['vencido'] ?? 0), 0) ?></div>
      <div class="tw-stat-meta"><?= $pm['n_pendientes'] ?? 0 ?> pendientes · <?= $pm['n_vencidos'] ?? 0 ?> vencidos</div>
    </div>

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,<?= $neto >= 0 ? '#10b981,#34d399' : '#f43f5e,#fb923c' ?>)"></div>
      <div class="glow" style="background:<?= $neto >= 0 ? '#10b981' : '#f43f5e' ?>"></div>
      <div class="tw-stat-label"><?= $neto >= 0 ? '📈' : '📉' ?> Balance neto del mes</div>
      <div class="tw-stat-value" style="color:<?= $neto >= 0 ? 'var(--green)' : 'var(--red)' ?>;">$<?= number_format(abs($neto), 0) ?></div>
      <div class="tw-stat-meta">Ing $<?= number_format($bal['ingresos'] ?? 0, 0) ?> · Eg $<?= number_format($bal['egresos'] ?? 0, 0) ?></div>
    </div>

  </div>

  <!-- ── Fila 2: Gráfica + Distribución + Indicadores ── -->
  <div style="display:grid;grid-template-columns:1fr 320px;gap:16px;margin-bottom:16px;">

    <!-- Gráfica 6 meses -->
    <div class="card">
      <div class="card-header">
        <div class="card-title-tw">📈 Ingresos vs Egresos — últimos 6 meses</div>
        <span style="font-size:11px;color:var(--text3);"><?= date('Y') ?></span>
      </div>
      <div style="padding:20px;">
        <canvas id="graficaMeses" height="105"></canvas>
      </div>
    </div>

    <!-- Distribución + KPIs -->
    <div class="card">
      <div class="card-header">
        <div class="card-title-tw">🍩 Distribución contratos</div>
      </div>
      <div style="padding:16px 20px;display:flex;align-items:center;justify-content:center;gap:18px;">
        <!-- SVG donut — dibujado con datos reales -->
        <?php
          $total_d = max(1, $activos + $vencidos + $finalizados);
          $circ    = 251.2; // 2*PI*40
          $pActivo = ($activos - $vencidos) / $total_d * $circ;
          $pVenc   = $vencidos / $total_d * $circ;
          $pIndf   = $indefinidos / $total_d * $circ;
          $pFin    = max(0, $circ - $pActivo - $pVenc - $pIndf);
          $o1 = 0;
          $o2 = -$pActivo;
          $o3 = -$pActivo - $pVenc;
          $o4 = -$pActivo - $pVenc - $pIndf;
        ?>
        <svg width="110" height="110" viewBox="0 0 110 110">
          <circle cx="55" cy="55" r="40" fill="none" stroke="#1a2438" stroke-width="18"/>
          <?php if ($pActivo > 0): ?>
          <circle cx="55" cy="55" r="40" fill="none" stroke="#10b981" stroke-width="18"
            stroke-dasharray="<?= $pActivo ?> <?= $circ - $pActivo ?>" stroke-dashoffset="<?= $o1 ?>" stroke-linecap="round"
            transform="rotate(-90 55 55)"/>
          <?php endif; ?>
          <?php if ($pVenc > 0): ?>
          <circle cx="55" cy="55" r="40" fill="none" stroke="#f43f5e" stroke-width="18"
            stroke-dasharray="<?= $pVenc ?> <?= $circ - $pVenc ?>" stroke-dashoffset="<?= $o2 ?>" stroke-linecap="round"
            transform="rotate(-90 55 55)"/>
          <?php endif; ?>
          <?php if ($pIndf > 0): ?>
          <circle cx="55" cy="55" r="40" fill="none" stroke="#3b82f6" stroke-width="18"
            stroke-dasharray="<?= $pIndf ?> <?= $circ - $pIndf ?>" stroke-dashoffset="<?= $o3 ?>" stroke-linecap="round"
            transform="rotate(-90 55 55)"/>
          <?php endif; ?>
          <text x="55" y="51" text-anchor="middle" fill="#dde4f0" font-family="Syne,sans-serif" font-weight="800" font-size="16"><?= $totalContratos ?></text>
          <text x="55" y="65" text-anchor="middle" fill="#8494b0" font-size="9">contratos</text>
        </svg>
        <div style="display:flex;flex-direction:column;gap:8px;">
          <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text2);">
            <div style="width:10px;height:10px;border-radius:3px;background:var(--green);flex-shrink:0;"></div>
            Activos <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--text);margin-left:auto;"><?= $activos ?></span>
          </div>
          <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text2);">
            <div style="width:10px;height:10px;border-radius:3px;background:var(--red);flex-shrink:0;"></div>
            Vencidos <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--text);margin-left:auto;"><?= $vencidos ?></span>
          </div>
          <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text2);">
            <div style="width:10px;height:10px;border-radius:3px;background:var(--blue);flex-shrink:0;"></div>
            Indefinidos <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--text);margin-left:auto;"><?= $indefinidos ?></span>
          </div>
        </div>
      </div>
      <!-- KPI rows -->
      <div style="border-top:1px solid var(--border);">
        <?php
          $kpis = [
            ['Cobro efectivo', $pctCobrado.'%', 'var(--green)', $pctCobrado],
            ['Ocupación contratos', $pctOcupacion.'%', 'var(--blue)', $pctOcupacion],
            ['Morosidad', $pctMorosidad.'%', 'var(--red)', $pctMorosidad],
          ];
          foreach ($kpis as $k):
        ?>
        <div style="padding:11px 20px;border-bottom:1px solid var(--border);display:flex;flex-direction:column;gap:5px;">
          <div style="display:flex;justify-content:space-between;">
            <span style="font-size:12px;color:var(--text2);"><?= $k[0] ?></span>
            <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:<?= $k[2] ?>;"><?= $k[1] ?></span>
          </div>
          <div style="height:5px;background:var(--s3);border-radius:4px;overflow:hidden;">
            <div style="height:100%;width:<?= $k[3] ?>%;background:<?= $k[2] ?>;border-radius:4px;transition:width .5s;"></div>
          </div>
        </div>
        <?php endforeach; ?>
        <div style="padding:11px 20px;display:flex;justify-content:space-between;">
          <span style="font-size:12px;color:var(--text2);">Total contratos</span>
          <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--text);"><?= $activos ?> / <?= $totalContratos ?></span>
        </div>
      </div>
    </div>

  </div>

  <!-- ── Fila 3: Alertas + Pagos pendientes + Por vencer ── -->
  <div style="display:grid;grid-template-columns:220px 1fr 320px;gap:16px;">

    <!-- Alertas -->
    <div class="card">
      <div class="card-header">
        <div class="card-title-tw">🔔 Alertas</div>
        <?php
          $totalAlertas_d = 0;
          if ($vencidos > 0) $totalAlertas_d++;
          if ($porVencer > 0) $totalAlertas_d++;
          if (($pm['n_pendientes'] ?? 0) > 0) $totalAlertas_d++;
        ?>
        <span class="tw-count"><?= $totalAlertas_d ?></span>
      </div>
      <div style="padding:14px;display:flex;flex-direction:column;gap:8px;">
        <?php if ($vencidos > 0): ?>
        <div style="padding:11px 14px;border-radius:9px;border:1px solid rgba(244,63,94,0.2);background:var(--red-g);display:flex;align-items:flex-start;gap:10px;">
          <span style="font-size:16px;flex-shrink:0;">🔴</span>
          <div>
            <div style="font-size:12px;font-weight:600;color:var(--red);"><?= $vencidos ?> contratos vencidos</div>
            <div style="font-size:11px;color:var(--red);opacity:.8;margin-top:2px;">Atención requerida</div>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($porVencer > 0): ?>
        <div style="padding:11px 14px;border-radius:9px;border:1px solid rgba(245,158,11,0.2);background:var(--amber-g);display:flex;align-items:flex-start;gap:10px;">
          <span style="font-size:16px;flex-shrink:0;">⚠️</span>
          <div>
            <div style="font-size:12px;font-weight:600;color:var(--amber);"><?= $porVencer ?> vencen en 30 días</div>
            <div style="font-size:11px;color:var(--amber);opacity:.8;margin-top:2px;">Renovar pronto</div>
          </div>
        </div>
        <?php endif; ?>
        <?php if (($pm['n_pendientes'] ?? 0) > 0): ?>
        <div style="padding:11px 14px;border-radius:9px;border:1px solid rgba(59,130,246,0.2);background:var(--blue-g);display:flex;align-items:flex-start;gap:10px;">
          <span style="font-size:16px;flex-shrink:0;">🕐</span>
          <div>
            <div style="font-size:12px;font-weight:600;color:var(--blue);"><?= $pm['n_pendientes'] ?> pagos sin confirmar</div>
            <div style="font-size:11px;color:var(--blue);opacity:.8;margin-top:2px;">Revisar este mes</div>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($totalAlertas_d === 0): ?>
        <div style="padding:24px 14px;text-align:center;color:var(--green);font-size:12px;">✅ Todo al corriente</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Pagos pendientes del mes -->
    <div class="card">
      <div class="card-header" style="justify-content:space-between;">
        <div class="card-title-tw">⏳ Pagos pendientes — <?= date('F Y') ?></div>
        <a href="../pagos/index.php" class="btn btn-success btn-sm">Ver todos</a>
      </div>
      <div class="card-body">
        <table class="tw-table" style="width:100%;">
          <thead>
            <tr>
              <th>Local</th><th>Arrendatario</th><th>Monto</th><th>Día pago</th><th>Estatus</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($pendientes)): ?>
            <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--green);font-size:13px;">
              ✅ Todos los pagos del mes están al corriente
            </td></tr>
          <?php else: foreach ($pendientes as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['local']) ?></td>
              <td><?= htmlspecialchars($p['arrendatario']) ?></td>
              <td style="font-family:'DM Mono',monospace;font-weight:600;">$<?= number_format($p['monto'], 2) ?></td>
              <td style="color:var(--text2);">Día <?= $p['dia_pago'] ?></td>
              <td>
                <?php if ($p['estatus'] === 'Vencido'): ?>
                  <span class="badge bg-danger">Vencido</span>
                <?php else: ?>
                  <span class="badge bg-warning">Pendiente</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Contratos por vencer -->
    <div class="card">
      <div class="card-header" style="justify-content:space-between;">
        <div class="card-title-tw">📅 Por vencer (60 días)</div>
        <a href="../contrato/index.php" class="btn btn-success btn-sm">Ver todos</a>
      </div>
      <div class="card-body">
        <table class="tw-table" style="width:100%;">
          <thead>
            <tr><th>Local</th><th>Arrendatario</th><th>Vence</th><th>Días</th></tr>
          </thead>
          <tbody>
          <?php if (empty($proximosVencer)): ?>
            <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--green);font-size:13px;">
              ✅ Sin contratos por vencer en 60 días
            </td></tr>
          <?php else: foreach ($proximosVencer as $cv): ?>
            <?php
              $dias = (int)$cv['dias_restantes'];
              $col  = $dias <= 15 ? 'var(--red)' : ($dias <= 30 ? 'var(--amber)' : 'var(--text2)');
            ?>
            <tr>
              <td><?= htmlspecialchars($cv['local']) ?></td>
              <td><?= htmlspecialchars(explode(' ', $cv['arrendatario'])[0]) ?></td>
              <td style="color:var(--text2);font-size:12px;"><?= date('d/m/Y', strtotime($cv['fecha_fin'])) ?></td>
              <td style="color:<?= $col ?>;font-weight:700;font-family:'DM Mono',monospace;"><?= $dias ?>d</td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div><!-- /content -->

<script>
Chart.defaults.color = '#8494b0';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
Chart.defaults.font.family = "'Epilogue', sans-serif";
Chart.defaults.font.size = 12;

const mesesLabels = <?= json_encode(array_column($meses6, 'mes')) ?>;
const mesesIngr   = <?= json_encode(array_column($meses6, 'ingresos')) ?>;
const mesesEgr    = <?= json_encode(array_column($meses6, 'egresos')) ?>;

new Chart(document.getElementById('graficaMeses'), {
    type: 'bar',
    data: {
        labels: mesesLabels,
        datasets: [
            {
                label: 'Ingresos',
                data: mesesIngr,
                backgroundColor: 'rgba(16,185,129,0.7)',
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Egresos',
                data: mesesEgr,
                backgroundColor: 'rgba(244,63,94,0.6)',
                borderRadius: 6,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                labels: { color: '#8494b0', boxWidth: 10, padding: 16, font: { size: 12 } }
            },
            tooltip: {
                backgroundColor: '#141c2e',
                borderColor: 'rgba(255,255,255,0.11)',
                borderWidth: 1,
                titleColor: '#dde4f0',
                bodyColor: '#8494b0',
                callbacks: { label: ctx => ' $' + ctx.parsed.y.toLocaleString('es-MX') }
            }
        },
        scales: {
            x: {
                grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                ticks: { color: '#8494b0' }
            },
            y: {
                grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                ticks: {
                    color: '#8494b0',
                    callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v)
                }
            }
        }
    }
});
</script>

<?php include('../../templates/pie.php'); ?>
