<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM pagos WHERE id_pago=:id_pago");
    $consulta->bindParam(':id_pago', $txtID);
    $consulta->execute();

    $pago = $consulta->fetch(PDO::FETCH_LAZY);
    $id_contrato = $pago['id_contrato'];
    $fecha_pago = $pago['fecha_pago'];
    $monto = $pago['monto'];
    $metodo_pago = $pago['metodo_pago'];
    $estatus = $pago['estatus'];
}

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_contrato = isset($_POST['id_contrato']) ? $_POST['id_contrato'] : '';
    $fecha_pago = isset($_POST['fecha_pago']) ? $_POST['fecha_pago'] : '';
    $monto = isset($_POST['monto']) ? $_POST['monto'] : '';
    $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';


    $consulta = $conexionBD->prepare("UPDATE pagos SET
                id_contrato = :id_contrato,
                fecha_pago = :fecha_pago,
                monto = :monto,
                metodo_pago = :metodo_pago,
                estatus = :estatus
                WHERE id_pago = :id_pago");

    $consulta->bindParam(':id_contrato', $id_contrato);
    $consulta->bindParam(':fecha_pago', $fecha_pago);
    $consulta->bindParam(':monto', $monto);
    $consulta->bindParam(':metodo_pago', $metodo_pago);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->bindParam(':id_pago', $txtID);

    $consulta->execute();
    header("Location:index.php");
    exit();
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
        <div class="card-header">Pagos</div>
        <div class="card-body">

            <form action="" method="post">

                <div class="mb-3">
                    <label for="" class="form-label">ID</label>
                    <input
                        type="hidden"
                        class="form-control"
                        name="txtID"
                        id="txtID"
                        value="<?php echo $txtID ?>"
                        aria-describedby="helpId"
                        placeholder="ID" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Contrato</label>
                    <select name="id_contrato" class="form-control" required>
                        <?php foreach ($listaContratos as $contrato) { ?>
                            <option value="<?php echo $contrato['id_contrato']; ?>"
                                <?php echo ($contrato['id_contrato'] == $id_contrato) ? 'selected' : ''; ?>>
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
                        value="<?php echo $fecha_pago ?>" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Monto</label>
                    <input
                        type="text"
                        class="form-control"
                        name="monto"
                        id="monto"
                        value="<?php echo $monto ?>"
                        aria-describedby="helpId"
                        placeholder="Monto" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de pago</label>
                    <select name="metodo_pago" class="form-control" required>
                        <option value="Efectivo" <?php echo ($metodo_pago == 'Efectivo') ? 'selected' : ''; ?>>
                            Efectivo
                        </option>
                        <option value="Transferencia" <?php echo ($metodo_pago == 'Transferencia') ? 'selected' : ''; ?>>
                            Transferencia
                        </option>
                        <option value="Depósito" <?php echo ($metodo_pago == 'Depósito') ? 'selected' : ''; ?>>
                            Depósito
                        </option>
                        <option value="SPEI" <?php echo ($metodo_pago == 'SPEI') ? 'selected' : ''; ?>>
                            SPEI
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="Pagado" <?php echo ($estatus == 'Pagado') ? 'selected' : ''; ?>>
                            Pagado
                        </option>
                        <option value="Pendiente" <?php echo ($estatus == 'Pendiente') ? 'selected' : ''; ?>>
                            Pendiente
                        </option>
                        <option value="Vencido" <?php echo ($estatus == 'Vencido') ? 'selected' : ''; ?>>
                            Vencido
                        </option>
                        <option value="Cancelado" <?php echo ($estatus == 'Cancelado') ? 'selected' : ''; ?>>
                            Cancelado
                        </option>
                    </select>
                </div>


                <button type="submit" name="accion" value="agregar" class="btn btn-success">Modificar</button>
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