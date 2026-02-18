<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_contrato = isset($_POST['id_contrato']) ? $_POST['id_contrato'] : '';
    $fecha_pago = isset($_POST['fecha_pago']) ? $_POST['fecha_pago'] : '';
    $monto = isset($_POST['monto']) ? $_POST['monto'] : '';
    $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';


    $consulta = $conexionBD->prepare("INSERT INTO pagos (id_pago, id_contrato, fecha_pago, monto, metodo_pago, estatus) 
                                   VALUES (NULL, :id_contrato, :fecha_pago, :monto, :metodo_pago, :estatus)");


    $consulta->bindParam(':id_contrato', $id_contrato);
    $consulta->bindParam(':fecha_pago', $fecha_pago);
    $consulta->bindParam(':monto', $monto);
    $consulta->bindParam(':metodo_pago', $metodo_pago);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->execute();
    header("Location:index.php");
}

$consultaContrato = $conexionBD->prepare("SELECT r.id_contrato, l.codigo, c.nombre
    FROM contratos r
    INNER JOIN locales l ON r.id_local = l.id_local
    INNER JOIN clientes c ON r.id_cliente = c.id_cliente");

$consultaContrato->execute();
$listaContratos = $consultaContrato->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');

?>

<div class="main-content">

    <div class="card">
        <div class="card-header">Rentas</div>
        <div class="card-body">

            <form action="" method="post">

                <div class="mb-3">
                    <label for="" class="form-label">Pago</label>
                    <input
                        type="text"
                        class="form-control"
                        name="txtID"
                        id="txtID"
                        aria-describedby="helpId"
                        placeholder="ID" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Contrato</label>
                    <select name="id_contrato" class="form-control" required>
                        <option value="">-- Selecciona un contrato --</option>

                        <?php foreach ($listaContratos as $contrato) { ?>
                            <option value="<?php echo $contrato['id_contrato']; ?>">
                                <?php echo $contrato['codigo'] . ' - ' . $contrato['nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Fecha Pago</label>
                    <input
                        type="date"
                        class="form-control"
                        name="fecha_pago"
                        id="fecha_pago"
                        aria-describedby="helpId"
                        placeholder="Fecha Pago" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Monto</label>
                    <input
                        type="text"
                        class="form-control"
                        name="monto"
                        id="monto"
                        aria-describedby="helpId"
                        placeholder="monto" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de pago</label>
                    <select name="metodo_pago" class="form-control" required>
                        <option value="">-- Selecciona método --</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Depósito">Depósito</option>
                        <option value="Depósito">SPEI</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="">-- Selecciona estatus --</option>
                        <option value="Pagado">Pagado</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Vencido">Vencido</option>
                        <option value="Vencido">Cancelado</option>
                    </select>
                </div>

                <button type="submit" name="accion" value="agregar" class="btn btn-success">Agregar</button>
                <a
                    name=""
                    id=""
                    class="btn btn-primary"
                    href="index.php"
                    role="button">Cancelar</a>


            </form>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>