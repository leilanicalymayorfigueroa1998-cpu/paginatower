<?php /* USUARIOS — view.php  |  Solo HTML. Variables vienen de index.php */ ?>

<div class="content">

  <!-- ── PAGE HEADER ── -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">👤 Usuarios</div>
      <div class="tw-page-subtitle">
        Gestión de accesos al sistema · <?= $total ?> usuarios
      </div>
    </div>
    <a class="btn btn-success" href="agregar.php">+ Agregar Usuario</a>
  </div>

  <!-- ── STATS ── -->
  <div class="usr-stats">

    <div class="usr-stat blue">
      <div class="usr-stat-lbl">Total Usuarios</div>
      <div class="usr-stat-val"><?= $total ?></div>
      <div class="usr-stat-sub">En el sistema</div>
    </div>

    <div class="usr-stat violet">
      <div class="usr-stat-lbl">Roles Distintos</div>
      <div class="usr-stat-val"><?= $totalRoles ?></div>
      <div class="usr-stat-sub">Perfiles de acceso</div>
    </div>

    <?php foreach (array_slice($roles, 0, 2, true) as $rol => $cnt): ?>
    <div class="usr-stat green">
      <div class="usr-stat-lbl"><?= htmlspecialchars($rol) ?></div>
      <div class="usr-stat-val"><?= $cnt ?></div>
      <div class="usr-stat-sub">Usuarios con este rol</div>
    </div>
    <?php endforeach; ?>

  </div>

  <!-- ── TABLA ── -->
  <div class="card-tabla">

    <div class="card-header">
      <div class="tabla-titulo">
        👤 Listado de Usuarios
        <span class="count-pill"><?= $total ?></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle no-dt" id="tablaUsuarios">

        <thead>
          <tr>
            <th>#</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Arrendatario</th>
            <th>Dueño</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($listaUsuarios as $value): ?>
          <tr>

            <td><span class="td-code"><?= htmlspecialchars($value['id']) ?></span></td>

            <td>
              <div class="td-entity">
                <div class="entity-avatar"
                     style="background:var(--violet-g);color:var(--violet);">
                  <?= mb_strtoupper(mb_substr($value['usuario'], 0, 1)) ?>
                </div>
                <div class="entity-name"><?= htmlspecialchars($value['usuario']) ?></div>
              </div>
            </td>

            <td>
              <a class="td-link" href="mailto:<?= htmlspecialchars($value['correo']) ?>">
                <?= htmlspecialchars($value['correo']) ?>
              </a>
            </td>

            <td>
              <span class="badge-estado badge-violeta">
                <span class="badge-dot"></span>
                <?= htmlspecialchars($value['rol'] ?? '—') ?>
              </span>
            </td>

            <td class="td-muted">
              <?= !empty($value['cliente']) ? htmlspecialchars($value['cliente']) : '—' ?>
            </td>

            <td class="td-muted">
              <?= !empty($value['dueno']) ? htmlspecialchars($value['dueno']) : '—' ?>
            </td>

            <td>
              <div class="acciones">
                <a class="btn-accion btn-editar"
                   href="editar.php?txtID=<?= htmlspecialchars($value['id']) ?>">✏️ Editar</a>
                <a class="btn-accion btn-borrar"
                   href="index.php?txtID=<?= htmlspecialchars($value['id']) ?>"
                   onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">🗑️ Borrar</a>
              </div>
            </td>

          </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>

  </div>

</div>

<script src="<?= $url_base ?>assets/js/usuarios_mod.js"></script>
