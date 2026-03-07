<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? '';
$rol     = $_SESSION['rol']     ?? '';
$color   = isset($rol) ? obtenerColorRol($rol) : '#3b82f6';
$uri     = $_SERVER['REQUEST_URI'] ?? '';
function isActive($path, $uri) { return (strpos($uri, $path) !== false) ? ' active' : ''; }
?>

<div class="sidebar">

  <!-- Brand -->
  <div style="padding:20px 18px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:11px;">
    <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#06b6d4);border-radius:9px;display:grid;place-items:center;font-size:17px;flex-shrink:0;box-shadow:0 0 18px rgba(59,130,246,0.35);">🏢</div>
    <div>
      <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:15px;color:var(--text);line-height:1.1;">Sistema Tower</div>
      <div style="font-size:10.5px;color:var(--text2);margin-top:1px;">Sistema Inmobiliario</div>
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
  <div>

    <span style="padding:12px 18px 4px;display:block;font-size:9.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);">Principal</span>

    <a href="<?= $url_base ?>/secciones/dashboard/"   class="<?= isActive('dashboard',$uri) ?>">📊 Dashboard</a>
    <a href="<?= $url_base ?>/secciones/movimientos/" class="<?= isActive('movimientos',$uri) ?>">⚙️ Administración</a>

    <span style="padding:12px 18px 4px;display:block;font-size:9.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);">Inmuebles</span>

    <a href="javascript:void(0)" onclick="togglePropiedades()" class="menu-toggle<?= isActive('propiedades',$uri) ?: isActive('locales',$uri) ?: isActive('servicios',$uri) ?: isActive('restricciones',$uri) ?>">
      🏘️ Propiedades <span class="arrow" id="arrowProp">▾</span>
    </a>
    <div class="submenu" id="submenuPropiedades">
      <a href="<?= $url_base ?>/secciones/propiedades/"  class="<?= isActive('/propiedades/',$uri) ?>">🏠 Propiedades</a>
      <a href="<?= $url_base ?>/secciones/locales/"      class="<?= isActive('/locales/',$uri) ?>">🏢 Locales</a>
      <a href="<?= $url_base ?>/secciones/servicios/"    class="<?= isActive('/servicios/',$uri) ?>">💧 Servicios</a>
      <a href="<?= $url_base ?>/secciones/restricciones/" class="<?= isActive('/restricciones/',$uri) ?>">⚠️ Restricciones</a>
    </div>

    <span style="padding:12px 18px 4px;display:block;font-size:9.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);">Finanzas</span>

    <a href="<?= $url_base ?>/secciones/contrato/" class="<?= isActive('contrato',$uri) ?>">📋 Contratos</a>
    <a href="<?= $url_base ?>/secciones/pagos/"    class="<?= isActive('pagos',$uri) ?>">💳 Pagos</a>

    <span style="padding:12px 18px 4px;display:block;font-size:9.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);">CRM</span>

    <a href="<?= $url_base ?>/secciones/dueños/"       class="<?= isActive('dueños',$uri) ?: isActive('due%C3%B1os',$uri) ?>">👤 Dueños</a>
    <a href="<?= $url_base ?>/secciones/arrendatario/" class="<?= isActive('arrendatario',$uri) ?>">🏠 Arrendatario</a>
    <a href="<?= $url_base ?>/secciones/usuarios/"     class="<?= isActive('usuarios',$uri) ?>">◎ Usuarios</a>

  </div>
</div>
