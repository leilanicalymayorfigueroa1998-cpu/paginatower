<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'contratos', 'ver');

$consulta = $conexionBD->prepare("
    SELECT r.id_contrato, l.codigo AS local, a.nombre AS arrendatario,
           r.renta, r.deposito, r.adicional, r.fecha_inicio, r.fecha_fin,
           r.estatus, r.duracion,
           (SELECT IFNULL(SUM(p.monto),0) FROM pagos p
            WHERE p.id_contrato = r.id_contrato
            AND p.estatus IN ('Pendiente','Vencido')) AS deuda_total
    FROM contratos r
    INNER JOIN locales l ON l.id_local = r.id_local
    INNER JOIN arrendatarios a ON a.id_arrendatario = r.id_arrendatario
    ORDER BY r.id_contrato DESC
");
$consulta->execute();
$listaRentas = $consulta->fetchAll(PDO::FETCH_ASSOC);

$total = count($listaRentas);
$hoy   = date('Y-m-d');
$activos = $vencidos = $rentaTotal = $deudaTotal = 0;

foreach ($listaRentas as $r) {
    $rentaTotal += $r['renta'];
    $deudaTotal += $r['deuda_total'];
    if ($r['estatus'] === 'Activa' && (empty($r['fecha_fin']) || $r['fecha_fin'] >= $hoy))
        $activos++;
    elseif (!empty($r['fecha_fin']) && $r['fecha_fin'] < $hoy)
        $vencidos++;
}
$rentaFmt = '$' . ($rentaTotal >= 1000
    ? number_format($rentaTotal / 1000, 1) . 'k'
    : number_format($rentaTotal, 0));

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

  <!-- Page header -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">📋 Contratos</div>
      <div class="tw-page-subtitle">Gestión de contratos de arrendamiento · <?= $total ?> registros</div>
    </div>
    <div class="tw-page-actions">
      <?php if (tienePermiso($conexionBD, $idRol, 'contratos', 'crear')): ?>
        <a class="btn btn-success" href="crear.php">＋ Nuevo contrato</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- KPI cards -->
  <div class="tw-stats tw-stats-4">

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,#10b981,#34d399)"></div>
      <div class="glow" style="background:#10b981"></div>
      <div class="tw-stat-label">✅ Activos</div>
      <div class="tw-stat-value" style="color:var(--green);"><?= $activos ?></div>
      <div class="tw-stat-meta">de <?= $total ?> totales</div>
    </div>

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,#f43f5e,#fb923c)"></div>
      <div class="glow" style="background:#f43f5e"></div>
      <div class="tw-stat-label">⚠️ Vencidos</div>
      <div class="tw-stat-value" style="color:var(--red);"><?= $vencidos ?></div>
      <div class="tw-stat-meta">requieren atención</div>
    </div>

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,#06b6d4,#3b82f6)"></div>
      <div class="glow" style="background:#06b6d4"></div>
      <div class="tw-stat-label">💰 Renta mensual total</div>
      <div class="tw-stat-value" style="color:var(--cyan);"><?= $rentaFmt ?></div>
      <div class="tw-stat-meta">ingresos proyectados</div>
    </div>

    <div class="tw-stat">
      <div class="bar" style="background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div>
      <div class="glow" style="background:#f59e0b"></div>
      <div class="tw-stat-label">📌 Deuda total</div>
      <div class="tw-stat-value" style="color:var(--amber);">$<?= number_format($deudaTotal, 0) ?></div>
      <div class="tw-stat-meta">en contratos vencidos</div>
    </div>

  </div>

  <!-- Tabla -->
  <div class="card">
    <div class="card-header">
      <div class="card-title-tw">
        Todos los contratos
        <span class="tw-count"><?= $total ?></span>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="tablaContratos">
          <thead>
            <tr>
              <th>Local</th>
              <th>Arrendatario</th>
              <th>Renta</th>
              <th>Depósito</th>
              <th>Adicional</th>
              <th>Fecha Inicio</th>
              <th>Fecha Fin</th>
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
              <td><?= htmlspecialchars($v['local']) ?></td>
              <td><?= htmlspecialchars($v['arrendatario']) ?></td>
              <td style="font-family:'DM Mono',monospace;font-weight:600;">$<?= number_format($v['renta'], 2) ?></td>
              <td style="color:var(--text2);font-family:'DM Mono',monospace;">$<?= number_format($v['deposito'], 2) ?></td>
              <td style="color:var(--text2);font-family:'DM Mono',monospace;">$<?= number_format($v['adicional'], 2) ?></td>
              <td style="color:var(--text2);font-size:12px;"><?= date('d/m/Y', strtotime($v['fecha_inicio'])) ?></td>
              <td style="color:var(--text2);font-size:12px;"><?= !empty($v['fecha_fin']) ? date('d/m/Y', strtotime($v['fecha_fin'])) : '—' ?></td>
              <td style="color:var(--text2);font-size:12px;"><?= $diff->y ?>a <?= $diff->m ?>m</td>

              <td><?php
                switch ($v['estatus']) {
                  case 'Finalizada': echo '<span class="badge bg-secondary">Finalizada</span>'; break;
                  case 'Cancelada':  echo '<span class="badge bg-dark">Cancelada</span>'; break;
                  case 'Pendiente':  echo '<span class="badge bg-warning">Pendiente</span>'; break;
                  case 'Activa':
                    if ($dur === 'Indefinido')
                      echo '<span class="badge bg-primary">Activa</span>';
                    elseif (!empty($v['fecha_fin']) && $v['fecha_fin'] < $hoy)
                      echo '<span class="badge bg-danger">Vencida</span>';
                    elseif (!empty($v['fecha_fin']) && $v['fecha_fin'] <= date('Y-m-d', strtotime('+5 days')))
                      echo '<span class="badge bg-warning">Por vencer</span>';
                    else
                      echo '<span class="badge bg-success">Activa</span>';
                    break;
                  default: echo '<span class="badge bg-secondary">'.htmlspecialchars($v['estatus']).'</span>';
                }
              ?></td>

              <td><?= $dur === 'Indefinido'
                ? '<span class="badge bg-info">Indefinido</span>'
                : '<span class="badge bg-secondary">Plazo fijo</span>' ?></td>

              <td>
                <?php if ($v['deuda_total'] > 0): ?>
                  <span class="tw-debt pos">↑ $<?= number_format($v['deuda_total'], 2) ?></span>
                <?php else: ?>
                  <span class="tw-debt zero">✓ $0.00</span>
                <?php endif; ?>
              </td>

              <td>
                <div style="display:flex;gap:5px;">
                  <?php if (tienePermiso($conexionBD, $idRol, 'contratos', 'editar')): ?>
                    <a class="tw-act tw-act-edit" href="editar.php?txtID=<?= (int)$v['id_contrato'] ?>">Editar</a>
                  <?php endif; ?>
                  <?php if (tienePermiso($conexionBD, $idRol, 'contratos', 'eliminar')): ?>
                    <form action="eliminar.php" method="post" style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                      <input type="hidden" name="txtID"      value="<?= (int)$v['id_contrato'] ?>">
                      <button type="submit" class="tw-act tw-act-del"
                        onclick="return confirm('¿Seguro que deseas eliminar?');">Borrar</button>
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

</div><!-- /.content -->

<?php include('../../templates/pie.php'); ?>
