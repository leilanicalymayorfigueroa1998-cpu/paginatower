<?php

include('../../bd.php');

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_renta = isset($_POST['id_renta']) ? $_POST['id_renta'] : '';
    $fecha_pago = isset($_POST['fecha_pago']) ? $_POST['fecha_pago'] : '';
    $monto = isset($_POST['monto']) ? $_POST['monto'] : '';
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';


    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO pagos (id_pago, id_renta, fecha_pago, monto, estatus) 
                                   VALUES (NULL, :id_renta, :fecha_pago, :monto, :estatus)");


    $consulta->bindParam(':id_renta', $id_renta);
    $consulta->bindParam(':fecha_pago', $fecha_pago);
    $consulta->bindParam(':monto', $monto);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->execute();
    header("Location:index.php");
}

$consultaRentas = $conexionBD->prepare("SELECT r.id_renta, l.codigo, c.nombre
    FROM rentas r
    INNER JOIN locales l ON r.id_local = l.id_local
    INNER JOIN clientes c ON r.id_cliente = c.id_cliente");
$consultaRentas->execute();
$listaRentas = $consultaRentas->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');

?>

<br />
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
                <label class="form-label">Renta</label>
                <select name="id_renta" class="form-control" required>
                    <option value="">-- Selecciona una renta --</option>

                    <?php foreach ($listaRentas as $renta) { ?>
                        <option value="<?php echo $renta['id_renta']; ?>">
                            <?php echo $renta['codigo'] . ' - ' . $renta['nombre']; ?>
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
                <label class="form-label">Estatus</label>
                <select name="estatus" class="form-control" required>
                    <option value="">-- Selecciona estatus --</option>
                    <option value="Pagado">Pagado</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Vencido">Vencido</option>
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

    <div class="card-footer text-muted">


    </div>

</div>

<?php include('../../templates/pie.php'); ?>