<?php /* CONTRATOS — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">📋 Contratos</div>
      <div class="tw-page-subtitle">
        Gestión de contratos de arrendamiento · <?= $total ?> registros
      </div>
    </div>
    <?php if ($puedeCrear): ?>
      <a class="btn btn-success" href="crear.php">＋ Nuevo Contrato</a>
    <?php endif; ?>
  </div>

  <!-- ── STATS ── -->
  <div class="con-stats">

    <div class="con-stat green">
      <div class="con-stat-lbl">✅ Activos</div>
      <div class="con-stat-val"><?= $activos ?></div>
      <div class="con-stat-sub">de <?= $total ?> totales</div>
    </div>

    <div class="con-stat red">
      <div class="con-stat-lbl">⚠️ Vencidos</div>
      <div class="con-stat-val"><?= $vencidos ?></div>
      <div class="con-stat-sub">Requieren atención</div>
    </div>

    <div class="con-stat cyan">
      <div class="con-stat-lbl">💰 Renta mensual</div>
      <div class="con-stat-val"><?= $rentaFmt ?></div>
      <div class="con-stat-sub">Ingresos proyectados</div>
    </div>

    <div class="con-stat amber">
      <div class="con-stat-lbl">📌 Deuda total</div>
      <div class="con-stat-val">$<?= number_format($deudaTotal, 0) ?></div>
      <div class="con-stat-sub">Pagos pendientes/vencidos</div>
    </div>

  </div>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        📋 Todos los contratos
        <span class="count-pill"><?= $total ?></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle no-dt" id="tablaContratos">

        <thead>
          <tr>
            <th>Local</th>
            <th>Arrendatario</th>
            <th>Renta</th>
            <th>Depósito</th>
            <th>Adicional</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Antigüedad</th>
            <th>Estatus</th>
            <th>Duración</th>
            <th>Deuda</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($listaRentas as $v):
            $dur  = $v['duracion'] ?? 'Fijo';
            $diff = (new DateTime($v['fecha_inicio']))->diff(new DateTime());
          ?>
          <tr>

            <td><span class="td-code"><?= htmlspecialchars($v['local']) ?></span></td>

            <td>
              <div class="td-entity">
                <div class="entity-avatar" style="background:var(--violet-g);color:var(--violet);">
                  <?= mb_strtoupper(mb_substr($v['arrendatario'], 0, 1)) ?>
                </div>
                <div class="entity-name"><?= htmlspecialchars($v['arrendatario']) ?></div>
              </div>
            </td>

            <td><span class="td-mono con-monto">$<?= number_format($v['renta'], 2) ?></span></td>
            <td><span class="td-muted td-mono">$<?= number_format($v['deposito'], 2) ?></span></td>
            <td><span class="td-muted td-mono">$<?= number_format($v['adicional'], 2) ?></span></td>

            <td><span class="td-date"><?= date('d/m/Y', strtotime($v['fecha_inicio'])) ?></span></td>
            <td><span class="td-date"><?= !empty($v['fecha_fin']) ? date('d/m/Y', strtotime($v['fecha_fin'])) : '—' ?></span></td>
            <td><span class="td-muted"><?= $diff->y ?>a <?= $diff->m ?>m</span></td>

            <td><?php
              switch ($v['estatus']) {
                case 'Finalizada':
                  echo '<span class="badge-estado badge-gris">Finalizada</span>'; break;
                case 'Cancelada':
                  echo '<span class="badge-estado badge-rojo">Cancelada</span>'; break;
                case 'Pendiente':
                  echo '<span class="badge-estado badge-ambar">Pendiente</span>'; break;
                case 'Activa':
                  if ($dur === 'Indefinido')
                    echo '<span class="badge-estado badge-azul"><span class="badge-dot"></span>Activa</span>';
                  elseif (!empty($v['fecha_fin']) && $v['fecha_fin'] < $hoy)
                    echo '<span class="badge-estado badge-rojo"><span class="badge-dot"></span>Vencida</span>';
                  elseif (!empty($v['fecha_fin']) && $v['fecha_fin'] <= date('Y-m-d', strtotime('+5 days')))
                    echo '<span class="badge-estado badge-ambar"><span class="badge-dot"></span>Por vencer</span>';
                  else
                    echo '<span class="badge-estado badge-verde"><span class="badge-dot"></span>Activa</span>';
                  break;
                default:
                  echo '<span class="badge-estado badge-gris">'.htmlspecialchars($v['estatus']).'</span>';
              }
            ?></td>

            <td><?= $dur === 'Indefinido'
              ? '<span class="badge-estado badge-azul">∞ Indefinido</span>'
              : '<span class="badge-estado badge-gris">Plazo fijo</span>' ?></td>

            <td>
              <?php if ($v['deuda_total'] > 0): ?>
                <span class="deuda-tag deuda-pendiente">↑ $<?= number_format($v['deuda_total'], 2) ?></span>
              <?php else: ?>
                <span class="deuda-tag deuda-cero">✓ $0.00</span>
              <?php endif; ?>
            </td>

            <td>
              <div class="acciones">
                <?php if ($puedeEditar): ?>
                  <a class="btn-accion btn-editar"
                     href="editar.php?txtID=<?= (int)$v['id_contrato'] ?>">✏️ Editar</a>
                <?php endif; ?>
                <?php if ($puedeEliminar): ?>
                  <form action="eliminar.php" method="post" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                    <input type="hidden" name="txtID"      value="<?= (int)$v['id_contrato'] ?>">
                    <button type="submit" class="btn-accion btn-borrar"
                            onclick="return confirm('¿Seguro que deseas eliminar?');">🗑️ Borrar</button>
                  </form>
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
const CON_PUEDE_EDITAR   = <?= $puedeEditar   ? 'true' : 'false' ?>;
const CON_PUEDE_ELIMINAR = <?= $puedeEliminar ? 'true' : 'false' ?>;
</script>
<script src="<?= $url_base ?>assets/js/contrato.js"></script>
