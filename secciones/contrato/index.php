<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'contratos', 'ver');

$consulta = $conexionBD->prepare("SELECT
    r.id_contrato,
    l.codigo AS local,
    a.nombre AS arrendatario,
    r.renta, r.deposito, r.adicional,
    r.fecha_inicio, r.fecha_fin, r.estatus, r.duracion,
    (SELECT IFNULL(SUM(p.monto),0) FROM pagos p
     WHERE p.id_contrato = r.id_contrato
     AND p.estatus IN ('Pendiente','Vencido')) AS deuda_total
FROM contratos r
INNER JOIN locales l ON l.id_local = r.id_local
INNER JOIN arrendatarios a ON a.id_arrendatario = r.id_arrendatario
ORDER BY r.id_contrato DESC");

$consulta->execute();
$listaRentas = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$total = count($listaRentas);
$hoy = date('Y-m-d');
$activos = $vencidos = $rentaTotal = $deudaTotal = 0;

foreach ($listaRentas as $r) {
    $rentaTotal += $r['renta'];
    $deudaTotal += $r['deuda_total'];
    if ($r['estatus'] === 'Activa' && (empty($r['fecha_fin']) || $r['fecha_fin'] >= $hoy))
        $activos++;
    elseif (!empty($r['fecha_fin']) && $r['fecha_fin'] < $hoy)
        $vencidos++;
}

$rentaFmt = '$' . ($rentaTotal >= 1000 ? number_format($rentaTotal/1000,1).'k' : number_format($rentaTotal,0));

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

  <!-- STATS -->
  <div class="stats-row">
    <div class="stat-card sc-green">
      <span class="sc-label">Contratos activos</span>
      <span class="sc-value"><?= $activos ?></span>
      <span class="sc-sub">de <?= $total ?> totales</span>
    </div>
    <div class="stat-card sc-red">
      <span class="sc-label">Vencidos</span>
      <span class="sc-value"><?= $vencidos ?></span>
      <span class="sc-sub">requieren atención</span>
    </div>
    <div class="stat-card sc-blue">
      <span class="sc-label">Renta mensual total</span>
      <span class="sc-value"><?= $rentaFmt ?></span>
      <span class="sc-sub">ingresos proyectados</span>
    </div>
    <div class="stat-card sc-yellow">
      <span class="sc-label">Deuda pendiente</span>
      <span class="sc-value">$<?= number_format($deudaTotal,0) ?></span>
      <span class="sc-sub">en contratos vencidos</span>
    </div>
  </div>

  <!-- TABLA -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Todos los contratos</span>
      <?php if (tienePermiso($conexionBD,$idRol,'contratos','crear')): ?>
        <a class="btn btn-success" href="crear.php">＋ Nuevo contrato</a>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="tablaContratos">
          <thead>
            <tr>
              <th>Local</th><th>Arrendatario</th><th>Renta</th><th>Depósito</th>
              <th>Adicional</th><th>Fecha Inicio</th><th>Fecha Fin</th>
              <th>Antigüedad</th><th>Estatus</th><th>Duración</th><th>Deuda</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($listaRentas as $v):
            $dur  = $v['duracion'] ?? 'Fijo';
            $ini  = new DateTime($v['fecha_inicio']);
            $diff = $ini->diff(new DateTime());
          ?>
            <tr>
              <td><?= htmlspecialchars($v['local']) ?></td>
              <td><?= htmlspecialchars($v['arrendatario']) ?></td>
              <td>$<?= number_format($v['renta'],2) ?></td>
              <td>$<?= number_format($v['deposito'],2) ?></td>
              <td>$<?= number_format($v['adicional'],2) ?></td>
              <td><?= date('d/m/Y',strtotime($v['fecha_inicio'])) ?></td>
              <td><?= !empty($v['fecha_fin']) ? date('d/m/Y',strtotime($v['fecha_fin'])) : '—' ?></td>
              <td><?= $diff->y ?> años, <?= $diff->m ?> meses</td>
              <td>
                <?php
                switch($v['estatus']) {
                  case 'Finalizada': echo '<span class="badge bg-secondary">Finalizada</span>'; break;
                  case 'Cancelada':  echo '<span class="badge bg-dark">Cancelada</span>';  break;
                  case 'Pendiente':  echo '<span class="badge bg-warning">Pendiente</span>'; break;
                  case 'Activa':
                    if ($dur==='Indefinido')
                      echo '<span class="badge bg-primary">Activa</span>';
                    elseif (!empty($v['fecha_fin']) && $v['fecha_fin'] < $hoy)
                      echo '<span class="badge bg-danger">Vencida</span>';
                    elseif (!empty($v['fecha_fin']) && $v['fecha_fin'] <= date('Y-m-d',strtotime('+5 days')))
                      echo '<span class="badge bg-warning">Por vencer</span>';
                    else
                      echo '<span class="badge bg-success">Activa</span>';
                    break;
                  default: echo '<span class="badge bg-secondary">'.htmlspecialchars($v['estatus']).'</span>';
                }
                ?>
              </td>
              <td>
                <?= $dur==='Indefinido'
                  ? '<span class="badge bg-info">Indefinido</span>'
                  : '<span class="badge bg-secondary">Plazo fijo</span>' ?>
              </td>
              <td>
                <?= $v['deuda_total']>0
                  ? '<span class="badge bg-danger">$'.number_format($v['deuda_total'],2).'</span>'
                  : '<span class="badge bg-success">$0.00</span>' ?>
              </td>
              <td>
                <?php if(tienePermiso($conexionBD,$idRol,'contratos','editar')): ?>
                  <a class="btn btn-primary btn-sm" href="editar.php?txtID=<?= (int)$v['id_contrato'] ?>">Editar</a>
                <?php endif; ?>
                <?php if(tienePermiso($conexionBD,$idRol,'contratos','eliminar')): ?>
                  <form action="eliminar.php" method="post" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                    <input type="hidden" name="txtID" value="<?= (int)$v['id_contrato'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm"
                      onclick="return confirm('¿Seguro que deseas eliminar este contrato?');">Borrar</button>
                  </form>
                <?php endif; ?>
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
