<?php
include('../../bd.php');
include('../../templates/cabecera.php');

// 1️⃣ Verificar que sea cliente
if ($_SESSION['rol'] != 'arrendatarios') {
    header("Location: ../../index.php");
    exit();
}

// 2️⃣ Obtener id_cliente del usuario logueado
$idUsuario = $_SESSION['id'];

$consultaUsuario = $conexionBD->prepare("SELECT id_arrendatario FROM usuarios WHERE id = :id");
$consultaUsuario->bindParam(':id', $idUsuario);
$consultaUsuario->execute();

$usuario = $consultaUsuario->fetch(PDO::FETCH_ASSOC);
$idArrendatario = $usuario['id_arrendatario'] ?? null;

// 🔐 Validación extra
if (!$idArrendatario) {
    echo "<div class='alert alert-warning'>No tienes cliente asignado.</div>";
    include('../../templates/pie.php');
    exit();
}

// 3️⃣ Obtener renta activa
$consultaRenta = $conexionBD->prepare("SELECT * FROM rentas WHERE id_arrendatario = :id_arrendatario AND estatus = 'Activa'");
$consultaRenta->bindParam(':id_arrendatario', $idCliente);
$consultaRenta->execute();

$renta = $consultaRenta->fetch(PDO::FETCH_ASSOC);
?>

<?php if ($renta): ?>

<?php
// 4️⃣ Obtener pagos
$consultaPagos = $conexionBD->prepare("SELECT * FROM pagos WHERE id_renta = :id_renta ORDER BY fecha_pago DESC");
$consultaPagos->bindParam(':id_renta', $renta['id_renta']);
$consultaPagos->execute();
$pagos = $consultaPagos->fetchAll(PDO::FETCH_ASSOC);

// 5️⃣ Cálculo mensual con día fijo
$hoy = new DateTime();
$inicio = new DateTime($renta['fecha_inicio']);

$diff = $inicio->diff($hoy);
$meses = ($diff->y * 12) + $diff->m;

if ($hoy->format('d') >= $inicio->format('d')) {
    $meses++;
}

// Próximo vencimiento real
$proximoVencimiento = clone $inicio;
$proximoVencimiento->modify("+$meses month");

// 6️⃣ Total que debería pagar
$totalDeberia = $meses * $renta['renta'];

// 7️⃣ Total pagado
$totalPagado = 0;
foreach ($pagos as $pago) {
    $totalPagado += $pago['monto'];
}

// 8️⃣ Saldo real
$saldo = $totalDeberia - $totalPagado;

// 9️⃣ Porcentaje de pago
$porcentaje = 0;
if ($totalDeberia > 0) {
    $porcentaje = ($totalPagado / $totalDeberia) * 100;
}
if ($porcentaje > 100) {
    $porcentaje = 100;
}
?>

<div class="card <?php echo ($saldo > 0) ? 'border-danger' : 'border-success'; ?>">
    <div class="card-header">Mi Estado de Cuenta</div>
    <div class="card-body">

        <p><strong>Renta mensual:</strong> $<?php echo number_format($renta['renta'],2); ?></p>
        <p><strong>Fecha inicio:</strong> <?php echo $renta['fecha_inicio']; ?></p>
        <p><strong>Fecha fin:</strong> <?php echo $renta['fecha_fin']; ?></p>
        <p><strong>Próximo vencimiento:</strong> 
            <?php echo $proximoVencimiento->format('d-m-Y'); ?>
        </p>

        <?php
        if ($hoy->format('Y-m-d') == $proximoVencimiento->format('Y-m-d')) {
            echo '<div class="alert alert-danger mt-2">
                    ⚠️ Tu pago vence hoy
                  </div>';
        }
        ?>

        <hr>

        <p><strong>Total que deberías pagar:</strong> $<?php echo number_format($totalDeberia,2); ?></p>
        <p><strong>Total pagado:</strong> $<?php echo number_format($totalPagado,2); ?></p>

        <!-- Barra de progreso -->
        <div class="progress mb-3" style="height: 20px;">
            <div class="progress-bar 
                <?php echo ($saldo > 0) ? 'bg-danger' : 'bg-success'; ?>"
                role="progressbar"
                style="width: <?php echo $porcentaje; ?>%;">
                <?php echo round($porcentaje); ?>%
            </div>
        </div>

        <?php if ($saldo > 0): ?>
            <p class="text-danger"><strong>Saldo pendiente:</strong> $<?php echo number_format($saldo,2); ?></p>
        <?php elseif ($saldo == 0): ?>
            <p class="text-success"><strong>Estás al corriente</strong></p>
        <?php else: ?>
            <p class="text-primary"><strong>Tienes saldo a favor:</strong> $<?php echo number_format(abs($saldo),2); ?></p>
        <?php endif; ?>

        <hr>
        <h5>Historial de Pagos</h5>

        <?php if (count($pagos) > 0): ?>

        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?php echo $pago['fecha_pago']; ?></td>
                        <td>$<?php echo number_format($pago['monto'],2); ?></td>
                        <td>
                            <?php if ($pago['estatus'] == 'Pagado'): ?>
                                <span class="badge bg-success">Pagado</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php else: ?>
            <p>No hay pagos registrados.</p>
        <?php endif; ?>

    </div>
</div>

<?php else: ?>
    <div class="alert alert-info">
        No tienes renta activa.
    </div>
<?php endif; ?>

<?php include('../../templates/pie.php'); ?>
