<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM servicios WHERE id_servicio=:id_servicio");
    $consulta->bindParam(':id_servicio', $txtID);
    $consulta->execute();

    $serv = $consulta->fetch(PDO::FETCH_LAZY);
    $id_servicio = $serv['id_servicio'];
    $id_local = $serv['id_local'];
    $cfe = $serv['cfe'];
    $agua = $serv['agua'];
}


if ($_POST) {

    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_local = isset($_POST['id_local']) ? $_POST['id_local'] : '';
    $cfe = isset($_POST['cfe']) ? $_POST['cfe'] : '';
    $agua = isset($_POST['agua']) ? $_POST['agua'] : '';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    print_r($_POST);

    $consulta = $conexionBD->prepare("UPDATE servicios
        SET id_local = :id_local,
        cfe = :cfe,
        agua = :agua
                WHERE id_servicio=:id_servicio");

    $consulta->bindParam(':id_local', $id_local);
    $consulta->bindParam(':cfe', $cfe);
    $consulta->bindParam(':agua', $agua);
    $consulta->execute();
    header("Location:index.php");
}


include('../../templates/cabecera.php'); ?>


<div class="card">
    <div class="card-header">Dueños</div>
    <div class="card-body">

        <form action="" method="post">

            <div class="mb-3">
                <label for="" class="form-label">ID</label>
                <input
                    type="text"
                    class="form-control"
                    value="<?php echo $txtID; ?>"
                    name="txtID"
                    id="txtID"
                    aria-describedby="helpId"
                    placeholder="ID" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Local</label>
                <input
                    type="text"
                    class="form-control"
                    value="<?php echo $id_local ?>"
                    name="id_local"
                    id="id_local"
                    aria-describedby="helpId"
                    placeholder="Local" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">CFE</label>
                <input
                    type="text"
                    class="form-control"
                    name="cfe"
                    id="cfe"
                    value="<?php echo $cfe ?>"
                    aria-describedby="helpId"
                    placeholder="CFE" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Agua</label>
                <input
                    type="text"
                    class="form-control"
                    name="agua"
                    id="agua"
                    value="<?php echo $agua ?>"
                    aria-describedby="helpId"
                    placeholder="Agua" />
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