<?php /* PROPIEDADES — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── MENSAJES ── -->
  <?php if (isset($_GET['mensaje'])): ?>
    <?php if ($_GET['mensaje'] === 'creado'): ?>
      <div class="alert alert-success mb-3">✅ Propiedad creada correctamente.</div>
    <?php elseif ($_GET['mensaje'] === 'editado'): ?>
      <div class="alert alert-primary mb-3">✏️ Propiedad actualizada.</div>
    <?php elseif ($_GET['mensaje'] === 'eliminado'): ?>
      <div class="alert alert-danger mb-3">🗑️ Propiedad eliminada.</div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">🏠 Propiedades</div>
      <div class="tw-page-subtitle">
        Gestión de inmuebles registrados · <?= $total ?> propiedades
      </div>
    </div>
    <?php if ($puedeCrear): ?>
      <a class="btn btn-success" href="crear.php">+ Nueva Propiedad</a>
    <?php endif; ?>
  </div>

  <!-- ── STATS POR TIPO ── -->
  <div class="prop-stats">
    <?php foreach ($tipos as $tipo => $cnt):
      $ico = $iconosTipo[$tipo]   ?? '🏘️';
      $col = $coloresTipo[$tipo]  ?? 'gris';
    ?>
    <div class="prop-stat <?= $col ?>">
      <div class="prop-stat-icon"><?= $ico ?></div>
      <div>
        <div class="prop-stat-val"><?= $cnt ?></div>
        <div class="prop-stat-lbl"><?= htmlspecialchars($tipo) ?></div>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="prop-stat total">
      <div class="prop-stat-icon">📋</div>
      <div>
        <div class="prop-stat-val"><?= $total ?></div>
        <div class="prop-stat-lbl">Total</div>
      </div>
    </div>
  </div>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        🏘️ Listado de Propiedades
        <span class="count-pill"><?= $total ?></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle no-dt" id="tablaPropiedades">

        <thead>
          <tr>
            <th>Código</th>
            <th>Dueño</th>
            <th>Tipo</th>
            <th>Dirección</th>
            <th>Coordenadas</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($listaPropiedades as $p):
            $tipo      = $p['tipo'] ?? '';
            $ico       = $iconosTipo[$tipo] ?? '🏘️';
            $lat       = floatval($p['latitud']);
            $lng       = floatval($p['longitud']);
            $hasCoords = ($lat != 0 && $lng != 0);
          ?>
          <tr>

            <td>
              <span class="td-code"><?= htmlspecialchars($p['codigo']) ?></span>
            </td>

            <td>
              <div class="td-entity">
                <div class="entity-avatar"
                     style="background:var(--blue-g);color:var(--blue);">
                  <?= mb_strtoupper(mb_substr($p['dueno'], 0, 1)) ?>
                </div>
                <div class="entity-name"><?= htmlspecialchars($p['dueno']) ?></div>
              </div>
            </td>

            <td>
              <span class="badge-estado badge-gris"><?= $ico ?> <?= htmlspecialchars($tipo) ?></span>
            </td>

            <td class="td-muted prop-dir">
              📍 <?= htmlspecialchars($p['direccion']) ?>
            </td>

            <td>
              <?php if ($hasCoords): ?>
                <a class="td-link prop-coords"
                   href="https://maps.google.com/?q=<?= $lat ?>,<?= $lng ?>"
                   target="_blank" title="Ver en Google Maps">
                  🗺️ <?= number_format($lat, 4) ?>, <?= number_format($lng, 4) ?>
                </a>
              <?php else: ?>
                <span class="td-muted">Sin coordenadas</span>
              <?php endif; ?>
            </td>

            <td>
              <div class="acciones">
                <?php if ($puedeEditar): ?>
                  <a class="btn-accion btn-editar"
                     href="editar.php?txtID=<?= $p['id_propiedad'] ?>">✏️ Editar</a>
                <?php endif; ?>
                <?php if ($puedeEliminar): ?>
                  <a class="btn-accion btn-borrar"
                     href="eliminar.php?txtID=<?= $p['id_propiedad'] ?>"
                     onclick="return confirm('¿Eliminar esta propiedad?')">🗑️ Borrar</a>
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
const PROP_PUEDE_EDITAR   = <?= $puedeEditar   ? 'true' : 'false' ?>;
const PROP_PUEDE_ELIMINAR = <?= $puedeEliminar ? 'true' : 'false' ?>;
</script>
<script src="<?= $url_base ?>assets/js/propiedades_mod.js"></script>
