<?php /* SERVICIOS — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">⚡ Servicios</div>
      <div class="tw-page-subtitle">
        Control de CFE y Agua por inmueble · <?= $total ?> registros
      </div>
    </div>
    <?php if ($puedeCrear): ?>
      <a class="btn btn-success" href="crear.php">+ Nuevo Servicio</a>
    <?php endif; ?>
  </div>

  <!-- ── STATS ── -->
  <div class="svc-stats">

    <div class="svc-stat blue">
      <div class="svc-stat-icon">⚡</div>
      <div>
        <div class="svc-stat-val"><?= $cfeActivos ?> / <?= $total ?></div>
        <div class="svc-stat-lbl">CFE Activos</div>
      </div>
    </div>

    <div class="svc-stat cyan">
      <div class="svc-stat-icon">💧</div>
      <div>
        <div class="svc-stat-val"><?= $aguaActivos ?> / <?= $total ?></div>
        <div class="svc-stat-lbl">Agua Activa</div>
      </div>
    </div>

    <div class="svc-stat red">
      <div class="svc-stat-icon">⚠️</div>
      <div>
        <div class="svc-stat-val"><?= $total - $cfeActivos ?></div>
        <div class="svc-stat-lbl">CFE Inactivos</div>
      </div>
    </div>

    <div class="svc-stat green">
      <div class="svc-stat-icon">✅</div>
      <div>
        <div class="svc-stat-val"><?= $total ?></div>
        <div class="svc-stat-lbl">Total Inmuebles</div>
      </div>
    </div>

  </div>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        🔌 Servicios por Inmueble
        <span class="count-pill"><?= $total ?></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle no-dt" id="tablaServicios">

        <thead>
          <tr>
            <th>Inmueble</th>
            <th>CFE</th>
            <th>Número CFE</th>
            <th>Agua</th>
            <th>Contrato Agua</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($lista_servicios as $value):
            $cfeActivo  = !empty($value['cfe']);
            $aguaActivo = !empty($value['agua']);
          ?>
          <tr>

            <td>
              <span class="td-code"><?= htmlspecialchars($value['codigo']) ?></span>
            </td>

            <td>
              <?php if ($cfeActivo): ?>
                <span class="badge-estado badge-verde">
                  <span class="badge-dot"></span>⚡ Activo
                </span>
              <?php else: ?>
                <span class="badge-estado badge-rojo">
                  <span class="badge-dot"></span>Inactivo
                </span>
              <?php endif; ?>
            </td>

            <td>
              <?php if (!empty($value['contrato_cfe'])): ?>
                <span class="td-mono svc-num">
                  🔢 <?= htmlspecialchars($value['contrato_cfe']) ?>
                </span>
              <?php else: ?>
                <span class="td-muted">—</span>
              <?php endif; ?>
            </td>

            <td>
              <?php if ($aguaActivo): ?>
                <span class="badge-estado badge-azul">
                  <span class="badge-dot"></span>💧 Activo
                </span>
              <?php else: ?>
                <span class="badge-estado badge-rojo">
                  <span class="badge-dot"></span>Inactivo
                </span>
              <?php endif; ?>
            </td>

            <td class="td-muted">
              <?= !empty($value['contrato_agua'])
                    ? htmlspecialchars($value['contrato_agua'])
                    : '—' ?>
            </td>

            <td>
              <div class="acciones">
                <?php if ($puedeEditar): ?>
                  <a class="btn-accion btn-editar"
                     href="editar.php?txtID=<?= $value['id_servicio'] ?>">✏️ Editar</a>
                <?php endif; ?>
                <?php if ($puedeEliminar): ?>
                  <a class="btn-accion btn-borrar"
                     href="eliminar.php?txtID=<?= $value['id_servicio'] ?>"
                     onclick="return confirm('¿Eliminar este servicio?')">🗑️ Borrar</a>
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
const SVC_PUEDE_EDITAR   = <?= $puedeEditar   ? 'true' : 'false' ?>;
const SVC_PUEDE_ELIMINAR = <?= $puedeEliminar ? 'true' : 'false' ?>;
</script>
<script src="<?= $url_base ?>assets/js/servicios_mod.js"></script>
