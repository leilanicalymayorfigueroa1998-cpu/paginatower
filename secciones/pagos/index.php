<?php
/* ════════════════════════════════════════════
   PAGOS — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/pagos.css
   JS  → assets/js/pagos.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PagosService.php');

$idRol = $_SESSION['id_rol'] ?? null;
if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

$puedeCrear = tienePermiso($conexionBD, $idRol, 'pagos', 'crear');

/* ── Marcar vencidos automáticamente ── */
$conexionBD->prepare("
    UPDATE pagos SET estatus = 'Vencido'
    WHERE estatus = 'Pendiente' AND fecha_pago < CURDATE()
")->execute();

$periodo = date('Y-m-01');

/* ── Un renglón por contrato activo con info del mes ── */
$consulta = $conexionBD->prepare("
    SELECT
        c.id_contrato,
        c.duracion,
        c.dia_pago,
        c.renta,
        l.codigo       AS local,
        a.nombre       AS arrendatario,
        pm.id_pago     AS pago_mes_id,
        pm.fecha_pago  AS fecha_pago_mes,
        pm.monto       AS monto_mes,
        pm.metodo_pago AS metodo_mes,
        pm.estatus     AS estatus_mes,
        COUNT(ph.id_pago)                                          AS total_pagos,
        SUM(CASE WHEN ph.estatus='Pagado'    THEN 1 ELSE 0 END)   AS pagados,
        SUM(CASE WHEN ph.estatus='Pendiente' THEN 1 ELSE 0 END)   AS pendientes,
        SUM(CASE WHEN ph.estatus='Vencido'   THEN 1 ELSE 0 END)   AS vencidos
    FROM contratos c
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    LEFT  JOIN pagos pm        ON pm.id_contrato = c.id_contrato AND pm.periodo = :periodo
    LEFT  JOIN pagos ph        ON ph.id_contrato = c.id_contrato
    WHERE c.estatus = 'Activa'
    GROUP BY c.id_contrato, pm.id_pago
    ORDER BY FIELD(pm.estatus,'Vencido','Pendiente','Pagado',NULL), l.codigo
");
$consulta->execute([':periodo' => $periodo]);
$contratos = $consulta->fetchAll(PDO::FETCH_ASSOC);

/* ─── Estadísticas ─── */
$totalVencidos = $totalPendientes = $totalPagados = 0;
foreach ($contratos as $c) {
    if ($c['estatus_mes'] === 'Vencido')    $totalVencidos++;
    elseif ($c['estatus_mes'] === 'Pagado') $totalPagados++;
    else                                     $totalPendientes++;
}

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/pagos.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
