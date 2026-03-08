<?php
/* ════════════════════════════════════════════
   DASHBOARD — index.php
   Solo lógica PHP / controlador
   CSS → assets/css/dashboard.css   (ya existe)
   JS  → assets/js/dashboard_mod.js
════════════════════════════════════════════ */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'dashboard', 'ver');

$hoy    = date('Y-m-d');
$periodo = date('Y-m-01');

/* ── Contratos ── */
$totalContratos = $conexionBD->query("SELECT COUNT(*) FROM contratos")->fetchColumn();
$activos        = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus='Activa'")->fetchColumn();
$vencidos       = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus='Activa' AND fecha_fin IS NOT NULL AND fecha_fin < CURDATE()")->fetchColumn();
$porVencer      = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus='Activa' AND fecha_fin IS NOT NULL AND fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
$indefinidos    = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus='Activa' AND duracion='Indefinido'")->fetchColumn();
$finalizados    = $conexionBD->query("SELECT COUNT(*) FROM contratos WHERE estatus IN ('Finalizada','Cancelada')")->fetchColumn();

/* ── Pagos del mes ── */
$pagosMes = $conexionBD->prepare("
    SELECT
        SUM(CASE WHEN estatus='Pagado'    THEN monto ELSE 0 END) AS cobrado,
        SUM(CASE WHEN estatus='Pendiente' THEN monto ELSE 0 END) AS pendiente,
        SUM(CASE WHEN estatus='Vencido'   THEN monto ELSE 0 END) AS vencido,
        COUNT(CASE WHEN estatus='Pagado'    THEN 1 END) AS n_pagados,
        COUNT(CASE WHEN estatus='Pendiente' THEN 1 END) AS n_pendientes,
        COUNT(CASE WHEN estatus='Vencido'   THEN 1 END) AS n_vencidos
    FROM pagos WHERE periodo = :p
");
$pagosMes->execute([':p' => $periodo]);
$pm = $pagosMes->fetch(PDO::FETCH_ASSOC);

/* ── Balance financiero del mes ── */
$balance = $conexionBD->prepare("
    SELECT SUM(abono) AS ingresos, SUM(cargo) AS egresos, SUM(abono) - SUM(cargo) AS neto
    FROM movimientos_financieros WHERE fecha BETWEEN :inicio AND :fin
");
$balance->execute([':inicio' => $periodo, ':fin' => $hoy]);
$bal = $balance->fetch(PDO::FETCH_ASSOC);

/* ── Renta potencial mensual ── */
$rentaPotencial = $conexionBD->query("SELECT SUM(renta) FROM contratos WHERE estatus='Activa'")->fetchColumn();

/* ── Contratos por vencer en 60 días ── */
$proximosVencer = $conexionBD->query("
    SELECT l.codigo AS local, a.nombre AS arrendatario,
           c.fecha_fin, c.renta,
           DATEDIFF(c.fecha_fin, CURDATE()) AS dias_restantes
    FROM contratos c
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    WHERE c.estatus = 'Activa' AND c.fecha_fin IS NOT NULL
    AND c.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
    ORDER BY c.fecha_fin ASC LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

/* ── Pagos pendientes/vencidos del mes ── */
$pagosPendientes = $conexionBD->prepare("
    SELECT l.codigo AS local, a.nombre AS arrendatario,
           p.monto, p.estatus, p.fecha_pago, c.dia_pago
    FROM pagos p
    INNER JOIN contratos c     ON p.id_contrato     = c.id_contrato
    INNER JOIN locales l       ON c.id_local        = l.id_local
    INNER JOIN arrendatarios a ON c.id_arrendatario = a.id_arrendatario
    WHERE p.periodo = :periodo AND p.estatus IN ('Pendiente','Vencido')
    ORDER BY p.estatus DESC, l.codigo ASC LIMIT 10
");
$pagosPendientes->execute([':periodo' => $periodo]);
$pendientes = $pagosPendientes->fetchAll(PDO::FETCH_ASSOC);

/* ── Últimos 6 meses ── */
$meses6 = [];
for ($i = 5; $i >= 0; $i--) {
    $ts  = strtotime("-$i month", strtotime($periodo));
    $ini = date('Y-m-01', $ts);
    $fin = date('Y-m-t', $ts);
    $ing = $conexionBD->prepare("SELECT COALESCE(SUM(abono),0) FROM movimientos_financieros WHERE fecha BETWEEN :i AND :f");
    $ing->execute([':i' => $ini, ':f' => $fin]);
    $eg  = $conexionBD->prepare("SELECT COALESCE(SUM(cargo),0) FROM movimientos_financieros WHERE fecha BETWEEN :i AND :f");
    $eg->execute([':i' => $ini, ':f' => $fin]);
    $meses6[] = ['mes' => date('M', $ts), 'ingresos' => (float)$ing->fetchColumn(), 'egresos' => (float)$eg->fetchColumn()];
}

/* ── KPIs calculados ── */
$pctCobrado   = $rentaPotencial > 0 ? round(($pm['cobrado'] / $rentaPotencial) * 100) : 0;
$neto         = (float)($bal['neto'] ?? 0);
$totalMes     = ($pm['cobrado'] ?? 0) + ($pm['pendiente'] ?? 0) + ($pm['vencido'] ?? 0);
$pctMorosidad = $totalMes > 0 ? round((($pm['pendiente'] + $pm['vencido']) / $totalMes) * 100) : 0;
$pctOcupacion = $totalContratos > 0 ? round(($activos / $totalContratos) * 100) : 0;

/* ── Datos gráfica para JS ── */
$chartLabels   = array_column($meses6, 'mes');
$chartIngresos = array_column($meses6, 'ingresos');
$chartEgresos  = array_column($meses6, 'egresos');

/* ─── CSS extra de esta sección ─── */
$pagina_css = ['assets/css/dashboard.css'];

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
include('view.php');
include('../../templates/pie.php');
