<div class="topbar">
  <div class="page-title">🏢 Sistema Tower</div>
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
        <li class="dropdown-item" style="color:var(--muted);font-size:12px;cursor:default;"><?= $_SESSION['usuario'] ?? '' ?></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger fw-semibold" href="<?= $url_base ?>cerrar.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</div>
