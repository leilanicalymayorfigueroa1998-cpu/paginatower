<?php /* RESTRICCIONES — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">🚫 Restricciones</div>
      <div class="tw-page-subtitle">
        Reglas por inmueble · <?= $total ?> restricciones en <?= $inmuebles ?> inmuebles
      </div>
    </div>
    <?php if ($puedeCrear): ?>
      <a class="btn btn-success" href="crear.php">+ Nueva Restricción</a>
    <?php endif; ?>
  </div>

  <!-- ── STATS ── -->
  <div class="rest-stats">

    <div class="rest-stat amber">
      <div class="rest-stat-icon">🚫</div>
      <div>
        <div class="rest-stat-val"><?= $total ?></div>
        <div class="rest-stat-lbl">Total Restricciones</div>
      </div>
    </div>

    <div class="rest-stat blue">
      <div class="rest-stat-icon">🏠</div>
      <div>
        <div class="rest-stat-val"><?= $inmuebles ?></div>
        <div class="rest-stat-lbl">Inmuebles con reglas</div>
      </div>
    </div>

    <div class="rest-stat violet">
      <div class="rest-stat-icon">📋</div>
      <div>
        <div class="rest-stat-val"><?= $promedio ?></div>
        <div class="rest-stat-lbl">Promedio por inmueble</div>
      </div>
    </div>

  </div>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        📋 Listado de Restricciones
        <span class="count-pill"><?= $total ?></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle no-dt" id="tablaRestricciones">

        <thead>
          <tr>
            <th>Inmueble</th>
            <th>Restricción</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($listaRestricciones as $value): ?>
          <tr>

            <td>
              <span class="td-code"><?= htmlspecialchars($value['codigo']) ?></span>
            </td>

            <td>
              <div class="rest-regla">
                <div class="rest-regla-icon">⚠️</div>
                <span><?= htmlspecialchars($value['restriccion']) ?></span>
              </div>
            </td>

            <td>
              <div class="acciones">
                <?php if ($puedeEditar): ?>
                  <a class="btn-accion btn-editar"
                     href="editar.php?txtID=<?= $value['id_restriccion'] ?>">✏️ Editar</a>
                <?php endif; ?>
                <?php if ($puedeEliminar): ?>
                  <a class="btn-accion btn-borrar"
                     href="eliminar.php?txtID=<?= $value['id_restriccion'] ?>"
                     onclick="return confirm('¿Eliminar esta restricción?')">🗑️ Borrar</a>
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
const REST_PUEDE_EDITAR   = <?= $puedeEditar   ? 'true' : 'false' ?>;
const REST_PUEDE_ELIMINAR = <?= $puedeEliminar ? 'true' : 'false' ?>;
</script>
<script src="<?= $url_base ?>assets/js/restricciones.js"></script>
