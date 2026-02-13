<?php
include(__DIR__ . '/../includes/auth.php');
include(__DIR__ . '/../includes/helpers.php');

$rol = $_SESSION['rol'] ?? '';
$color = obtenerColorRol($rol);

$idUsuario = $_SESSION['id'] ?? null;  // Asegurar que exista el ID del usuario
$totalAlertas = 0;  // Inicializar contador

if ($idUsuario) {

    include(__DIR__ . '/../alertas/alertas_automaticas.php');  // Ejecutar alertas automáticas

    // Contar alertas no leídas
    $consultaAlertas = $conexionBD->prepare("SELECT COUNT(*) 
        FROM alertas 
        WHERE id_usuario = :id 
        AND leida = 0 ");

    $consultaAlertas->bindParam(':id', $idUsuario);
    $consultaAlertas->execute();
    $totalAlertas = $consultaAlertas->fetchColumn();
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Inmobiliario Tower</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.dataTables.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Estilos propios -->
    <link rel="stylesheet" href="<?php echo $url_base; ?>assets/css/cabecera.css">
    <link rel="stylesheet" href="<?php echo $url_base; ?>assets/css/tablas.css">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- ===== TOPBAR ===== -->
    <div class="topbar">
        <div class="page-title">
            🏢 Sistema Tower
        </div>

        <div class="top-icons">

            <!-- ===== MENSAJES ===== -->
            <a href="#"><i class="fas fa-comment"></i></a>

            <!-- ===== ALERTAS ===== -->
            <a href="<?php echo $url_base . 'alertas/'; ?>" class="notification">
                <i class="fas fa-bell"></i>

                <?php if ($totalAlertas > 0): ?>
                    <span class="badge"><?php echo $totalAlertas; ?></span>
                <?php endif; ?>
            </a>

            <!-- ===== USUARIOS ===== -->
            <div class="dropdown">
                <a href="#" class="text-white dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li class="dropdown-item text-muted small">
                        <?php echo $_SESSION['usuario']; ?>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger fw-semibold"
                            href="<?php echo $url_base; ?>cerrar.php">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>

        </div>

    </div>

    <!-- ===== SIDEBAR ===== -->
    <div class="sidebar">

        <!-- Usuario -->
        <div class="user-panel">
            <div class="user-avatar" style="background-color: <?php echo $color; ?>;">
                <?php echo strtoupper(substr($_SESSION['usuario'], 0, 1)); ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['usuario']; ?></div>
                <div class="user-role"><?php echo ucfirst($rol); ?></div>
            </div>
        </div>

        <!-- Menú -->
        <a href="#">📊 Dashboard</a>
        <a href="<?php echo $url_base; ?>/secciones/movimientos/">🧾 Administración</a>

        <a href="javascript:void(0)" onclick="togglePropiedades()">🏠 Propiedades</a>
        <div class="submenu" id="submenuPropiedades">
            <a href="<?php echo $url_base; ?>/secciones/propiedades/">🏠 Propiedades</a>
            <a href="<?php echo $url_base; ?>/secciones/locales/">🏢 Locales</a>
            <a href="<?php echo $url_base; ?>/secciones/servicios/">💧🔌 Servicios</a>
            <a href="<?php echo $url_base; ?>/secciones/restricciones/">⚠️ Restricciones</a>
        </div>

        <a href="<?php echo $url_base; ?>/secciones/rentas/">📄 Rentas</a>
        <a href="<?php echo $url_base; ?>/secciones/pagos/">💳 Pagos</a>
        <a href="<?php echo $url_base; ?>/secciones/dueños/">👤 Dueños</a>
        <a href="<?php echo $url_base; ?>/secciones/arrendatario/"> 🏘️ Arrendatario</a>
        <a href="<?php echo $url_base; ?>/secciones/usuarios/">⚙ Usuarios</a>
    </div>

    <!-- ===== CONTENIDO ===== -->
    <div class="content">
        <div class="container-fluid">