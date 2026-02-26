<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

$id_pago = 0;

if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {

    $id_pago = intval($_GET['txtID']);

    $consulta = $conexionBD->prepare("SELECT * FROM pagos WHERE id_pago = :id");
    $consulta->bindParam(':id', $id_pago);
    $consulta->execute();

    $pago = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$pago) {
        die("Pago no encontrado.");
    }

    $id_contrato = $pago['id_contrato'];
    $fecha_pago  = $pago['fecha_pago'];
    $monto       = $pago['monto'];
    $metodo_pago = $pago['metodo_pago'];
    $estatus     = $pago['estatus'];
}

$consultaContrato = $conexionBD->prepare("SELECT r.id_contrato, l.codigo, c.nombre
    FROM contratos r
    INNER JOIN locales l ON r.id_local = l.id_local
    INNER JOIN arrendatarios c ON r.id_arrendatario = c.id_arrendatario");
$consultaContrato->execute();
$listaContratos = $consultaContrato->fetchAll(PDO::FETCH_ASSOC);


include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');

?>

<div class="content">

    <div class="card">
        <div class="card-header">Pagos</div>
        <div class="card-body">

            <form action="actualizar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= $id_pago ?>">

                <div class="mb-3">
                    <label class="form-label">Contrato</label>
                    <select name="id_contrato" class="form-control" required>
                        <?php foreach ($listaContratos as $contrato) { ?>
                            <option value="<?= $contrato['id_contrato']; ?>"
                                <?= ($contrato['id_contrato'] == $id_contrato) ? 'selected' : ''; ?>>
                                <?= $contrato['codigo'] . ' - ' . $contrato['nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha Pago</label>
                    <input type="date"
                        class="form-control"
                        name="fecha_pago"
                        value="<?= htmlspecialchars($fecha_pago) ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Monto</label>
                    <input type="number"
                        step="0.01"
                        min="0"
                        class="form-control"
                        name="monto"
                        value="<?= htmlspecialchars($monto) ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de pago</label>
                    <select name="metodo_pago" class="form-control" required>
                        <option value="Efectivo" <?= ($metodo_pago == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
                        <option value="Transferencia" <?= ($metodo_pago == 'Transferencia') ? 'selected' : ''; ?>>Transferencia</option>
                        <option value="Depósito" <?= ($metodo_pago == 'Depósito') ? 'selected' : ''; ?>>Depósito</option>
                        <option value="SPEI" <?= ($metodo_pago == 'SPEI') ? 'selected' : ''; ?>>SPEI</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="Pagado" <?= ($estatus == 'Pagado') ? 'selected' : ''; ?>>Pagado</option>
                        <option value="Pendiente" <?= ($estatus == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="Vencido" <?= ($estatus == 'Vencido') ? 'selected' : ''; ?>>Vencido</option>
                        <option value="Cancelado" <?= ($estatus == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning">Actualizar Pago</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>

            </form>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>