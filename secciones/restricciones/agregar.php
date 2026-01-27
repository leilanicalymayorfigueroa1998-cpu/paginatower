<?php

include('../../bd.php');

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_local = isset($_POST['id_local']) ? $_POST['id_local'] : '';
    $restricciones = isset($_POST['restricciones']) ? $_POST['restricciones'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO restricciones (id_restriccion, id_local, restricciones) 
                  VALUES (NULL, :id_local, :restricciones )");

    $consulta->bindParam(':id_local', $id_local);
    $consulta->bindParam(':restricciones', $restricciones);
    $consulta->execute();
    header("Location:index.php");
}

include('../../templates/cabecera.php'); ?>


<div class="card">
    <div class="card-header">Restricciones</div>
    <div class="card-body">

        <form action="" method="post">

            <div class="mb-3">
                <label for="" class="form-label">ID</label>
                <input
                    type="text"
                    class="form-control"
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
                    name="local"
                    id="local"
                    aria-describedby="helpId"
                    placeholder="Local" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Restricciones</label>
                <input
                    type="text"
                    class="form-control"
                    name="restricciones"
                    id="restricciones"
                    aria-describedby="helpId"
                    placeholder="Restricciones" />
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