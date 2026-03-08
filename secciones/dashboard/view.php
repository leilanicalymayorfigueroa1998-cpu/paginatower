<?php /* DASHBOARD — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">📊 Dashboard General</div>
      <div class="tw-page-subtitle">Resumen ejecutivo · <?= date('d/m/Y') ?></div>
    </div>
    <div class="tw-page-actions">
      <a href="../contrato/index.php" class="btn btn-secondary">📋 Ver contratos</a>
      <a href="../pagos/index.php"    class="btn btn-success">+ Registrar pago</a>
    </div>
  </div>

  <!-- ── KPI ROW ── -->
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
        <div class="dash-progress-row">
          <div class="dash-progress-bg">
            <div class="dash-progress-fill green" style="width:<?= $pctCobrado ?>%"></div>
          </div>
          <span><?= $pctCobrado ?>%</span>
        </div>
      </div>
    </div>

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,#f43f5e,#fb923c)"></div>
      <div class="glow" style="background:#f43f5e"></div>
      <div class="tw-stat-label">⚠️ Por cobrar este mes</div>
      <div class="tw-stat-value" style="color:var(--red);">
        $<?= number_format(($pm['pendiente'] ?? 0) + ($pm['vencido'] ?? 0), 0) ?>
      </div>
      <div class="tw-stat-meta">
        <?= $pm['n_pendientes'] ?? 0 ?> pendientes · <?= $pm['n_vencidos'] ?? 0 ?> vencidos
      </div>
    </div>

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,<?= $neto >= 0 ? '#10b981,#34d399' : '#f43f5e,#fb923c' ?>)"></div>
      <div class="glow" style="background:<?= $neto >= 0 ? '#10b981' : '#f43f5e' ?>"></div>
      <div class="tw-stat-label"><?= $neto >= 0 ? '📈' : '📉' ?> Balance neto del mes</div>
      <div class="tw-stat-value" style="color:<?= $neto >= 0 ? 'var(--green)' : 'var(--red)' ?>;">
        $<?= number_format(abs($neto), 0) ?>
      </div>
      <div class="tw-stat-meta">
        Ing $<?= number_format($bal['ingresos'] ?? 0, 0) ?> · Eg $<?= number_format($bal['egresos'] ?? 0, 0) ?>
      </div>
    </div>

  </div>

  <!-- ── FILA 2: Gráfica + Distribución ── -->
  <div class="dash-row-2">

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
      <div class="dash-donut-wrap">
        <?php
          $total_d = max(1, $activos + $vencidos + $finalizados);
          $circ    = 251.2;
          $pActivo = ($activos - $vencidos) / $total_d * $circ;
          $pVenc   = $vencidos  / $total_d * $circ;
          $pIndf   = $indefinidos / $total_d * $circ;
          $o1 = 0; $o2 = -$pActivo; $o3 = -$pActivo - $pVenc;
        ?>
        <svg width="110" height="110" viewBox="0 0 110 110">
          <circle cx="55" cy="55" r="40" fill="none" stroke="#1a2438" stroke-width="18"/>
          <?php if ($pActivo > 0): ?>
          <circle cx="55" cy="55" r="40" fill="none" stroke="#10b981" stroke-width="18"
            stroke-dasharray="<?= $pActivo ?> <?= $circ - $pActivo ?>"
            stroke-dashoffset="<?= $o1 ?>" stroke-linecap="round" transform="rotate(-90 55 55)"/>
          <?php endif; ?>
          <?php if ($pVenc > 0): ?>
          <circle cx="55" cy="55" r="40" fill="none" stroke="#f43f5e" stroke-width="18"
            stroke-dasharray="<?= $pVenc ?> <?= $circ - $pVenc ?>"
            stroke-dashoffset="<?= $o2 ?>" stroke-linecap="round" transform="rotate(-90 55 55)"/>
          <?php endif; ?>
          <?php if ($pIndf > 0): ?>
          <circle cx="55" cy="55" r="40" fill="none" stroke="#3b82f6" stroke-width="18"
            stroke-dasharray="<?= $pIndf ?> <?= $circ - $pIndf ?>"
            stroke-dashoffset="<?= $o3 ?>" stroke-linecap="round" transform="rotate(-90 55 55)"/>
          <?php endif; ?>
          <text x="55" y="51" text-anchor="middle" fill="#dde4f0"
                font-family="Syne,sans-serif" font-weight="800" font-size="16">
            <?= $totalContratos ?>
          </text>
          <text x="55" y="65" text-anchor="middle" fill="#8494b0" font-size="9">contratos</text>
        </svg>

        <div class="dash-legend">
          <div class="dash-legend-item">
            <div class="dash-legend-dot green"></div>
            Activos <span class="dash-legend-val"><?= $activos ?></span>
          </div>
          <div class="dash-legend-item">
            <div class="dash-legend-dot red"></div>
            Vencidos <span class="dash-legend-val"><?= $vencidos ?></span>
          </div>
          <div class="dash-legend-item">
            <div class="dash-legend-dot blue"></div>
            Indefinidos <span class="dash-legend-val"><?= $indefinidos ?></span>
          </div>
        </div>
      </div>

      <!-- KPI rows -->
      <div style="border-top:1px solid var(--border);">
        <?php foreach ([
          ['Cobro efectivo',      $pctCobrado.'%',   'var(--green)', $pctCobrado],
          ['Ocupación contratos', $pctOcupacion.'%', 'var(--blue)',  $pctOcupacion],
          ['Morosidad',           $pctMorosidad.'%', 'var(--red)',   $pctMorosidad],
        ] as $k): ?>
        <div class="dash-kpi-row">
          <div class="dash-kpi-header">
            <span class="dash-kpi-label"><?= $k[0] ?></span>
            <span class="dash-kpi-value" style="color:<?= $k[2] ?>;"><?= $k[1] ?></span>
          </div>
          <div class="dash-progress-bg">
            <div class="dash-progress-fill" style="width:<?= $k[3] ?>%;background:<?= $k[2] ?>;"></div>
          </div>
        </div>
        <?php endforeach; ?>

        <div class="dash-kpi-row">
          <span class="dash-kpi-label">Total contratos</span>
          <span class="dash-kpi-value"><?= $activos ?> / <?= $totalContratos ?></span>
        </div>
      </div>
    </div>

  </div>

  <!-- ── FILA 3: Alertas + Pagos pendientes + Por vencer ── -->
  <div class="dash-row-3">

    <!-- Alertas -->
    <div class="card">
      <div class="card-header">
        <div class="card-title-tw">🔔 Alertas</div>
        <?php $totalAlertas = ($vencidos > 0 ? 1 : 0) + ($porVencer > 0 ? 1 : 0) + (($pm['n_pendientes'] ?? 0) > 0 ? 1 : 0); ?>
        <span class="tw-count"><?= $totalAlertas ?></span>
      </div>
      <div class="dash-alertas">
        <?php if ($vencidos > 0): ?>
        <div class="dash-alerta red">
          <span class="dash-alerta-icon">🔴</span>
          <div>
            <div class="dash-alerta-title"><?= $vencidos ?> contratos vencidos</div>
            <div class="dash-alerta-sub">Atención requerida</div>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($porVencer > 0): ?>
        <div class="dash-alerta amber">
          <span class="dash-alerta-icon">⚠️</span>
          <div>
            <div class="dash-alerta-title"><?= $porVencer ?> vencen en 30 días</div>
            <div class="dash-alerta-sub">Renovar pronto</div>
          </div>
        </div>
        <?php endif; ?>
        <?php if (($pm['n_pendientes'] ?? 0) > 0): ?>
        <div class="dash-alerta blue">
          <span class="dash-alerta-icon">🕐</span>
          <div>
            <div class="dash-alerta-title"><?= $pm['n_pendientes'] ?> pagos sin confirmar</div>
            <div class="dash-alerta-sub">Revisar este mes</div>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($totalAlertas === 0): ?>
        <div class="dash-alerta-ok">✅ Todo al corriente</div>
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
              <tr><td colspan="5" class="dash-table-empty">
                ✅ Todos los pagos del mes están al corriente
              </td></tr>
            <?php else: foreach ($pendientes as $p): ?>
              <tr>
                <td><span class="td-code"><?= htmlspecialchars($p['local']) ?></span></td>
                <td class="td-muted"><?= htmlspecialchars($p['arrendatario']) ?></td>
                <td><span class="td-mono">$<?= number_format($p['monto'], 2) ?></span></td>
                <td class="td-muted">Día <?= $p['dia_pago'] ?></td>
                <td>
                  <?php if ($p['estatus'] === 'Vencido'): ?>
                    <span class="badge-estado badge-rojo"><span class="badge-dot"></span>Vencido</span>
                  <?php else: ?>
                    <span class="badge-estado badge-ambar"><span class="badge-dot"></span>Pendiente</span>
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
              <tr><td colspan="4" class="dash-table-empty">
                ✅ Sin contratos por vencer en 60 días
              </td></tr>
            <?php else: foreach ($proximosVencer as $cv):
              $dias = (int)$cv['dias_restantes'];
              $col  = $dias <= 15 ? 'var(--red)' : ($dias <= 30 ? 'var(--amber)' : 'var(--text2)');
            ?>
              <tr>
                <td><span class="td-code"><?= htmlspecialchars($cv['local']) ?></span></td>
                <td class="td-muted"><?= htmlspecialchars(explode(' ', $cv['arrendatario'])[0]) ?></td>
                <td><span class="td-date"><?= date('d/m/Y', strtotime($cv['fecha_fin'])) ?></span></td>
                <td><span class="td-mono" style="color:<?= $col ?>;font-weight:700;"><?= $dias ?>d</span></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div><!-- /content -->

<!-- ── PUENTE PHP → JS ── -->
<script>
const DASH_CHART_LABELS   = <?= json_encode($chartLabels) ?>;
const DASH_CHART_INGRESOS = <?= json_encode($chartIngresos) ?>;
const DASH_CHART_EGRESOS  = <?= json_encode($chartEgresos) ?>;
</script>
<script src="<?= $url_base ?>assets/js/dashboard_mod.js"></script>
