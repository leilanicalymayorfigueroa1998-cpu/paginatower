<?php 

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM pagos WHERE id_pago=:id_pago");
    $consulta->bindParam(':id_pago', $txtID);
    $consulta->execute();

   $pago = $consulta->fetch(PDO::FETCH_LAZY);
            $id_renta = $pago['id_renta'];
            $fecha_pago = $pago['fecha_pago'];
            $monto = $pago['monto'];
            $estatus = $pago['estatus'];
}

if ($_POST) {
$txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
$id_renta = isset($_POST['id_renta']) ? $_POST['id_renta'] : '';
$fecha_pago = isset($_POST['fecha_pago']) ? $_POST['fecha_pago'] : '';
$monto = isset($_POST['monto']) ? $_POST['monto'] : '';
$estatus= isset($_POST['estatus']) ? $_POST['estatus'] : '';
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    print_r($_POST);

    $consulta = $conexionBD->prepare("UPDATE pagos SET
                id_renta = :id_renta,
                fecha_pago = :fecha_pago,
                monto = :monto,
                estatus = :estatus
                WHERE id_pago = :id_pago");

            $consulta->bindParam(':id_renta', $id_renta);
            $consulta->bindParam(':fecha_pago', $fecha_pago);
            $consulta->bindParam(':monto', $monto);
            $consulta->bindParam(':estatus', $estatus);
            $consulta->bindParam(':id_pago', $txtID);

    $consulta->execute();
    header("Location:index.php");
}

include('../../templates/cabecera.php'); 

?>

<br />
<div class="card">
    <div class="card-header">Pagos</div>
    <div class="card-body">

        <form action="" method="post">

            <div class="mb-3">
                <label for="" class="form-label">ID</label>
                <input
                    type="text"
                    class="form-control"
                    name="txtID"
                    id="txtID"
                    value="<?php echo $txtID ?>"
                    aria-describedby="helpId"
                    placeholder="ID" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Renta</label>
                <input
                    type="text"
                    class="form-control"
                    name="id_renta"
                    id="id_renta"
                    value="<?php echo $id_renta ?>"
                    aria-describedby="helpId"
                    placeholder="Renta" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Fecha Pago</label>
                <input
                    type="date"
                    class="form-control"
                    name="fecha-pago"
                    id="fecha_pago"
                    value="<?php echo $fecha_pago ?>"
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
                    value="<?php echo $monto ?>"
                    aria-describedby="helpId"
                    placeholder="Monto" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Estatus</label>
                <input
                    type="date"
                    class="form-control"
                    name="estatus"
                    id="estatus"
                    value="<?php echo $estatus ?>"
                    aria-describedby="helpId"
                    placeholder="Estatus" />
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

    <div class="card-footer text-muted">


    </div>

</div>

<?php include('../../templates/pie.php'); ?>
