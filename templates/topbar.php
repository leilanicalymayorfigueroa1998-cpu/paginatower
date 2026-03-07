<?php
// Determina el título de la sección activa para el breadcrumb
$secciones = [
    'dashboard'    => 'Dashboard',
    'contrato'     => 'Contratos',
    'pagos'        => 'Pagos',
    'propiedades'  => 'Propiedades',
    'locales'      => 'Locales',
    'servicios'    => 'Servicios',
    'restricciones'=> 'Restricciones',
    'movimientos'  => 'Administración',
    'dueños'       => 'Dueños',
    'arrendatario' => 'Arrendatarios',
    'usuarios'     => 'Usuarios',
    'alertas'      => 'Alertas',
];
$uri_actual = $_SERVER['REQUEST_URI'] ?? '';
$seccion_actual = 'Sistema Tower';
foreach ($secciones as $key => $label) {
    if (strpos($uri_actual, $key) !== false) {
        $seccion_actual = $label;
        break;
    }
}
?>
<div class="topbar">
  <div class="page-title">
    <span style="color:var(--text3);">Sistema Tower</span>
    <span style="color:var(--text3);margin:0 7px;">›</span>
    <span style="color:var(--text);font-weight:600;"><?= htmlspecialchars($seccion_actual) ?></span>
  </div>
  <div class="top-icons">
    <a href="#" title="Mensajes"><i class="fas fa-comment"></i></a>
    <a href="<?= $url_base ?>alertas/" class="notification" title="Alertas">
      <i class="fas fa-bell"></i>
      <?php if (!empty($totalAlertas) && $totalAlertas > 0): ?>
        <span class="badge"><?= $totalAlertas ?></span>
      <?php endif; ?>
    </a>
    <div class="dropdown">
      <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" style="text-decoration:none;">
        <i class="fas fa-user"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li class="dropdown-item" style="color:var(--text2);font-size:12px;cursor:default;"><?= $_SESSION['usuario'] ?? '' ?></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger fw-semibold" href="<?= $url_base ?>cerrar.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</div>
