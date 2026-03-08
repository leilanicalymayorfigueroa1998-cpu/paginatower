<?php /* MOVIMIENTOS — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">💸 Movimientos Financieros</div>
      <div class="tw-page-subtitle">
        Registro de ingresos y egresos · <?= $totalMov ?> registros
      </div>
    </div>
    <?php if ($puedeCrear): ?>
      <a class="btn btn-success" href="crear.php">+ Agregar Movimiento</a>
    <?php endif; ?>
  </div>

  <!-- ── ESTADÍSTICAS ── -->
  <div class="mov-stats">

    <div class="mov-stat green">
      <div class="mov-stat-lbl">Total Abonos</div>
      <div class="mov-stat-val">$<?= number_format($totalAbonos, 0, '.', ',') ?></div>
      <div class="mov-stat-sub">Ingresos registrados</div>
    </div>

    <div class="mov-stat red">
      <div class="mov-stat-lbl">Total Cargos</div>
      <div class="mov-stat-val">$<?= number_format($totalCargos, 0, '.', ',') ?></div>
      <div class="mov-stat-sub">Egresos registrados</div>
    </div>

    <div class="mov-stat <?= $balance >= 0 ? 'blue' : 'amber' ?>">
      <div class="mov-stat-lbl">Balance Neto</div>
      <div class="mov-stat-val">
        <?= $balance >= 0 ? '+' : '' ?>$<?= number_format(abs($balance), 0, '.', ',') ?>
      </div>
      <div class="mov-stat-sub">Abonos menos cargos</div>
    </div>

    <div class="mov-stat violet">
      <div class="mov-stat-lbl">Movimientos</div>
      <div class="mov-stat-val"><?= $totalMov ?></div>
      <div class="mov-stat-sub">Total en el sistema</div>
    </div>

  </div>

  <!-- ── MENSAJES ── -->
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'guardado'): ?>
    <div class="alert alert-success mb-3">✅ Movimiento guardado correctamente.</div>
  <?php endif; ?>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        📊 Todos los movimientos
        <span class="count-pill"><?= $totalMov ?></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle" id="tablaMovimientos">

        <thead>
          <tr>
            <th>Fecha</th>
            <th>Propiedad</th>
            <th>Operación</th>
            <th>Nota</th>
            <th>Abono</th>
            <th>Cargo</th>
            <th>Origen</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($listaMovimientos as $mov):
            $abono  = floatval($mov['abono'] ?? 0);
            $cargo  = floatval($mov['cargo'] ?? 0);
            $origen = strtoupper($mov['origen'] ?? '');
          ?>
          <tr>

            <td>
              <span class="td-date"><?= htmlspecialchars($mov['fecha']) ?></span>
            </td>

            <td>
              <span class="td-code"><?= htmlspecialchars($mov['propiedad']) ?></span>
            </td>

            <td>
              <span class="badge-estado badge-azul">
                <span class="badge-dot"></span>
                <?= htmlspecialchars($mov['tipo_codigo']) ?>
              </span>
            </td>

            <td class="td-muted">
              <?= htmlspecialchars($mov['nota']) ?: '<span style="color:var(--text3)">—</span>' ?>
            </td>

            <td>
              <?php if ($abono > 0): ?>
                <span class="deuda-tag deuda-cero">↑ $<?= number_format($abono, 2) ?></span>
              <?php else: ?>
                <span class="td-muted">—</span>
              <?php endif; ?>
            </td>

            <td>
              <?php if ($cargo > 0): ?>
                <span class="deuda-tag deuda-pendiente">↓ $<?= number_format($cargo, 2) ?></span>
              <?php else: ?>
                <span class="td-muted">—</span>
              <?php endif; ?>
            </td>

            <td>
              <span class="badge-estado <?= $origen === 'CUENTA' ? 'badge-violeta' : 'badge-gris' ?>">
                <?= $origen === 'CUENTA' ? '🏦' : '💵' ?>
                <?= htmlspecialchars($origen) ?>
              </span>
            </td>

            <td>
              <div class="acciones">
                <?php if ($puedeEditar): ?>
                  <a class="btn-accion btn-editar"
                     href="editar.php?txtID=<?= $mov['id_movimiento'] ?>">✏️ Editar</a>
                <?php endif; ?>
                <?php if ($puedeEliminar): ?>
                  <a class="btn-accion btn-borrar"
                     href="eliminar.php?txtID=<?= $mov['id_movimiento'] ?>"
                     onclick="return confirm('¿Eliminar este movimiento?')">🗑️ Borrar</a>
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
const MOV_PUEDE_EDITAR   = <?= $puedeEditar   ? 'true' : 'false' ?>;
const MOV_PUEDE_ELIMINAR = <?= $puedeEliminar ? 'true' : 'false' ?>;
</script>
<script src="<?= $url_base ?>assets/js/movimientos.js"></script>
