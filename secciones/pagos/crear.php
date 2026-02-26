<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

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
        <div class="card-header">Nueva Renta</div>
        <div class="card-body">

            <form action="guardar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <div class="mb-3">
                    <label class="form-label">Contrato</label>
                    <select name="id_contrato" class="form-control" required>
                        <option value="">-- Selecciona un contrato --</option>
                        <?php foreach ($listaContratos as $contrato) { ?>
                            <option value="<?= $contrato['id_contrato']; ?>">
                                <?= $contrato['codigo'] . ' - ' . $contrato['nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de Pago</label>
                    <input type="date" class="form-control" name="fecha_pago" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Monto</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="monto" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago</label>
                    <select name="metodo_pago" class="form-control" required>
                        <option value="">-- Selecciona método --</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Deposito">Depósito</option>
                        <option value="SPEI">SPEI</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="">-- Selecciona estatus --</option>
                        <option value="Pagado">Pagado</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Vencido">Vencido</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar Pago</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>

            </form>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>