<?php

include('../../bd.php');

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_renta = isset($_POST['id_renta']) ? $_POST['id_renta'] : '';
    $fecha_pago = isset($_POST['fecha_pago']) ? $_POST['fecha_pago'] : '';
    $monto = isset($_POST['monto']) ? $_POST['monto'] : '';
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';
   

    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO `pagos` (`id_pago`, `id_renta`, `fecha_pago`, `monto`, `estatus`) 
                                   VALUES (NULL, :id_renta, :fecha_pago, :monto, :estatus)");

    $consulta->bindParam(':id_renta', $id_renta);
    $consulta->bindParam(':fecha_pago', $fecha_pago);
    $consulta->bindParam(':monto', $monto);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->execute();

    header("Location:index.php");
}

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
                <label for="" class="form-label">Renta</label>
                <input
                    type="text"
                    class="form-control"
                    name="id_renta"
                    id="id_renta"
                    aria-describedby="helpId"
                    placeholder="Renta" />
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
                <label for="" class="form-label">Estatus</label>
                <input
                    type="date"
                    class="form-control"
                    name="estatus"
                    id="estatus"
                    aria-describedby="helpId"
                    placeholder="Estatus" />
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