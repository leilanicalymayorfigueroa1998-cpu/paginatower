<?php
/* ════════════════════════════════════════════
   USUARIOS — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/usuarios_mod.css
   JS  → assets/js/usuarios_mod.js

   NOTA: La eliminación se hace vía URL GET (diseño original).
         Se mantiene por compatibilidad.
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

/* ── Eliminar si viene txtID por GET ── */
if (isset($_GET['txtID'])) {
    $txtID    = $_GET['txtID'];
    $consulta = $conexionBD->prepare("DELETE FROM usuarios WHERE id = :id");
    $consulta->bindParam(':id', $txtID);
    $consulta->execute();
    header("Location: index.php");
    exit();
}

$consulta = $conexionBD->prepare("
    SELECT
        u.id,
        u.usuario,
        u.correo,
        r.nombre AS rol,
        c.nombre AS cliente,
        d.nombre AS dueno
    FROM usuarios u
    LEFT JOIN roles r    ON u.id_rol     = r.id_rol
    LEFT JOIN clientes c ON u.id_cliente = c.id_cliente
    LEFT JOIN duenos d   ON u.id_dueno   = d.id_dueno
");
$consulta->execute();
$listaUsuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

/* ─── Estadísticas ─── */
$total     = count($listaUsuarios);
$roles     = array_count_values(array_filter(array_column($listaUsuarios, 'rol')));
$totalRoles = count($roles);

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/usuarios_mod.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
