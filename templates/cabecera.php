<?php
$url_base = "http://localhost/paginatower/";
?>

<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.6/css/dataTables.dataTables.min.css">

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo $url_base; ?>assets/css/cabecera.css">
    <link rel="stylesheet" href="<?php echo $url_base; ?>assets/css/tablas.css">

</head>

<body>

    <div class="sidebar">

        <h4>🏢 Sistema Tower</h4>

        <a href="#">📊 Dashboard</a>
        <a href="<?php echo $url_base; ?>/secciones/movimientos/">🧾 Administracion</a>
        <a href="javascript:void(0)" onclick="togglePropiedades()">
            🏠 Propiedades
        </a>

        <div class="submenu" id="submenuPropiedades">
            <a href="<?php echo $url_base; ?>/secciones/propiedades/">🏠 Propiedades</a>
            <a href="<?php echo $url_base; ?>/secciones/locales/">🏢 Locales</a>
            <a href="<?php echo $url_base; ?>/secciones/servicios/">💧🔌 Servicios</a>
            <a href="<?php echo $url_base; ?>/secciones/restricciones/">⚠️ Restricciones</a>
        </div>

        <a href="<?php echo $url_base; ?>/secciones/rentas/">📄 Rentas</a>
        <a href="<?php echo $url_base; ?>/secciones/pagos/">💳 Pagos</a>

        <a href="<?php echo $url_base; ?>/secciones/dueños/">👤 Dueños</a>
        <a href="<?php echo $url_base; ?>/secciones/clientes/">👥 Clientes</a>
        <a href="<?php echo $url_base; ?>/secciones/usuarios/">⚙ Usuarios</a>

        <a href="#" style="position:absolute; bottom:20px; color:#f87171;">
            🚪 Cerrar sesión
        </a>
    </div>

    <script>
        function togglePropiedades() {
            const menu = document.getElementById("submenuPropiedades");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }
    </script>

    <div class="content">
        <div class="container-fluid">