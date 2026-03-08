<?php
/* ════════════════════════════════════════════
   DUEÑOS — index.php
   Lógica PHP + HTML
   CSS  → assets/css/duenos.css
   JS   → assets/js/duenos.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

$idRol = $_SESSION['id_rol'] ?? null;
if (!$idRol) { header("Location: ../../login.php"); exit(); }

verificarPermiso($conexionBD, $idRol, 'duenos', 'ver');
$puedeCrear    = tienePermiso($conexionBD, $idRol, 'duenos', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'duenos', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'duenos', 'eliminar');

/* ─── STATS GLOBALES ─────────────────────────────────────── */
$totalDuenos = $conexionBD->query(
    "SELECT COUNT(*) FROM duenos"
)->fetchColumn();

$totalProps = $conexionBD->query(
    "SELECT COUNT(*) FROM propiedades"
)->fetchColumn();

$propsRentadas = $conexionBD->query("
    SELECT COUNT(DISTINCT l.id_propiedad)
    FROM locales l
    INNER JOIN contratos c ON c.id_local = l.id_local
    WHERE c.estatus = 'Activa'
")->fetchColumn();

$rentaMensual = $conexionBD->query("
    SELECT IFNULL(SUM(c.renta), 0)
    FROM contratos c
    WHERE c.estatus = 'Activa'
")->fetchColumn();

/* ─── DUEÑOS CON RENTAS ACTIVAS ──────────────────────────── */
$stmtDuenos = $conexionBD->query("
    SELECT
        d.id_dueno,
        d.nombre,
        d.telefono,
        d.correo,
        COUNT(DISTINCT p.id_propiedad) AS total_props,
        IFNULL(SUM(CASE WHEN c.estatus = 'Activa' THEN c.renta ELSE 0 END), 0) AS renta_activa
    FROM duenos d
    LEFT JOIN propiedades p  ON p.id_dueno    = d.id_dueno
    LEFT JOIN locales l      ON l.id_propiedad = p.id_propiedad
    LEFT JOIN contratos c    ON c.id_local    = l.id_local
    GROUP BY d.id_dueno, d.nombre, d.telefono, d.correo
    ORDER BY d.id_dueno DESC
");
$listaDuenos = $stmtDuenos->fetchAll(PDO::FETCH_ASSOC);

/* ─── PROPIEDADES POR DUEÑO ──────────────────────────────── */
$stmtProps = $conexionBD->query("
    SELECT
        p.id_propiedad,
        p.id_dueno,
        p.codigo,
        p.direccion,
        t.nombre AS tipo,
        l.estatus AS estatus_local,
        IFNULL(
            (SELECT c.renta
             FROM contratos c
             WHERE c.id_local = l.id_local AND c.estatus = 'Activa'
             LIMIT 1),
            0
        ) AS renta
    FROM propiedades p
    LEFT JOIN tipo_propiedad t ON t.id_tipo     = p.id_tipo
    LEFT JOIN locales l        ON l.id_propiedad = p.id_propiedad
    ORDER BY p.id_dueno, p.codigo
");
$todasProps = $stmtProps->fetchAll(PDO::FETCH_ASSOC);

/* Indexar propiedades por id_dueno */
$propsPorDueno = [];
foreach ($todasProps as $prop) {
    $propsPorDueno[$prop['id_dueno']][] = $prop;
}

$mesActual  = (int) date('n');
$anioActual = date('Y');
$csrfToken  = generarTokenCSRF();

$rentaFmt = '$' . ($rentaMensual >= 1000
    ? number_format($rentaMensual / 1000, 1) . 'k'
    : number_format($rentaMensual, 0));

/* ─── TEMPLATES ──────────────────────────────────────────── */
$pagina_css = ['assets/css/duenos.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

  <!-- PAGE HEADER -->
  <div class="tw-page-header">
    <div>
      <div class="tw-page-title">👤 Dueños</div>
      <div class="tw-page-subtitle">
        Gestión de propietarios e ingresos · <?= $totalDuenos ?> registros
      </div>
    </div>
    <div class="tw-page-actions">
      <?php if ($puedeCrear): ?>
        <button class="btn btn-success" onclick="dnoOpenModal()">＋ Nuevo Dueño</button>
      <?php endif; ?>
    </div>
  </div>

  <!-- ESTADÍSTICAS -->
  <div class="dno-stats">
    <div class="dno-stat blue">
      <div class="dno-stat-lbl">Total Dueños</div>
      <div class="dno-stat-val"><?= $totalDuenos ?></div>
      <div class="dno-stat-sub">Registrados en sistema</div>
    </div>
    <div class="dno-stat green">
      <div class="dno-stat-lbl">Propiedades</div>
      <div class="dno-stat-val"><?= $totalProps ?></div>
      <div class="dno-stat-sub">Total registradas</div>
    </div>
    <div class="dno-stat amber">
      <div class="dno-stat-lbl">Rentadas</div>
      <div class="dno-stat-val"><?= $propsRentadas ?></div>
      <div class="dno-stat-sub">Con contrato activo</div>
    </div>
    <div class="dno-stat violet">
      <div class="dno-stat-lbl">Ingreso Mensual</div>
      <div class="dno-stat-val"><?= $rentaFmt ?></div>
      <div class="dno-stat-sub">MXN contratos activos</div>
    </div>
  </div>

  <!-- MENSAJES -->
  <?php if (isset($_GET['mensaje'])): ?>
    <?php if ($_GET['mensaje'] === 'creado'): ?>
      <div class="alert alert-success mb-3">✅ Dueño creado correctamente.</div>
    <?php elseif ($_GET['mensaje'] === 'editado'): ?>
      <div class="alert alert-primary mb-3">✏️ Dueño actualizado.</div>
    <?php elseif ($_GET['mensaje'] === 'eliminado'): ?>
      <div class="alert alert-danger mb-3">🗑️ Dueño eliminado.</div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- CARD PRINCIPAL -->
  <div class="card">

    <!-- Controles -->
    <div class="dno-controls">
      <div class="dno-controls-left">
        <span style="font-size:12px;color:var(--text3);">Mostrar</span>
        <select class="form-select"
                style="width:70px;padding:6px 8px;font-size:12px;"
                id="dnoPageSize"
                onchange="dnoRender()">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
        </select>
        <span style="font-size:12px;color:var(--text3);">entradas</span>
      </div>
      <div class="dno-controls-right">
        <div class="dno-view-toggle">
          <button class="dno-view-btn active" id="btnViewTable"
                  onclick="dnoSetView('table',this)" title="Vista tabla">☰</button>
          <button class="dno-view-btn" id="btnViewCards"
                  onclick="dnoSetView('cards',this)" title="Vista tarjetas">⊞</button>
        </div>
        <div class="dno-search">
          <span class="dno-search-icon">🔍</span>
          <input type="text" id="dnoSearch"
                 placeholder="Buscar dueño..."
                 oninput="dnoFilter(this.value)">
        </div>
      </div>
    </div>

    <!-- Vista Tabla -->
    <div id="dnoTableView">
      <table class="dno-table no-dt">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Propiedades</th>
            <th>Renta mensual</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="dnoDuenos"></tbody>
      </table>
      <div class="dno-footer">
        <div class="dno-footer-info" id="dnoInfo"></div>
        <div class="dno-pag-btns"    id="dnoPag"></div>
      </div>
    </div>

    <!-- Vista Tarjetas -->
    <div id="dnoCardsView" class="hidden">
      <div class="dno-cards" id="dnoCardsGrid"></div>
    </div>

  </div>

</div>

<!-- MODAL NUEVO DUEÑO -->
<div class="dno-modal-overlay" id="dnoModal">
  <div class="dno-modal">
    <div class="dno-modal-title">
      <span>➕ Nuevo Dueño</span>
      <button class="dno-modal-close" onclick="dnoCloseModal()">✕</button>
    </div>
    <form method="POST" action="guardar.php">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="dno-modal-row">
        <div class="dno-modal-group">
          <label class="dno-modal-label">Nombre(s)</label>
          <input class="dno-modal-input" type="text" name="nombre_p"
                 placeholder="Ej. María" required>
        </div>
        <div class="dno-modal-group">
          <label class="dno-modal-label">Apellidos</label>
          <input class="dno-modal-input" type="text" name="nombre_a"
                 placeholder="Ej. García López">
        </div>
      </div>
      <div class="dno-modal-group">
        <label class="dno-modal-label">Correo Electrónico</label>
        <input class="dno-modal-input" type="email" name="correo"
               placeholder="correo@ejemplo.com">
      </div>
      <div class="dno-modal-group">
        <label class="dno-modal-label">Teléfono</label>
        <input class="dno-modal-input" type="tel" name="telefono"
               placeholder="55 0000 0000" required>
      </div>
      <div class="dno-modal-footer">
        <button type="button" class="btn btn-secondary btn-sm"
                onclick="dnoCloseModal()">Cancelar</button>
        <button type="submit" class="btn btn-success btn-sm">✓ Guardar Dueño</button>
      </div>
    </form>
  </div>
</div>

<!-- PUENTE PHP → JS (variables necesarias para duenos.js) -->
<script>
const dnoDatos           = <?= json_encode(array_values($listaDuenos), JSON_UNESCAPED_UNICODE) ?>;
const dnoProps           = <?= json_encode($propsPorDueno,             JSON_UNESCAPED_UNICODE) ?>;
const MES_ACTUAL         = <?= $mesActual ?>;
const ANIO_ACTUAL        = <?= $anioActual ?>;
const DNO_PUEDE_EDITAR   = <?= $puedeEditar   ? 'true' : 'false' ?>;
const DNO_PUEDE_ELIMINAR = <?= $puedeEliminar ? 'true' : 'false' ?>;
const DNO_CSRF_TOKEN     = <?= json_encode($csrfToken) ?>;
</script>
<script src="<?= $url_base ?>assets/js/duenos.js"></script>

<?php include('../../templates/pie.php'); ?>
