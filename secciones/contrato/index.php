<?php
/* ════════════════════════════════════════════
   CONTRATOS — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/contrato.css
   JS  → assets/js/contrato.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'contratos', 'ver');

$puedeCrear    = tienePermiso($conexionBD, $idRol, 'contratos', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'contratos', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'contratos', 'eliminar');

$consulta = $conexionBD->prepare("
    SELECT r.id_contrato, l.codigo AS local, a.nombre AS arrendatario,
           r.renta, r.deposito, r.adicional, r.fecha_inicio, r.fecha_fin,
           r.estatus, r.duracion,
           (SELECT IFNULL(SUM(p.monto),0) FROM pagos p
            WHERE p.id_contrato = r.id_contrato
            AND p.estatus IN ('Pendiente','Vencido')) AS deuda_total
    FROM contratos r
    INNER JOIN locales l ON l.id_local = r.id_local
    INNER JOIN arrendatarios a ON a.id_arrendatario = r.id_arrendatario
    ORDER BY r.id_contrato DESC
");
$consulta->execute();
$listaRentas = $consulta->fetchAll(PDO::FETCH_ASSOC);

/* ─── Estadísticas ─── */
$total      = count($listaRentas);
$hoy        = date('Y-m-d');
$activos    = $vencidos = $rentaTotal = $deudaTotal = 0;

foreach ($listaRentas as $r) {
    $rentaTotal += $r['renta'];
    $deudaTotal += $r['deuda_total'];
    if ($r['estatus'] === 'Activa' && (empty($r['fecha_fin']) || $r['fecha_fin'] >= $hoy))
        $activos++;
    elseif (!empty($r['fecha_fin']) && $r['fecha_fin'] < $hoy)
        $vencidos++;
}

$rentaFmt = '$' . ($rentaTotal >= 1000
    ? number_format($rentaTotal / 1000, 1) . 'k'
    : number_format($rentaTotal, 0));

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/contrato.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
