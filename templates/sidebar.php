<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario = $_SESSION['usuario'] ?? '';
$rol = $_SESSION['rol'] ?? '';
$color = isset($rol) ? obtenerColorRol($rol) : '#6c757d';
?>

<div class="sidebar">

    <div class="user-panel">
        <div class="user-avatar" style="background-color: <?php echo $color; ?>;">
            <?php echo strtoupper(substr($_SESSION['usuario'], 0, 1)); ?>
        </div>

        <div class="user-info">
            <div class="user-name"><?php echo $_SESSION['usuario']; ?></div>
            <div class="user-role"><?php echo ucfirst($rol); ?></div>
        </div>
    </div>

    <a href="#">📊 Dashboard</a>
    <a href="<?php echo $url_base; ?>/secciones/movimientos/">🧾 Administración</a>

    <a href="javascript:void(0)" onclick="togglePropiedades()" class="menu-toggle">
        🏠 Propiedades
        <span class="arrow">▼</span>
    </a>

    <div class="submenu" id="submenuPropiedades">
        <a href="<?php echo $url_base; ?>/secciones/propiedades/">🏠 Propiedades</a>
        <a href="<?php echo $url_base; ?>/secciones/locales/">🏢 Locales</a>
        <a href="<?php echo $url_base; ?>/secciones/servicios/">💧 Servicios</a>
        <a href="<?php echo $url_base; ?>/secciones/restricciones/">⚠ Restricciones</a>
    </div>

    <a href="<?php echo $url_base; ?>/secciones/contrato/">📄 Contrato</a>
    <a href="<?php echo $url_base; ?>/secciones/pagos/">💳 Pagos</a>
    <a href="<?php echo $url_base; ?>/secciones/dueños/">👤 Dueños</a>
    <a href="<?php echo $url_base; ?>/secciones/arrendatario/">🏘️ Arrendatario</a>
    <a href="<?php echo $url_base; ?>/secciones/usuarios/">⚙ Usuarios</a>

</div>
