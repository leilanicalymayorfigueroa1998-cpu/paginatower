<?php /* PAGOS — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">💳 Pagos</div>
      <div class="tw-page-subtitle">
        <?= date('F Y') ?> · <?= count($contratos) ?> contratos activos
      </div>
    </div>
    <?php if ($puedeCrear): ?>
      <a class="btn btn-success" href="crear.php">+ Registrar Pago</a>
    <?php endif; ?>
  </div>

  <!-- ── STATS ── -->
  <div class="pag-stats">

    <div class="pag-stat red">
      <div class="pag-stat-icon">⚠️</div>
      <div>
        <div class="pag-stat-val"><?= $totalVencidos ?></div>
        <div class="pag-stat-lbl">Vencidos</div>
      </div>
    </div>

    <div class="pag-stat amber">
      <div class="pag-stat-icon">⏳</div>
      <div>
        <div class="pag-stat-val"><?= $totalPendientes ?></div>
        <div class="pag-stat-lbl">Pendientes</div>
      </div>
    </div>

    <div class="pag-stat green">
      <div class="pag-stat-icon">✅</div>
      <div>
        <div class="pag-stat-val"><?= $totalPagados ?></div>
        <div class="pag-stat-lbl">Pagados este mes</div>
      </div>
    </div>

    <div class="pag-stat blue">
      <div class="pag-stat-icon">🏢</div>
      <div>
        <div class="pag-stat-val"><?= count($contratos) ?></div>
        <div class="pag-stat-lbl">Contratos activos</div>
      </div>
    </div>

  </div>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        💳 Pagos — <span style="font-weight:400;color:var(--text2);"><?= date('F Y') ?></span>
        <span class="count-pill"><?= count($contratos) ?></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle no-dt" id="tablaPagos">

        <thead>
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
            $anio     = (int)date('Y'); $mes = (int)date('m');
            $dia      = max(1, min(28, (int)($c['dia_pago'] ?? 1)));
            $max      = (int)date('t', mktime(0,0,0,$mes,1,$anio));
            $fechaProg = date('d/m/Y', mktime(0,0,0,$mes,min($dia,$max),$anio));

            $estMes = $c['estatus_mes'] ?? null;
            if ($estMes === 'Pagado')
                [$badgeClass, $badgeLabel] = ['badge-verde', 'Pagado'];
            elseif ($estMes === 'Vencido')
                [$badgeClass, $badgeLabel] = ['badge-rojo', 'Vencido'];
            elseif ($estMes === 'Pendiente')
                [$badgeClass, $badgeLabel] = ['badge-ambar', 'Pendiente'];
            else
                [$badgeClass, $badgeLabel] = ['badge-gris', 'Sin generar'];
          ?>
          <tr>

            <td><span class="td-code"><?= htmlspecialchars($c['local']) ?></span></td>

            <td>
              <div class="td-entity">
                <div class="entity-avatar" style="background:var(--green-g);color:var(--green);">
                  <?= mb_strtoupper(mb_substr($c['arrendatario'], 0, 1)) ?>
                </div>
                <div class="entity-name"><?= htmlspecialchars($c['arrendatario']) ?></div>
              </div>
            </td>

            <td>
              <span class="pag-dia">Día <?= $c['dia_pago'] ?></span>
              <div class="td-muted" style="font-size:10.5px;margin-top:2px;">
                <?= $c['duracion'] === 'Indefinido' ? '∞ Indefinido' : 'Fijo' ?>
              </div>
            </td>

            <td><span class="td-date"><?= $fechaProg ?></span></td>

            <td><span class="td-mono pag-monto">$<?= number_format($c['renta'], 2) ?></span></td>

            <td>
              <?= !empty($c['metodo_mes'])
                    ? '<span class="badge-estado badge-gris">'.htmlspecialchars($c['metodo_mes']).'</span>'
                    : '<span class="td-muted">—</span>' ?>
            </td>

            <td>
              <span class="badge-estado <?= $badgeClass ?>">
                <span class="badge-dot"></span><?= $badgeLabel ?>
              </span>
            </td>

            <td>
              <div class="pag-historial">
                <span class="pag-his-badge green" title="Pagados"><?= $c['pagados'] ?>✓</span>
                <span class="pag-his-badge red"   title="Vencidos"><?= $c['vencidos'] ?>⚠</span>
                <span class="pag-his-badge amber" title="Pendientes"><?= $c['pendientes'] ?>⏳</span>
              </div>
            </td>

            <td>
              <div class="acciones">
                <a class="btn-accion btn-ver"
                   href="detalle_pagos.php?id=<?= $c['id_contrato'] ?>">👁️ Detalle</a>
                <?php if ($puedeCrear && $estMes !== 'Pagado'): ?>
                  <a class="btn-accion btn-registrar"
                     href="crear.php?id_contrato=<?= $c['id_contrato'] ?>">+ Registrar</a>
                <?php endif; ?>
              </div>
            </td>

          </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>

  </div>

</div>

<!-- ── PUENTE PHP → JS ── -->
<script>
const PAG_PUEDE_CREAR = <?= $puedeCrear ? 'true' : 'false' ?>;
</script>
<script src="<?= $url_base ?>assets/js/pagos.js"></script>
