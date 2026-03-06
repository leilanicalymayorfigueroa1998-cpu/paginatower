<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? '';
$rol     = $_SESSION['rol']     ?? '';
$color   = isset($rol) ? obtenerColorRol($rol) : '#4f8ef7';
$uri     = $_SERVER['REQUEST_URI'] ?? '';
function isActive($path, $uri) { return (strpos($uri, $path) !== false) ? ' active' : ''; }
?>

<div class="sidebar">

  <!-- Brand -->
  <div style="display:flex;align-items:center;gap:10px;padding:18px 16px 16px;border-bottom:1px solid var(--border);">
    <div style="width:32px;height:32px;background:var(--accent);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:15px;flex-shrink:0;">T</div>
    <div>
      <div style="font-size:14px;font-weight:700;color:var(--text);line-height:1.2;">Sistema Tower</div>
      <div style="font-size:10.5px;color:var(--muted);">Gestión Inmobiliaria</div>
    </div>
  </div>

  <!-- Usuario -->
  <div class="user-panel">
    <div class="user-avatar" style="background:<?= $color ?>;"><?= strtoupper(substr($usuario,0,1)) ?></div>
    <div>
      <div class="user-name"><?= htmlspecialchars($usuario) ?></div>
      <div class="user-role"><?= ucfirst($rol) ?></div>
    </div>
  </div>

  <!-- Nav -->
  <div style="padding:8px 0;">

    <span style="font-size:10px;font-weight:700;color:var(--dim);letter-spacing:1.2px;text-transform:uppercase;padding:12px 20px 4px;display:block;">Principal</span>

    <a href="<?= $url_base ?>/secciones/dashboard/"  class="<?= isActive('dashboard',$uri) ?>">📊 Dashboard</a>
    <a href="<?= $url_base ?>/secciones/movimientos/" class="<?= isActive('movimientos',$uri) ?>">🧾 Administración</a>

    <a href="javascript:void(0)" onclick="togglePropiedades()" class="menu-toggle<?= isActive('propiedades',$uri) ?: isActive('locales',$uri) ?: isActive('servicios',$uri) ?: isActive('restricciones',$uri) ?>">
      🏠 Propiedades <span class="arrow" id="arrowProp">▼</span>
    </a>
    <div class="submenu" id="submenuPropiedades">
      <a href="<?= $url_base ?>/secciones/propiedades/" class="<?= isActive('/propiedades/',$uri) ?>">🏠 Propiedades</a>
      <a href="<?= $url_base ?>/secciones/locales/"     class="<?= isActive('/locales/',$uri) ?>">🏢 Locales</a>
      <a href="<?= $url_base ?>/secciones/servicios/"   class="<?= isActive('/servicios/',$uri) ?>">💧 Servicios</a>
      <a href="<?= $url_base ?>/secciones/restricciones/" class="<?= isActive('/restricciones/',$uri) ?>">⚠ Restricciones</a>
    </div>

    <span style="font-size:10px;font-weight:700;color:var(--dim);letter-spacing:1.2px;text-transform:uppercase;padding:12px 20px 4px;display:block;">Finanzas</span>

    <a href="<?= $url_base ?>/secciones/contrato/"    class="<?= isActive('contrato',$uri) ?>">📄 Contratos</a>
    <a href="<?= $url_base ?>/secciones/pagos/"       class="<?= isActive('pagos',$uri) ?>">💳 Pagos</a>

    <span style="font-size:10px;font-weight:700;color:var(--dim);letter-spacing:1.2px;text-transform:uppercase;padding:12px 20px 4px;display:block;">CRM</span>

    <a href="<?= $url_base ?>/secciones/dueños/"      class="<?= isActive('dueños',$uri) ?: isActive('due%C3%B1os',$uri) ?>">👤 Dueños</a>
    <a href="<?= $url_base ?>/secciones/arrendatario/" class="<?= isActive('arrendatario',$uri) ?>">🏘️ Arrendatario</a>
    <a href="<?= $url_base ?>/secciones/usuarios/"    class="<?= isActive('usuarios',$uri) ?>">⚙ Usuarios</a>

  </div>
</div>
