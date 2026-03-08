<?php /* LOCALES — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">🏘️ Inmuebles</div>
      <div class="tw-page-subtitle">
        Gestión de unidades y locales · <?= $total ?> registros
      </div>
    </div>
    <?php if ($puedeCrear): ?>
      <a class="btn btn-success" href="crear.php">+ Agregar Inmueble</a>
    <?php endif; ?>
  </div>

  <!-- ── STATS ── -->
  <div class="loc-stats">

    <div class="loc-stat blue">
      <div class="loc-stat-lbl">Total Inmuebles</div>
      <div class="loc-stat-val"><?= $total ?></div>
      <div class="loc-stat-sub">En el sistema</div>
    </div>

    <div class="loc-stat red">
      <div class="loc-stat-lbl">Ocupados</div>
      <div class="loc-stat-val"><?= $ocupados ?></div>
      <div class="loc-stat-sub">Con contrato activo</div>
    </div>

    <div class="loc-stat green">
      <div class="loc-stat-lbl">Disponibles</div>
      <div class="loc-stat-val"><?= $disponibles ?></div>
      <div class="loc-stat-sub">Listos para rentar</div>
    </div>

    <div class="loc-stat amber">
      <div class="loc-stat-lbl">Ocupación</div>
      <div class="loc-stat-val"><?= $pctOcup ?>%</div>
      <div class="loc-stat-sub">
        <div class="loc-mini-bar-bg">
          <div class="loc-mini-bar-fill" style="width:<?= $pctOcup ?>%"></div>
        </div>
      </div>
    </div>

  </div>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        🏠 Listado de Inmuebles
        <span class="count-pill"><?= $total ?></span>
      </div>
      <!-- Chips filtro rápido -->
      <div class="loc-chips">
        <button class="chip-filtro active" onclick="locFiltrar('todos', this)">Todos</button>
        <button class="chip-filtro"        onclick="locFiltrar('Ocupado', this)">Ocupados</button>
        <button class="chip-filtro"        onclick="locFiltrar('Disponible', this)">Disponibles</button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle" id="tablaLocales">

        <thead>
          <tr>
            <th>Código</th>
            <th>Medidas</th>
            <th>Descripción</th>
            <th>Estacionamiento</th>
            <th>Estatus</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($listaLocales as $value):
            /* ── Normalizar medidas ── */
            $medida = strtolower(str_replace(' ', '', $value['medidas'] ?? ''));
            if (strpos($medida, 'x') !== false) {
                $partes = explode('x', $medida);
                if (count($partes) === 2) {
                    $medida = (floatval($partes[0]) * floatval($partes[1])) . ' m²';
                }
            }
            $medida  = str_replace(['m3','m^3','m2','m^2'], ' m²', $medida);
            $estatus = $value['estatus'] ?? 'Disponible';
            $ocup    = ($estatus === 'Ocupado');
          ?>
          <tr data-estatus="<?= htmlspecialchars($estatus) ?>">

            <td>
              <span class="td-code"><?= htmlspecialchars($value['codigo']) ?></span>
            </td>

            <td>
              <span class="loc-medida">📐 <?= htmlspecialchars($medida) ?></span>
            </td>

            <td class="td-muted loc-desc">
              <?= htmlspecialchars($value['descripcion']) ?>
            </td>

            <td class="td-muted">
              🚗 <?= htmlspecialchars($value['estacionamiento']) ?>
            </td>

            <td>
              <?php if ($ocup): ?>
                <span class="badge-estado badge-rojo">
                  <span class="badge-dot"></span>Ocupado
                </span>
              <?php else: ?>
                <span class="badge-estado badge-verde">
                  <span class="badge-dot"></span>Disponible
                </span>
              <?php endif; ?>
            </td>

            <td>
              <div class="acciones">
                <?php if ($puedeEditar): ?>
                  <a class="btn-accion btn-editar"
                     href="editar.php?txtID=<?= $value['id_local'] ?>">✏️ Editar</a>
                <?php endif; ?>
                <?php if ($puedeEliminar): ?>
                  <a class="btn-accion btn-borrar"
                     href="eliminar.php?txtID=<?= $value['id_local'] ?>"
                     onclick="return confirm('¿Eliminar este inmueble?')">🗑️ Borrar</a>
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
const LOC_PUEDE_EDITAR   = <?= $puedeEditar   ? 'true' : 'false' ?>;
const LOC_PUEDE_ELIMINAR = <?= $puedeEliminar ? 'true' : 'false' ?>;
</script>
<script src="<?= $url_base ?>assets/js/locales.js"></script>
