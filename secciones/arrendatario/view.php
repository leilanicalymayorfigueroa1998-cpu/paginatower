<?php /* ARRENDATARIO — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">🧑‍💼 Arrendatarios</div>
      <div class="tw-page-subtitle">
        Gestión de inquilinos registrados · <?= $total ?> registros
      </div>
    </div>
    <?php if ($puedeCrear): ?>
      <a class="btn btn-success" href="crear.php">+ Nuevo Arrendatario</a>
    <?php endif; ?>
  </div>

  <!-- ── STATS ── -->
  <div class="arr-stats">

    <div class="arr-stat blue">
      <div class="arr-stat-lbl">Total</div>
      <div class="arr-stat-val"><?= $total ?></div>
      <div class="arr-stat-sub">Arrendatarios</div>
    </div>

    <div class="arr-stat green">
      <div class="arr-stat-lbl">Con Aval</div>
      <div class="arr-stat-val"><?= $conAval ?></div>
      <div class="arr-stat-sub">Garantía registrada</div>
    </div>

    <div class="arr-stat amber">
      <div class="arr-stat-lbl">Sin Aval</div>
      <div class="arr-stat-val"><?= $sinAval ?></div>
      <div class="arr-stat-sub">Sin garantía</div>
    </div>

    <div class="arr-stat violet">
      <div class="arr-stat-lbl">Ciudades</div>
      <div class="arr-stat-val"><?= $ciudades ?></div>
      <div class="arr-stat-sub">Distintas ubicaciones</div>
    </div>

  </div>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        🧑‍💼 Listado de Arrendatarios
        <span class="count-pill"><?= $total ?></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle no-dt" id="tablaArrendatarios">

        <thead>
          <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Aval</th>
            <th>Correo Aval</th>
            <th>Dirección</th>
            <th>Ciudad</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($listaArrendatarios as $value): ?>
          <tr>

            <td>
              <div class="td-entity">
                <div class="entity-avatar"
                     style="background:var(--blue-g);color:var(--blue);">
                  <?= mb_strtoupper(mb_substr($value['nombre'], 0, 1)) ?>
                </div>
                <div>
                  <div class="entity-name"><?= htmlspecialchars($value['nombre']) ?></div>
                </div>
              </div>
            </td>

            <td>
              <span class="arr-phone">📞 <?= htmlspecialchars($value['telefono']) ?></span>
            </td>

            <td>
              <a class="td-link" href="mailto:<?= htmlspecialchars($value['correo']) ?>">
                <?= htmlspecialchars($value['correo']) ?>
              </a>
            </td>

            <td>
              <?php if (!empty($value['aval'])): ?>
                <span class="badge-estado badge-verde">
                  <span class="badge-dot"></span>
                  <?= htmlspecialchars($value['aval']) ?>
                </span>
              <?php else: ?>
                <span class="td-muted">Sin aval</span>
              <?php endif; ?>
            </td>

            <td class="td-muted">
              <?= !empty($value['correoaval'])
                    ? '<a class="td-link" href="mailto:'.htmlspecialchars($value['correoaval']).'">'.htmlspecialchars($value['correoaval']).'</a>'
                    : '—' ?>
            </td>

            <td class="td-muted arr-dir">
              <?= htmlspecialchars($value['direccion']) ?>
            </td>

            <td>
              <?php if (!empty($value['ciudad'])): ?>
                <span class="badge-estado badge-azul">
                  📍 <?= htmlspecialchars($value['ciudad']) ?>
                </span>
              <?php else: ?>
                <span class="td-muted">—</span>
              <?php endif; ?>
            </td>

            <td>
              <div class="acciones">
                <?php if ($puedeEditar): ?>
                  <a class="btn-accion btn-editar"
                     href="editar.php?txtID=<?= htmlspecialchars($value['id_arrendatario']) ?>">✏️ Editar</a>
                <?php endif; ?>
                <?php if ($puedeEliminar): ?>
                  <form method="POST" action="eliminar.php" style="display:inline;">
                    <input type="hidden" name="id_arrendatario"
                           value="<?= htmlspecialchars($value['id_arrendatario']) ?>">
                    <input type="hidden" name="csrf_token"
                           value="<?= generarTokenCSRF() ?>">
                    <button type="submit" class="btn-accion btn-borrar"
                            onclick="return confirm('¿Eliminar este arrendatario?');">
                      🗑️ Borrar
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </td>

          </tr>
          <?php endforeach; ?>

          <?php if (empty($listaArrendatarios)): ?>
            <tr>
              <td colspan="8" class="empty-state">
                No hay arrendatarios registrados.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>

      </table>
    </div>

  </div>

</div>

<!-- ── PUENTE PHP → JS ── -->
<script>
const ARR_PUEDE_EDITAR   = <?= $puedeEditar   ? 'true' : 'false' ?>;
const ARR_PUEDE_ELIMINAR = <?= $puedeEliminar ? 'true' : 'false' ?>;
</script>
<script src="<?= $url_base ?>assets/js/arrendatario.js"></script>
